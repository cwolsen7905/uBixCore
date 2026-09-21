<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Payment;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface as Client;
use Stripe\HttpClient\CurlClient;
use Stripe\StripeClient;
use Ubix\DataTransferObject\Payment\OneOffCheckoutRequest;
use Ubix\DataTransferObject\Payment\SubscriptionCheckoutRequest;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Payment\StripePaymentProviderService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Payment\StripePaymentProviderService
 *
 * Signature verification is real cryptography and runs entirely locally, so it
 * is tested for real rather than mocked — it is the security control this class
 * exists to enforce, and a mocked signature check asserts nothing. The calls
 * that would reach Stripe's API are driven through the SDK's own HTTP client
 * seam with canned responses; a live-key integration run belongs to the
 * environment described in the payments TDS, not to this suite.
 *
 * @coversDefaultClass \Ubix\Service\Payment\StripePaymentProviderService
 */
final class StripePaymentProviderServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * The signing secret these cases sign and verify with
     */
    private const WEBHOOK_SECRET = 'whsec_test_secret_value';

    /**
     * The parameters the SDK last sent, captured by the canned HTTP client
     *
     * @var array<string, mixed>
     */
    private array $sentParameters = [];

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(StripePaymentProviderService::class);
    }

    /**
     * A correctly signed payload verifies and becomes a VerifiedWebhookEvent
     *
     * @return void
     */
    public function testACorrectlySignedWebhookVerifies(): void
    {
        putenv('STRIPE_WEBHOOK_SECRET=' . self::WEBHOOK_SECRET);

        $payload = '{"id":"evt_123","type":"invoice.paid","created":1758300000,"data":{"object":{"id":"in_1"}}}';
        $event   = $this->provider()->verifyWebhook($payload, $this->signatureFor($payload));

        $this->assertSame('evt_123', $event->providerEventId);
        $this->assertSame('invoice.paid', $event->type);
        $this->assertSame(1758300000, $event->createdAtUnixTimestamp);
        $this->assertArrayHasKey('data', $event->payload);
    }

    /**
     * A payload altered after signing is rejected
     *
     * The whole point: the bytes are signed, so changing one of them breaks it.
     *
     * @return void
     */
    public function testATamperedPayloadIsRejected(): void
    {
        putenv('STRIPE_WEBHOOK_SECRET=' . self::WEBHOOK_SECRET);

        $signed   = '{"id":"evt_123","type":"invoice.paid","created":1758300000}';
        $tampered = '{"id":"evt_666","type":"invoice.paid","created":1758300000}';

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::PAYMENT_WEBHOOK_SIGNATURE_INVALID->value);

        $this->provider()->verifyWebhook($tampered, $this->signatureFor($signed));
    }

    /**
     * A signature made with a different secret is rejected
     *
     * @return void
     */
    public function testASignatureFromTheWrongSecretIsRejected(): void
    {
        putenv('STRIPE_WEBHOOK_SECRET=' . self::WEBHOOK_SECRET);

        $payload = '{"id":"evt_123","type":"invoice.paid","created":1758300000}';

        $this->expectException(DtoException::class);
        $this->provider()->verifyWebhook($payload, $this->signatureFor($payload, 'whsec_a_different_secret'));
    }

    /**
     * A missing signature header is rejected rather than waved through
     *
     * @return void
     */
    public function testAMissingSignatureHeaderIsRejected(): void
    {
        putenv('STRIPE_WEBHOOK_SECRET=' . self::WEBHOOK_SECRET);

        $this->expectException(DtoException::class);
        $this->provider()->verifyWebhook('{"id":"evt_123"}', '');
    }

    /**
     * With no signing secret configured, verification fails closed
     *
     * The dangerous alternative is skipping the check, which turns a public URL
     * into an open door to the ledger.
     *
     * @return void
     */
    public function testVerificationFailsClosedWhenNoSecretIsConfigured(): void
    {
        putenv('STRIPE_WEBHOOK_SECRET');

        $payload = '{"id":"evt_123"}';

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::PAYMENT_WEBHOOK_SIGNATURE_INVALID->value);

        $this->provider()->verifyWebhook($payload, $this->signatureFor($payload));
    }

    /**
     * A subscription checkout stamps metadata on the subscription, not just the session
     *
     * A Checkout Session's metadata does not propagate to the subscription
     * Stripe creates from it, and every later invoice event names the
     * subscription rather than the session. Without `subscription_data`, the
     * recurring charges could not be traced back to the host's records.
     *
     * @return void
     */
    public function testASubscriptionCheckoutStampsMetadataOnTheSubscription(): void
    {
        $this->cannedHttpClient([
            'expires_at' => 1758300000,
            'id'         => 'cs_test_1',
            'object'     => 'checkout.session',
            'url'        => 'https://checkout.stripe.com/c/pay/cs_test_1',
        ]);

        $session = $this->provider()->createSubscriptionCheckout(new SubscriptionCheckoutRequest(
            amountMinorUnits: 2500,
            currency:         'usd',
            intervalMonths:   1,
            description:      'Gold tier',
            successUrl:       'https://sowing.me/ok',
            cancelUrl:        'https://sowing.me/no',
            clientReference:  'kitg-7-3',
            metadata:         ['creatorId' => '3', 'userId' => '7'],
        ));

        $this->assertSame('https://checkout.stripe.com/c/pay/cs_test_1', $session->url);
        $this->assertSame('cs_test_1', $session->providerSessionId);

        $params = $this->sentParameters;
        $this->assertSame('subscription', $params['mode'] ?? null);

        $subscriptionData = $params['subscription_data'] ?? null;
        $this->assertIsArray($subscriptionData);
        $this->assertSame(['creatorId' => '3', 'userId' => '7'], $subscriptionData['metadata'] ?? null);

        $priceData = $this->capturedPriceData();
        $recurring = $priceData['recurring'] ?? null;
        $this->assertIsArray($recurring);
        $this->assertSame('month', $recurring['interval'] ?? null);
    }

    /**
     * A one-off checkout carries no recurring block and no subscription_data
     *
     * @return void
     */
    public function testAOneOffCheckoutHasNoRecurringBlock(): void
    {
        $this->cannedHttpClient([
            'expires_at' => 1758300000,
            'id'         => 'cs_test_2',
            'object'     => 'checkout.session',
            'url'        => 'https://checkout.stripe.com/c/pay/cs_test_2',
        ]);

        $this->provider()->createOneOffCheckout(new OneOffCheckoutRequest(
            amountMinorUnits: 500,
            currency:         'USD',
            description:      'A tip',
            successUrl:       'https://sowing.me/ok',
            cancelUrl:        'https://sowing.me/no',
            clientReference:  'tip-1',
        ));

        $params = $this->sentParameters;
        $this->assertSame('payment', $params['mode'] ?? null);
        $this->assertArrayNotHasKey('subscription_data', $params);

        $priceData = $this->capturedPriceData();
        $this->assertArrayNotHasKey('recurring', $priceData);
        // Lower-cased on the way out: Stripe rejects an upper-case currency.
        $this->assertSame('usd', $priceData['currency'] ?? null);
    }

    /**
     * A non-positive amount is refused before anything reaches the network
     *
     * @return void
     */
    public function testANonPositiveCheckoutAmountIsRefused(): void
    {
        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value);

        $this->provider()->createOneOffCheckout(new OneOffCheckoutRequest(
            amountMinorUnits: 0,
            currency:         'usd',
            successUrl:       'https://sowing.me/ok',
            cancelUrl:        'https://sowing.me/no',
        ));
    }

    /**
     * A non-positive refund amount is refused before anything reaches the network
     *
     * @return void
     */
    public function testANonPositiveRefundAmountIsRefused(): void
    {
        $this->expectException(DtoException::class);
        $this->provider()->refundPayment('pi_1', 0);
    }

    /**
     * With no secret key configured, a networked call fails rather than guessing
     *
     * @return void
     */
    public function testANetworkedCallWithoutAKeyFails(): void
    {
        putenv('STRIPE_SECRET_KEY');

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value);

        // Amount and currency are valid, so this gets past validation and fails
        // where it should: building a client with no credential.
        (new StripePaymentProviderService($this->createStub(Logger::class)))->createOneOffCheckout(
            new OneOffCheckoutRequest(amountMinorUnits: 500, currency: 'usd'),
        );
    }

    /**
     * Restore global SDK and environment state between cases
     *
     * `ApiRequestor::setHttpClient()` is global, so a canned client left in
     * place would leak into every later test in the suite.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Restored to the SDK's real default rather than null: the setter is
        // not nullable, and leaving a canned client installed would leak into
        // every later test in the suite. Constructed rather than taken from
        // CurlClient::instance(), which the SDK declares no return type for.
        ApiRequestor::setHttpClient(new CurlClient());
        putenv('STRIPE_WEBHOOK_SECRET');
        putenv('STRIPE_SECRET_KEY');

        parent::tearDown();
    }

    /**
     * A provider with an injected, keyed client
     *
     * @return StripePaymentProviderService The provider
     */
    private function provider(): StripePaymentProviderService
    {
        return new StripePaymentProviderService(
            $this->createStub(Logger::class),
            new StripeClient('sk_test_not_a_real_key'),
        );
    }

    /**
     * Install an SDK HTTP client that answers every request with $body
     *
     * A PHPUnit double rather than a hand-written class: the SDK's client
     * interface takes seven parameters, only one of which these cases care
     * about, and a variadic callback keeps the five unused ones out of the
     * source entirely.
     *
     * @param array<string, mixed> $body The canned response body
     *
     * @return void
     */
    private function cannedHttpClient(array $body): void
    {
        $json = json_encode($body); // phpcs:ignore Generic.PHP.ForbiddenFunctions -- building a canned HTTP fixture, not application output

        $client = $this->createMock(Client::class);
        $client->method('request')->willReturnCallback(
            function (mixed ...$arguments) use ($json): array {
                $params               = $arguments[3] ?? [];
                $this->sentParameters = is_array($params) ? $this->byName($params) : [];

                return [(string) $json, 200, []];
            },
        );

        ApiRequestor::setHttpClient($client);
    }

    /**
     * The first line item's `price_data`, narrowed to an array
     *
     * @return array<string, mixed> The price data
     */
    private function capturedPriceData(): array
    {
        $lineItems = $this->sentParameters['line_items'] ?? null;
        $this->assertIsArray($lineItems);

        $first = $lineItems[0] ?? null;
        $this->assertIsArray($first);

        $priceData = $first['price_data'] ?? null;
        $this->assertIsArray($priceData);

        return $this->byName($priceData);
    }

    /**
     * Rekey an array by string keys
     *
     * @param array<mixed> $values The array to rekey
     *
     * @return array<string, mixed> The rekeyed array
     */
    private function byName(array $values): array
    {
        $byName = [];
        foreach ($values as $key => $value) {
            $byName[(string) $key] = $value;
        }

        return $byName;
    }

    /**
     * A valid `Stripe-Signature` header for a payload
     *
     * Built the way Stripe builds it — `t=<timestamp>,v1=<hmac>` over
     * `<timestamp>.<payload>` — so the verification under test is the real one.
     *
     * @param string  $payload The exact bytes being signed
     * @param ?string $secret  The signing secret, defaulting to this case's own
     *
     * @return string The header value
     */
    private function signatureFor(string $payload, ?string $secret = null): string
    {
        $timestamp = (new DateTime())->getTimestamp();
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret ?? self::WEBHOOK_SECRET);

        return 't=' . $timestamp . ',v1=' . $signature;
    }
}
