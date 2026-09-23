<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Payment;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface as Client;
use Stripe\HttpClient\CurlClient;
use Stripe\StripeClient;
use Ubix\DataTransferObject\Payment\ConnectedAccountRequest;
use Ubix\DataTransferObject\Payment\CustomerRequest;
use Ubix\DataTransferObject\Payment\OneOffCheckoutRequest;
use Ubix\DataTransferObject\Payment\PaymentIntentRequest;
use Ubix\DataTransferObject\Payment\PendingSubscriptionRequest;
use Ubix\DataTransferObject\Payment\RecurringPriceRequest;
use Ubix\DataTransferObject\Payment\SubscriptionCheckoutRequest;
use Ubix\DataTransferObject\Payment\TransferRequest;
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
     * Method and path of every request the fake client saw, in order
     *
     * @var array<int, string>
     */
    private array $requestLines = [];

    /**
     * The headers the SDK last sent, captured by the canned HTTP client
     *
     * @var array<int, string>
     */
    private array $sentHeaders = [];

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
     * The settling payment's intent is returned, skipping failed attempts
     *
     * A refund names the payment intent, and a Basil invoice carries none —
     * so this is the only way to link a refund back to a subscription charge.
     *
     * @return void
     */
    public function testTheSettlingPaymentIntentIsReturnedForAnInvoice(): void
    {
        $this->cannedHttpClient([
            'data'   => [
                ['object' => 'invoice_payment', 'status' => 'canceled', 'payment' => ['type' => 'payment_intent', 'payment_intent' => 'pi_failed_attempt']],
                ['object' => 'invoice_payment', 'status' => 'paid', 'payment' => ['type' => 'payment_intent', 'payment_intent' => 'pi_settled']],
            ],
            'object' => 'list',
        ]);

        $this->assertSame('pi_settled', $this->provider()->getPaymentReferenceForInvoice('in_1'));
    }

    /**
     * An invoice settled without a card payment yields null, not an error
     *
     * A zero-amount invoice, or one paid from credit balance, has no payment
     * to name — and nothing to refund.
     *
     * @return void
     */
    public function testAnInvoiceWithNoCardPaymentYieldsNull(): void
    {
        $this->cannedHttpClient(['object' => 'list', 'data' => []]);

        $this->assertNull($this->provider()->getPaymentReferenceForInvoice('in_zero'));
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
     * A pending subscription waits for its first payment and hands back the invoice's confirmation secret
     *
     * @return void
     */
    public function testAPendingSubscriptionReturnsTheFirstInvoicesSecret(): void
    {
        $this->cannedHttpClient([
            'id'             => 'sub_1',
            'latest_invoice' => ['confirmation_secret' => ['client_secret' => 'pi_1_secret_x', 'type' => 'payment_intent'], 'id' => 'in_1', 'object' => 'invoice'],
            'object'         => 'subscription',
        ]);

        $pending = $this->provider()->createPendingSubscription(new PendingSubscriptionRequest(
            customerReference: 'cus_1',
            priceReference:    'price_1',
            metadata:          ['creatorId' => '3', 'userId' => '7'],
        ));

        $this->assertSame('sub_1', $pending->providerReference);
        $this->assertSame('pi_1_secret_x', $pending->clientSecret);
        $this->assertSame('default_incomplete', $this->sentParameters['payment_behavior'] ?? null);
        $this->assertSame(['latest_invoice.confirmation_secret'], $this->sentParameters['expand'] ?? null);
        $this->assertSame(['creatorId' => '3', 'userId' => '7'], $this->sentParameters['metadata'] ?? null);
    }

    /**
     * No confirmation secret means the page could not take payment: refuse loudly
     *
     * @return void
     */
    public function testAPendingSubscriptionWithoutASecretFails(): void
    {
        $this->cannedHttpClient(['id' => 'sub_2', 'latest_invoice' => ['id' => 'in_2', 'object' => 'invoice'], 'object' => 'subscription']);

        $this->expectException(DtoException::class);
        $this->provider()->createPendingSubscription(new PendingSubscriptionRequest(customerReference: 'cus_1', priceReference: 'price_1'));
    }

    /**
     * A yearly price is billed month x 12, exactly as the Checkout path bills it
     *
     * @return void
     */
    public function testARecurringPriceUsesMonthTimesN(): void
    {
        $this->cannedHttpClient(['id' => 'price_9', 'object' => 'price']);

        $id = $this->provider()->createRecurringPrice(new RecurringPriceRequest(amountMinorUnits: 12000, currency: 'USD', intervalMonths: 12, productName: 'Gold (yearly)'));

        $this->assertSame('price_9', $id);
        $this->assertSame(['interval' => 'month', 'interval_count' => 12], $this->byName((array) ($this->sentParameters['recurring'] ?? [])));
        $this->assertSame('usd', $this->sentParameters['currency'] ?? null);
    }

    /**
     * A one-off payment intent enables in-page payment methods and returns its secret
     *
     * @return void
     */
    public function testAPaymentIntentReturnsItsSecret(): void
    {
        $this->cannedHttpClient(['client_secret' => 'pi_5_secret_y', 'id' => 'pi_5', 'object' => 'payment_intent']);

        $pending = $this->provider()->createPaymentIntent(new PaymentIntentRequest(amountMinorUnits: 500, currency: 'usd', description: 'Gift', metadata: ['origin' => 'in_app']));

        $this->assertSame('pi_5_secret_y', $pending->clientSecret);
        $this->assertArrayHasKey('automatic_payment_methods', $this->sentParameters);
        $this->assertSame(['origin' => 'in_app'], $this->sentParameters['metadata'] ?? null);
    }

    /**
     * The card summary is brand, last four and expiry, or null without a default card
     *
     * @return void
     */
    public function testTheCardSummary(): void
    {
        $this->cannedHttpClient([
            'id'               => 'cus_1',
            'invoice_settings' => ['default_payment_method' => ['card' => ['brand' => 'visa', 'exp_month' => 4, 'exp_year' => 2030, 'last4' => '4242'], 'id' => 'pm_1', 'object' => 'payment_method']],
            'object'           => 'customer',
        ]);

        $card = $this->provider()->getDefaultCardSummary('cus_1');

        $this->assertNotNull($card);
        $this->assertSame(['visa', '4242', 4, 2030], [$card->brand, $card->last4, $card->expMonth, $card->expYear]);

        $this->cannedHttpClient(['id' => 'cus_2', 'invoice_settings' => ['default_payment_method' => null], 'object' => 'customer']);
        $this->assertNull($this->provider()->getDefaultCardSummary('cus_2'));
    }

    /**
     * A customer is created with only the fields given
     *
     * @return void
     */
    public function testACustomerIsCreatedWithTheGivenFields(): void
    {
        $this->cannedHttpClient(['id' => 'cus_7', 'object' => 'customer']);

        $this->assertSame('cus_7', $this->provider()->createCustomer(new CustomerRequest(email: 'grace@example.com', metadata: ['userId' => '7'])));
        $this->assertSame('grace@example.com', $this->sentParameters['email'] ?? null);
        $this->assertArrayNotHasKey('name', $this->sentParameters);
    }

    /**
     * Replacing the card moves live subscriptions to it, not just the customer default
     *
     * @return void
     */
    public function testReplacingTheCardMovesLiveSubscriptions(): void
    {
        $this->requestLines = [];
        $client             = $this->createStub(Client::class);
        $client->method('request')->willReturnCallback(
            function (mixed ...$arguments): array {
                $method               = is_string($arguments[0] ?? null) ? $arguments[0] : '';
                $url                  = is_string($arguments[1] ?? null) ? $arguments[1] : '';
                $this->requestLines[] = strtoupper($method) . ' ' . (string) parse_url($url, PHP_URL_PATH);

                $body = str_contains($url, '/v1/subscriptions') && strtolower($method) === 'get' ? ['data' => [['id' => 'sub_live', 'object' => 'subscription', 'status' => 'active'], ['id' => 'sub_old', 'object' => 'subscription', 'status' => 'canceled']], 'has_more' => false, 'object' => 'list'] : ['id' => 'x', 'object' => 'customer'];

                return [(string) json_encode($body), 200, []]; // phpcs:ignore Generic.PHP.ForbiddenFunctions -- canned HTTP fixture
            },
        );
        ApiRequestor::setHttpClient($client);

        $this->provider()->setDefaultPaymentMethod('cus_1', 'pm_new');

        $this->assertContains('POST /v1/customers/cus_1', $this->requestLines);
        $this->assertContains('POST /v1/subscriptions/sub_live', $this->requestLines);
        $this->assertNotContains('POST /v1/subscriptions/sub_old', $this->requestLines);
    }

    /**
     * Cancelling now is a DELETE, not the at-period-end update
     *
     * @return void
     */
    public function testCancellingNowDeletesTheSubscription(): void
    {
        $this->requestLines = [];
        $client             = $this->createStub(Client::class);
        $client->method('request')->willReturnCallback(
            function (mixed ...$arguments): array {
                $method               = is_string($arguments[0] ?? null) ? $arguments[0] : '';
                $url                  = is_string($arguments[1] ?? null) ? $arguments[1] : '';
                $this->requestLines[] = strtoupper($method) . ' ' . (string) parse_url($url, PHP_URL_PATH);

                return [(string) json_encode(['canceled_at' => 1758300000, 'customer' => 'cus_1', 'id' => 'sub_1', 'object' => 'subscription', 'status' => 'canceled']), 200, []]; // phpcs:ignore Generic.PHP.ForbiddenFunctions -- canned HTTP fixture
            },
        );
        ApiRequestor::setHttpClient($client);

        $subscription = $this->provider()->cancelSubscriptionNow('sub_1');

        $this->assertSame(['DELETE /v1/subscriptions/sub_1'], $this->requestLines);
        $this->assertSame('canceled', $subscription->status);
    }

    /**
     * A connected account is Express and asks for transfers, never Custom
     *
     * Custom would make this platform responsible for collecting the holder's
     * identity documents, which is the outcome the seam exists to prevent, so
     * the account type is asserted rather than left to a default.
     *
     * @return void
     */
    public function testAConnectedAccountIsExpressAndRequestsTransfers(): void
    {
        $this->cannedHttpClient(['id' => 'acct_1', 'object' => 'account']);

        $account = $this->provider()->createConnectedAccount(
            new ConnectedAccountRequest(country: 'US', email: 'creator@example.com', metadata: ['creatorId' => '4']),
        );

        $this->assertSame('acct_1', $account);
        $this->assertSame('express', $this->sentParameters['type'] ?? null);
        // The capture sits at the SDK's HTTP client, which is downstream of
        // its parameter encoding, so the boolean is already the string Stripe
        // will receive rather than the one this class passed.
        $this->assertSame(['transfers' => ['requested' => 'true']], $this->sentParameters['capabilities'] ?? null);
        $this->assertSame('US', $this->sentParameters['country'] ?? null);
        $this->assertSame('creator@example.com', $this->sentParameters['email'] ?? null);
        $this->assertSame(['creatorId' => '4'], $this->sentParameters['metadata'] ?? null);
    }

    /**
     * An omitted email is left out rather than sent empty
     *
     * @return void
     */
    public function testAConnectedAccountOmitsAnAbsentEmail(): void
    {
        $this->cannedHttpClient(['id' => 'acct_2', 'object' => 'account']);

        $this->provider()->createConnectedAccount(new ConnectedAccountRequest(country: 'US'));

        $this->assertArrayNotHasKey('email', $this->sentParameters);
        $this->assertArrayNotHasKey('metadata', $this->sentParameters);
    }

    /**
     * Onboarding and dashboard links come from their own endpoints
     *
     * @return void
     */
    public function testTheTwoHostedLinksUseTheirOwnEndpoints(): void
    {
        $this->cannedHttpClient(['created' => 1758300000, 'expires_at' => 1758300300, 'object' => 'account_link', 'url' => 'https://connect.stripe.com/setup/x']);

        $onboarding = $this->provider()->createConnectedAccountOnboardingLink('acct_1', 'https://app.example.com/refresh', 'https://app.example.com/done');

        $this->assertSame('https://connect.stripe.com/setup/x', $onboarding);
        $this->assertSame('account_onboarding', $this->sentParameters['type'] ?? null);
        $this->assertSame('acct_1', $this->sentParameters['account'] ?? null);
        $this->assertSame('https://app.example.com/refresh', $this->sentParameters['refresh_url'] ?? null);
        $this->assertSame('https://app.example.com/done', $this->sentParameters['return_url'] ?? null);

        $this->requestLines = [];
        $client             = $this->createStub(Client::class);
        $client->method('request')->willReturnCallback(
            function (mixed ...$arguments): array {
                $method               = is_string($arguments[0] ?? null) ? $arguments[0] : '';
                $url                  = is_string($arguments[1] ?? null) ? $arguments[1] : '';
                $this->requestLines[] = strtoupper($method) . ' ' . (string) parse_url($url, PHP_URL_PATH);

                return [(string) json_encode(['object' => 'login_link', 'url' => 'https://connect.stripe.com/express/y']), 200, []]; // phpcs:ignore Generic.PHP.ForbiddenFunctions -- canned HTTP fixture
            },
        );
        ApiRequestor::setHttpClient($client);

        $this->assertSame('https://connect.stripe.com/express/y', $this->provider()->createConnectedAccountDashboardLink('acct_1'));
        $this->assertSame(['POST /v1/accounts/acct_1/login_links'], $this->requestLines);
    }

    /**
     * Outstanding requirements report both buckets Stripe files them under, once each
     *
     * @return void
     */
    public function testOutstandingRequirementsMergeBothBucketsWithoutDuplicates(): void
    {
        $this->cannedHttpClient([
            'charges_enabled'   => false,
            'details_submitted' => true,
            'id'                => 'acct_1',
            'object'            => 'account',
            'payouts_enabled'   => false,
            'requirements'      => [
                'currently_due'   => ['individual.id_number', 'external_account'],
                'disabled_reason' => 'requirements.past_due',
                'past_due'        => ['external_account'],
            ],
        ]);

        $account = $this->provider()->getConnectedAccount('acct_1');

        $this->assertSame('acct_1', $account->providerAccountId);
        $this->assertFalse($account->payoutsEnabled);
        $this->assertFalse($account->chargesEnabled);
        $this->assertTrue($account->detailsSubmitted);
        $this->assertSame(['individual.id_number', 'external_account'], $account->requirementsDue);
        $this->assertSame('requirements.past_due', $account->disabledReason);
    }

    /**
     * An account with nothing outstanding reports no requirements and no reason
     *
     * @return void
     */
    public function testAReadyAccountReportsNothingOutstanding(): void
    {
        $this->cannedHttpClient([
            'charges_enabled'   => true,
            'details_submitted' => true,
            'id'                => 'acct_9',
            'object'            => 'account',
            'payouts_enabled'   => true,
            'requirements'      => ['currently_due' => [], 'disabled_reason' => null, 'past_due' => []],
        ]);

        $account = $this->provider()->getConnectedAccount('acct_9');

        $this->assertTrue($account->payoutsEnabled);
        $this->assertSame([], $account->requirementsDue);
        $this->assertNull($account->disabledReason);
    }

    /**
     * A transfer sends the caller's idempotency key as a header
     *
     * The key is the only thing standing between a retried run and paying
     * someone twice, so that it actually reaches Stripe is asserted rather than
     * assumed.
     *
     * @return void
     */
    public function testATransferSendsTheCallersIdempotencyKey(): void
    {
        $this->sentHeaders = [];
        $client            = $this->createStub(Client::class);
        $client->method('request')->willReturnCallback(
            function (mixed ...$arguments): array {
                $headers           = $arguments[2] ?? [];
                $this->sentHeaders = [];
                if (is_array($headers)) {
                    foreach ($headers as $header) {
                        if (is_string($header)) {
                            $this->sentHeaders[] = $header;
                        }
                    }
                }
                $params               = $arguments[3] ?? [];
                $this->sentParameters = is_array($params) ? $this->byName($params) : [];

                return [(string) json_encode(['amount' => 900, 'currency' => 'usd', 'destination' => 'acct_1', 'id' => 'tr_1', 'object' => 'transfer']), 200, []]; // phpcs:ignore Generic.PHP.ForbiddenFunctions -- canned HTTP fixture
            },
        );
        ApiRequestor::setHttpClient($client);

        $result = $this->provider()->createTransfer(new TransferRequest(
            destinationAccountReference: 'acct_1',
            amountMinorUnits:            900,
            currency:                    'usd',
            idempotencyKey:              'payout-run-12-creator-4',
            description:                 'Sowing.me payout',
        ));

        $this->assertSame('tr_1', $result->providerTransferId);
        $this->assertSame(900, $result->amountMinorUnits);
        $this->assertSame('acct_1', $result->destinationAccountReference);
        $this->assertContains('Idempotency-Key: payout-run-12-creator-4', $this->sentHeaders);
        $this->assertSame('acct_1', $this->sentParameters['destination'] ?? null);
    }

    /**
     * A transfer without a positive amount or a key never reaches the provider
     *
     * @return void
     */
    public function testATransferRefusesANonPositiveAmountOrAMissingKey(): void
    {
        $this->requestLines = [];
        $client             = $this->createStub(Client::class);
        $client->method('request')->willReturnCallback(
            function (mixed ...$arguments): array {
                $url                  = is_string($arguments[1] ?? null) ? $arguments[1] : '';
                $this->requestLines[] = (string) parse_url($url, PHP_URL_PATH);

                return ['{}', 200, []];
            },
        );
        ApiRequestor::setHttpClient($client);

        try {
            $this->provider()->createTransfer(new TransferRequest(destinationAccountReference: 'acct_1', amountMinorUnits: 0, currency: 'usd', idempotencyKey: 'k'));
            $this->fail('A zero transfer should be refused');
        } catch (DtoException $e) {
            $this->assertSame(ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value, $e->getCode());
        }

        try {
            $this->provider()->createTransfer(new TransferRequest(destinationAccountReference: 'acct_1', amountMinorUnits: 900, currency: 'usd'));
            $this->fail('A transfer without an idempotency key should be refused');
        } catch (DtoException $e) {
            $this->assertSame(ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value, $e->getCode());
        }

        $this->assertSame([], $this->requestLines);
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

        $client = $this->createStub(Client::class);
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
