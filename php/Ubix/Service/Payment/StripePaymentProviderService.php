<?php

declare(strict_types=1);

namespace Ubix\Service\Payment;

use Psr\Log\LoggerInterface as Logger;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Subscription;
use Stripe\Webhook;
use Throwable;
use Ubix\DataTransferObject\Payment\CheckoutSession;
use Ubix\DataTransferObject\Payment\OneOffCheckoutRequest;
use Ubix\DataTransferObject\Payment\ProviderSubscription;
use Ubix\DataTransferObject\Payment\RefundResult;
use Ubix\DataTransferObject\Payment\SubscriptionCheckoutRequest;
use Ubix\DataTransferObject\Payment\VerifiedWebhookEvent;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Payment\PaymentProviderServiceInterface as PaymentProviderService;

/**
 * Stripe implementation of {@see PaymentProviderServiceInterface}
 *
 * The only class in the framework that imports the Stripe SDK — every consumer
 * depends on {@see PaymentProviderServiceInterface} instead, wired via PHP-DI, so
 * swapping or adding a processor is a DI binding change and never a change to
 * any call site that happens to mention money.
 *
 * The SDK is a `require-dev` + `suggest` dependency here rather than a hard
 * `require`, exactly as `aws/aws-sdk-php` is for the media seam: a host that
 * never takes money has no business being made to install a payment SDK. A
 * host that binds this class requires `stripe/stripe-php` itself.
 *
 * Everything is hosted checkout. This class never sees a card number, expiry
 * or CVV, because the interface it implements offers no method that could carry
 * one — the PCI boundary is structural rather than a matter of discipline
 * (ADR-003).
 *
 * | Env var                 | Meaning                                                      | Source |
 * |-------------------------|--------------------------------------------------------------|--------|
 * | `STRIPE_SECRET_KEY`     | The secret API key this process authenticates with            | **uBixVault** in every deployed environment; `.env` only as a local-dev convenience |
 * | `STRIPE_WEBHOOK_SECRET` | The signing secret webhook signatures are verified against    | **uBixVault**; differs per endpoint and per environment |
 *
 * Neither is ever logged. A failed call logs the provider's message, never the
 * credentials that made it.
 *
 * @see \Ubix\Tests\Service\Payment\StripePaymentProviderServiceTest PHPUnit test case
 */
final class StripePaymentProviderService implements PaymentProviderService
{
    /**
     * Constructor
     *
     * The client is injectable so tests can drive this class without a network
     * or a key; in production it is left null and built from configuration.
     *
     * @param Logger        $logger       The Monolog logger
     * @param ?StripeClient $stripeClient A pre-built client, or null to build one from configuration
     */
    public function __construct(
        private Logger $logger,
        private ?StripeClient $stripeClient = null,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function createOneOffCheckout(OneOffCheckoutRequest $request): CheckoutSession
    {
        return $this->createCheckout(
            mode:        'payment',
            amount:      $request->amountMinorUnits,
            currency:    $request->currency,
            description: $request->description,
            successUrl:  $request->successUrl,
            cancelUrl:   $request->cancelUrl,
            customer:    $request->customerReference,
            clientRef:   $request->clientReference,
            metadata:    $request->metadata,
            recurring:   null,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createSubscriptionCheckout(SubscriptionCheckoutRequest $request): CheckoutSession
    {
        return $this->createCheckout(
            mode:        'subscription',
            amount:      $request->amountMinorUnits,
            currency:    $request->currency,
            description: $request->description,
            successUrl:  $request->successUrl,
            cancelUrl:   $request->cancelUrl,
            customer:    $request->customerReference,
            clientRef:   $request->clientReference,
            metadata:    $request->metadata,
            recurring:   ['interval' => 'month', 'interval_count' => max(1, $request->intervalMonths)],
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the provider rejects the request or is unreachable
     */
    public function verifyWebhook(string $rawPayload, string $signatureHeader): VerifiedWebhookEvent
    {
        $secret = (string) getenv('STRIPE_WEBHOOK_SECRET');
        if ($secret === '') {
            // Fail closed. With no secret configured every signature check would
            // pass or be skipped, which turns a public URL into an open door to
            // the ledger.
            throw new DtoException(
                'No webhook signing secret is configured',
                ExceptionCode::PAYMENT_WEBHOOK_SIGNATURE_INVALID->value,
            );
        }

        try {
            // Verified over the raw bytes, before anything decodes them.
            $event = Webhook::constructEvent($rawPayload, $signatureHeader, $secret);
        } catch (SignatureVerificationException $e) {
            // The payload is attacker-controlled and unverified, so it is never
            // logged -- only the fact that verification failed.
            $this->logger->warning('Rejected a webhook with an invalid Stripe signature');

            throw new DtoException(
                'Webhook signature verification failed',
                ExceptionCode::PAYMENT_WEBHOOK_SIGNATURE_INVALID->value,
                previous: $e,
            );
        } catch (Throwable $e) {
            $this->logger->warning('Rejected a malformed webhook');

            throw new DtoException(
                'Webhook could not be verified',
                ExceptionCode::PAYMENT_WEBHOOK_SIGNATURE_INVALID->value,
                previous: $e,
            );
        }

        // Rekeyed to string keys: toArray() is the SDK's own generic array, and
        // the DTO is explicit that the payload is keyed by name.
        $payload = [];
        foreach ($event->toArray() as $key => $value) {
            $payload[(string) $key] = $value;
        }

        return new VerifiedWebhookEvent(
            providerEventId:        (string) $event->id,
            type:                   (string) $event->type,
            payload:                $payload,
            createdAtUnixTimestamp: (int) $event->created,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the provider rejects the request or is unreachable
     */
    public function getSubscription(string $providerSubscriptionId): ProviderSubscription
    {
        try {
            $subscription = $this->client()->subscriptions->retrieve($providerSubscriptionId);
        } catch (Throwable $e) {
            throw new DtoException(
                'No such subscription at the provider',
                ExceptionCode::PAYMENT_SUBSCRIPTION_NOT_FOUND->value,
                previous: $e,
            );
        }

        return $this->toProviderSubscription($subscription);
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the provider rejects the request or is unreachable
     */
    public function cancelSubscription(string $providerSubscriptionId): ProviderSubscription
    {
        try {
            // Cancelled at period end, not immediately. The interface's contract
            // is "stop charging again", and the payer has usually paid through
            // the current period -- ending access the moment they click cancel
            // would take money for time they do not get. Whether access stops
            // now or at period end is the host's policy, and it can only make
            // that choice if the paid-through date still exists.
            $subscription = $this->client()->subscriptions->update(
                $providerSubscriptionId,
                ['cancel_at_period_end' => true],
            );
        } catch (Throwable $e) {
            throw new DtoException(
                'Could not cancel that subscription at the provider',
                ExceptionCode::PAYMENT_SUBSCRIPTION_NOT_FOUND->value,
                previous: $e,
            );
        }

        return $this->toProviderSubscription($subscription);
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the provider rejects the request or is unreachable
     */
    public function refundPayment(string $providerPaymentReference, ?int $amountMinorUnits): RefundResult
    {
        $parameters = ['payment_intent' => $providerPaymentReference];

        // Omitted rather than sent as null: Stripe reads an absent amount as
        // "refund everything remaining", which is the documented meaning of a
        // null argument here.
        if ($amountMinorUnits !== null) {
            if ($amountMinorUnits <= 0) {
                throw new DtoException(
                    'A refund amount must be positive',
                    ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value,
                );
            }

            $parameters['amount'] = $amountMinorUnits;
        }

        try {
            $refund = $this->client()->refunds->create($parameters);
        } catch (Throwable $e) {
            $this->logger->error('Stripe refused a refund', ['error' => $e->getMessage()]);

            throw new DtoException(
                'The provider refused the refund',
                ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value,
                previous: $e,
            );
        }

        return new RefundResult(
            providerRefundId:         (string) $refund->id,
            providerPaymentReference: $providerPaymentReference,
            // Positive, always. A host applies its own ledger sign convention.
            amountMinorUnits:         abs((int) $refund->amount),
            currency:                 (string) $refund->currency,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the provider refuses the lookup or is unreachable
     */
    public function getPaymentReferenceForInvoice(string $invoiceId): ?string
    {
        try {
            $payments = $this->client()->invoicePayments->all(['invoice' => $invoiceId, 'limit' => 10]);
        } catch (Throwable $e) {
            $this->logger->error('Stripe refused an invoice payment lookup', ['error' => $e->getMessage()]);

            throw new DtoException(
                'Could not look up how that invoice was paid',
                ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value,
                previous: $e,
            );
        }

        // An invoice can carry several payment attempts. The one that settled
        // it is `paid`; earlier failed attempts are not what a refund names.
        foreach ($payments->data as $invoicePayment) {
            if (($invoicePayment->status ?? null) !== 'paid') {
                continue;
            }

            $payment = $invoicePayment->payment ?? null;
            if (!is_object($payment)) {
                continue;
            }

            $reference = $payment->payment_intent ?? $payment->charge ?? null;
            if (is_string($reference) && $reference !== '') {
                return $reference;
            }
        }

        return null;
    }

    /**
     * Create a hosted Checkout Session in either mode
     *
     * @param string                                        $mode        Either `payment` or `subscription`
     * @param int                                           $amount      The amount in minor units
     * @param string                                        $currency    The ISO 4217 currency code, lower-case
     * @param string                                        $description What the payer is paying for
     * @param string                                        $successUrl  Where the payer returns on success
     * @param string                                        $cancelUrl   Where the payer returns on abandonment
     * @param ?string                                       $customer    The provider's customer id, when known
     * @param string                                        $clientRef   The host's own opaque reference
     * @param array<string, string>                         $metadata    Host-side context
     * @param ?array{interval: string, interval_count: int} $recurring   Stripe's recurring block, or null for one-off
     *
     * @throws DtoException When the provider rejects the request or is unreachable
     *
     * @return CheckoutSession The hosted page to redirect the payer to
     */
    private function createCheckout(
        string $mode,
        int $amount,
        string $currency,
        string $description,
        string $successUrl,
        string $cancelUrl,
        ?string $customer,
        string $clientRef,
        array $metadata,
        ?array $recurring,
    ): CheckoutSession {
        if ($amount <= 0 || $currency === '') {
            throw new DtoException(
                'A checkout needs a positive amount and a currency',
                ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value,
            );
        }

        // Built as two whole literals rather than one that is added to: the SDK
        // documents a precise array shape for `price_data`, and mutating a
        // literal after the fact widens the inferred type past what it accepts.
        $productData = ['name' => $description === '' ? 'Payment' : $description];
        $priceData   = $recurring === null ? [
            'currency'     => strtolower($currency),
            'product_data' => $productData,
            'unit_amount'  => $amount,
        ] : [
            'currency'     => strtolower($currency),
            'product_data' => $productData,
            'recurring'    => $recurring,
            'unit_amount'  => $amount,
        ];

        $parameters = [
            'cancel_url'  => $cancelUrl,
            'line_items'  => [['price_data' => $priceData, 'quantity' => 1]],
            'metadata'    => $metadata,
            'mode'        => $mode,
            'success_url' => $successUrl,
        ];

        if ($clientRef !== '') {
            $parameters['client_reference_id'] = $clientRef;
        }

        if ($customer !== null && $customer !== '') {
            $parameters['customer'] = $customer;
        }

        // Also stamped on the subscription itself: a Checkout Session's metadata
        // does not propagate to the subscription Stripe creates from it, and
        // every later invoice event names the subscription rather than the
        // session. Without this the recurring charges could not be traced back.
        if ($recurring !== null && $metadata !== []) {
            $parameters['subscription_data'] = ['metadata' => $metadata];
        }

        try {
            $session = $this->client()->checkout->sessions->create($parameters);
        } catch (Throwable $e) {
            $this->logger->error('Stripe refused a checkout session', ['mode' => $mode, 'error' => $e->getMessage()]);

            throw new DtoException(
                'The provider refused the checkout session',
                ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value,
                previous: $e,
            );
        }

        $url = (string) $session->url;
        if ($url === '') {
            throw new DtoException(
                'The provider returned a checkout session with no URL',
                ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value,
            );
        }

        return new CheckoutSession(
            url:                    $url,
            providerSessionId:      (string) $session->id,
            expiresAtUnixTimestamp: (int) $session->expires_at,
        );
    }

    /**
     * Map a Stripe subscription onto the seam's DTO
     *
     * The status is passed through **unmapped**, deliberately: translating it
     * here would bury a host's entitlement decision inside a vendor adapter,
     * and a status this adapter had never heard of would be silently flattened
     * into one that grants access.
     *
     * @param Subscription $subscription The Stripe subscription object
     *
     * @return ProviderSubscription The seam's view
     */
    private function toProviderSubscription(Subscription $subscription): ProviderSubscription
    {
        // The customer arrives either as an id or as an expanded object,
        // depending on what the call asked for; both are normalised to the id.
        $customer   = $subscription->customer;
        $customerId = '';
        if (is_string($customer)) {
            $customerId = $customer;
        } elseif (is_object($customer) && isset($customer->id) && is_string($customer->id)) {
            $customerId = $customer->id;
        }

        $canceledAt = $subscription->canceled_at;
        $periodEnd  = $subscription->current_period_end ?? null;

        return new ProviderSubscription(
            providerSubscriptionId:        is_string($subscription->id) ? $subscription->id : '',
            providerCustomerId:            $customerId,
            status:                        is_string($subscription->status) ? $subscription->status : '',
            currentPeriodEndUnixTimestamp: is_numeric($periodEnd) ? (int) $periodEnd : 0,
            canceledAtUnixTimestamp:       is_numeric($canceledAt) ? (int) $canceledAt : null,
        );
    }

    /**
     * The Stripe client, built from configuration on first use
     *
     * @throws DtoException When no secret key is configured
     *
     * @return StripeClient The client
     */
    private function client(): StripeClient
    {
        if ($this->stripeClient instanceof StripeClient) {
            return $this->stripeClient;
        }

        $secretKey = (string) getenv('STRIPE_SECRET_KEY');
        if ($secretKey === '') {
            throw new DtoException(
                'No Stripe secret key is configured',
                ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value,
            );
        }

        $this->stripeClient = new StripeClient($secretKey);

        return $this->stripeClient;
    }
}
