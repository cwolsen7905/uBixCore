<?php

declare(strict_types=1);

namespace Ubix\Service\Payment;

use Psr\Log\LoggerInterface as Logger;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Subscription;
use Stripe\Webhook;
use Throwable;
use Ubix\DataTransferObject\Payment\CardSummary;
use Ubix\DataTransferObject\Payment\CheckoutSession;
use Ubix\DataTransferObject\Payment\CustomerRequest;
use Ubix\DataTransferObject\Payment\OneOffCheckoutRequest;
use Ubix\DataTransferObject\Payment\PaymentIntentRequest;
use Ubix\DataTransferObject\Payment\PendingPayment;
use Ubix\DataTransferObject\Payment\PendingSubscriptionRequest;
use Ubix\DataTransferObject\Payment\ProviderSubscription;
use Ubix\DataTransferObject\Payment\RecurringPriceRequest;
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
    public function cancelSubscriptionNow(string $providerSubscriptionId): ProviderSubscription
    {
        try {
            // DELETE /v1/subscriptions/{id}: cancelled now. For an incomplete
            // subscription Stripe also voids the open first invoice, which is
            // the point -- its payment can no longer be confirmed.
            $subscription = $this->client()->subscriptions->cancel($providerSubscriptionId);
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
     * {@inheritDoc}
     */
    public function createCustomer(CustomerRequest $request): string
    {
        $customer = $this->call('create a customer', function () use ($request): object {
            return $this->client()->customers->create(array_filter([
                'email'    => $request->email,
                'metadata' => $request->metadata,
                'name'     => $request->name,
            ], static function (mixed $value): bool {
                return $value !== '' && $value !== [];
            }));
        });

        return $this->idOf($customer);
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the amount or period is invalid, or the provider refuses
     */
    public function createRecurringPrice(RecurringPriceRequest $request): string
    {
        if ($request->amountMinorUnits <= 0) {
            throw new DtoException('A recurring price must be positive', ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value);
        }

        $price = $this->call('create a recurring price', function () use ($request): object {
            return $this->client()->prices->create([
                'currency'     => strtolower($request->currency),
                'metadata'     => $request->metadata,
                'product_data' => ['name' => $request->productName],
                'recurring'    => $this->recurringFor($request->intervalMonths),
                'unit_amount'  => $request->amountMinorUnits,
            ]);
        });

        return $this->idOf($price);
    }

    /**
     * {@inheritDoc}
     */
    public function createPendingSubscription(PendingSubscriptionRequest $request): PendingPayment
    {
        $subscription = $this->call('create a subscription', function () use ($request): object {
            return $this->client()->subscriptions->create([
                'customer'         => $request->customerReference,
                // Basil: the first invoice's confirmation secret replaces the
                // payment intent's client secret.
                'expand'           => ['latest_invoice.confirmation_secret'],
                'items'            => [['price' => $request->priceReference]],
                'metadata'         => $request->metadata,
                // Created awaiting payment; it activates when the payer
                // confirms in the page, and expires if they never do.
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
            ]);
        });

        $confirmation = $this->property($this->property($subscription, 'latest_invoice'), 'confirmation_secret');
        $secret       = $this->property($confirmation, 'client_secret');

        return new PendingPayment(providerReference: $this->idOf($subscription), clientSecret: $this->secretOf($secret));
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the amount is invalid, or the provider refuses
     */
    public function createPaymentIntent(PaymentIntentRequest $request): PendingPayment
    {
        if ($request->amountMinorUnits <= 0) {
            throw new DtoException('A payment amount must be positive', ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value);
        }

        $intent = $this->call('create a payment', function () use ($request): object {
            $parameters = [
                'amount'                    => $request->amountMinorUnits,
                'automatic_payment_methods' => ['enabled' => true],
                'currency'                  => strtolower($request->currency),
                'metadata'                  => $request->metadata,
            ];
            if ($request->customerReference !== null && $request->customerReference !== '') {
                $parameters['customer'] = $request->customerReference;
            }
            if ($request->description !== '') {
                $parameters['description'] = $request->description;
            }

            return $this->client()->paymentIntents->create($parameters);
        });

        return new PendingPayment(providerReference: $this->idOf($intent), clientSecret: $this->secretOf($this->property($intent, 'client_secret')));
    }

    /**
     * {@inheritDoc}
     */
    public function createSetupIntent(string $customerReference): PendingPayment
    {
        $intent = $this->call('start saving a card', function () use ($customerReference): object {
            return $this->client()->setupIntents->create([
                'automatic_payment_methods' => ['enabled' => true],
                'customer'                  => $customerReference,
                'usage'                     => 'off_session',
            ]);
        });

        return new PendingPayment(providerReference: $this->idOf($intent), clientSecret: $this->secretOf($this->property($intent, 'client_secret')));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultPaymentMethod(string $customerReference, string $paymentMethodReference): void
    {
        $this->call('set the default card', function () use ($customerReference, $paymentMethodReference): object {
            return $this->client()->customers->update($customerReference, [
                'invoice_settings' => ['default_payment_method' => $paymentMethodReference],
            ]);
        });

        // Subscriptions created with save_default_payment_method=on_subscription
        // carry their OWN default card, which outranks the customer's. Without
        // this, "replace my card" would change nothing about the next renewal.
        $subscriptions = $this->call('list the customer\'s subscriptions', function () use ($customerReference): object {
            return $this->client()->subscriptions->all(['customer' => $customerReference, 'limit' => 100, 'status' => 'all']);
        });

        $data = $this->property($subscriptions, 'data');
        foreach (is_iterable($data) ? $data : [] as $subscription) {
            $status = $this->property($subscription, 'status');
            if (!in_array($status, ['active', 'past_due', 'trialing', 'unpaid'], true)) {
                continue;
            }

            $id = $this->property($subscription, 'id');
            if (!is_string($id) || $id === '') {
                continue;
            }

            $this->call('move a subscription to the new card', function () use ($id, $paymentMethodReference): object {
                return $this->client()->subscriptions->update($id, ['default_payment_method' => $paymentMethodReference]);
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultCardSummary(string $customerReference): ?CardSummary
    {
        $customer = $this->call('look up the default card', function () use ($customerReference): object {
            return $this->client()->customers->retrieve($customerReference, ['expand' => ['invoice_settings.default_payment_method']]);
        });

        $card = $this->property($this->property($this->property($customer, 'invoice_settings'), 'default_payment_method'), 'card');
        if (!is_object($card)) {
            return null;
        }

        $brand    = $this->property($card, 'brand');
        $last4    = $this->property($card, 'last4');
        $expMonth = $this->property($card, 'exp_month');
        $expYear  = $this->property($card, 'exp_year');

        return new CardSummary(
            brand:    is_string($brand) ? $brand : '',
            last4:    is_string($last4) ? $last4 : '',
            expMonth: is_numeric($expMonth) ? (int) $expMonth : 0,
            expYear:  is_numeric($expYear) ? (int) $expYear : 0,
        );
    }

    /**
     * Make one provider call, turning any failure into the provider-operation error
     *
     * @param string   $what What was being done, for the log and the message
     * @param callable $call The call
     *
     * @throws DtoException When the provider refuses or cannot be reached
     *
     * @return object The provider's response object
     */
    private function call(string $what, callable $call): object
    {
        try {
            $result = $call();
        } catch (Throwable $e) {
            $this->logger->error('Stripe refused to ' . $what, ['error' => $e->getMessage()]);

            throw new DtoException('The payment provider could not ' . $what, ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value, previous: $e);
        }

        if (!is_object($result)) {
            throw new DtoException('The payment provider could not ' . $what, ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value);
        }

        return $result;
    }

    /**
     * One property of a provider response, or null when the value is not an object or lacks it
     *
     * The SDK types every nested field as mixed; this keeps the narrowing in
     * one place instead of at every access.
     *
     * @param mixed  $object The response object, or anything
     * @param string $name   The property
     *
     * @return mixed The value, or null
     */
    private function property(mixed $object, string $name): mixed
    {
        return is_object($object) && isset($object->{$name}) ? $object->{$name} : null;
    }

    /**
     * A provider object's id
     *
     * @param object $object The response object
     *
     * @throws DtoException When it has none
     *
     * @return string The id
     */
    private function idOf(object $object): string
    {
        $id = $object->id ?? null;
        if (!is_string($id) || $id === '') {
            throw new DtoException('The payment provider returned no id', ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value);
        }

        return $id;
    }

    /**
     * A confirmation secret, which must be present for the page to confirm anything
     *
     * @param mixed $secret The value from the response
     *
     * @throws DtoException When it is missing
     *
     * @return string The secret
     */
    private function secretOf(mixed $secret): string
    {
        if (!is_string($secret) || $secret === '') {
            throw new DtoException('The payment provider returned no confirmation secret', ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value);
        }

        return $secret;
    }

    /**
     * The provider's recurring block for a period in months
     *
     * "month × N", exactly as the hosted Checkout path bills it, so a tier
     * costs the same whichever way it was bought.
     *
     * @param int $months The period, 1 to 12 months
     *
     * @throws DtoException Outside that range
     *
     * @return array{interval: string, interval_count: int} The block
     */
    private function recurringFor(int $months): array
    {
        if ($months < 1 || $months > 12) {
            throw new DtoException('A recurring period is 1 to 12 months', ExceptionCode::PAYMENT_PROVIDER_OPERATION_FAILED->value);
        }

        return ['interval' => 'month', 'interval_count' => $months];
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
