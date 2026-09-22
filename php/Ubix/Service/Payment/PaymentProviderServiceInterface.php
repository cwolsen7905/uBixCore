<?php

declare(strict_types=1);

namespace Ubix\Service\Payment;

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

/**
 * Interface for a vendor-agnostic payment-provider seam
 *
 * A host that takes money depends on this interface, never on a specific
 * payment processor's SDK. The concrete implementation is the **only** code in
 * a host's entire codebase permitted to import that SDK; swapping processors,
 * or running two, is a DI binding change rather than a rewrite of every call
 * site that happens to mention money.
 *
 * Three properties of this seam are load-bearing and are not stylistic:
 *
 * **Every payment path is provider-hosted.** Both checkout methods return a URL
 * to redirect a payer to, and nothing on this interface accepts a card number,
 * expiry or CVV. There is deliberately no method that could carry them. A host
 * built against this interface therefore has no card-collecting form to audit,
 * no card data in its request logs, and no card data in its database -- not as
 * a matter of discipline, but because the interface offers no way to obtain it.
 *
 * **Returning from a hosted page is not proof of payment.** A payer arriving at
 * a success URL has told the host it worked; they may be wrong, they may have
 * navigated there directly, and the charge may still fail minutes later. The
 * only proof is a signed webhook, which is why {@see self::verifyWebhook()}
 * returns a distinct type -- see {@see VerifiedWebhookEvent} for why that type
 * exists at all.
 *
 * **Money is minor units plus a currency, everywhere.** No method on this
 * interface accepts or returns a float amount, and no amount travels without
 * the currency it is denominated in.
 *
 * This interface knows nothing about what is being sold, who is selling it, or
 * who may see it afterwards. Entitlement, ledgers, fees, commission splits and
 * payouts to sellers are all host concerns that live above this seam.
 */
interface PaymentProviderServiceInterface
{
    /**
     * Create a provider-hosted page for collecting one non-recurring payment
     *
     * @param OneOffCheckoutRequest $request What to collect, and where to return the payer afterwards
     *
     * @throws \Ubix\Exception\DtoException If the provider rejects the request or is unreachable
     *
     * @return CheckoutSession The hosted page to redirect the payer to
     */
    public function createOneOffCheckout(OneOffCheckoutRequest $request): CheckoutSession;

    /**
     * Create a provider-hosted page for setting up a recurring payment
     *
     * Completing this page creates an ongoing obligation that will charge the
     * payer unattended until something cancels it, which is why it is a
     * separate method from {@see self::createOneOffCheckout()} rather than a
     * flag on the same one.
     *
     * @param SubscriptionCheckoutRequest $request What to collect and how often, and where to return the payer afterwards
     *
     * @throws \Ubix\Exception\DtoException If the provider rejects the request or is unreachable
     *
     * @return CheckoutSession The hosted page to redirect the payer to
     */
    public function createSubscriptionCheckout(SubscriptionCheckoutRequest $request): CheckoutSession;

    /**
     * Verify an inbound webhook's signature and return the event it carries
     *
     * This is the gate on a publicly-reachable, unauthenticated endpoint that
     * tells the host money moved. It must be called on the **raw** request body
     * before that body is parsed as JSON, because signatures are computed over
     * exact bytes -- decoding and re-encoding changes them, and a host that
     * parses first has already spent work on data it has no reason to believe.
     *
     * An implementation must reject rather than repair: a missing, malformed,
     * mismatched or stale signature throws, and nothing is returned that a
     * caller could mistake for a verified event.
     *
     * @param string $rawPayload      The request body exactly as received, unparsed and unmodified
     * @param string $signatureHeader The provider's signature header value, verbatim
     *
     * @throws \Ubix\Exception\DtoException If the signature is absent, malformed, does not match, or is outside the provider's accepted age
     *
     * @return VerifiedWebhookEvent The verified event -- a type an unverified payload cannot become
     */
    public function verifyWebhook(string $rawPayload, string $signatureHeader): VerifiedWebhookEvent;

    /**
     * Read the provider's current view of a recurring payment
     *
     * The provider is the system of record for whether money is still being
     * collected. A host reconciling its own records against reality -- after
     * missed webhooks, or on a schedule -- asks here rather than trusting what
     * it last wrote down.
     *
     * @param string $providerSubscriptionId The provider's id for the recurring payment
     *
     * @throws \Ubix\Exception\DtoException If no such subscription exists at the provider, or it is unreachable
     *
     * @return ProviderSubscription The provider's view, with its own unmapped status string
     */
    public function getSubscription(string $providerSubscriptionId): ProviderSubscription;

    /**
     * Stop a recurring payment from charging again
     *
     * Cancelling does not necessarily end access immediately: the payer has
     * usually paid through the end of the current period, and the returned
     * subscription reports when that is. Deciding whether access stops now or
     * at period end is a host's policy, not this seam's.
     *
     * @param string $providerSubscriptionId The provider's id for the recurring payment
     *
     * @throws \Ubix\Exception\DtoException If no such subscription exists at the provider, or it is unreachable
     *
     * @return ProviderSubscription The provider's view after cancellation, including the paid-through date
     */
    public function cancelSubscription(string $providerSubscriptionId): ProviderSubscription;

    /**
     * End a recurring payment immediately, with nothing further owed or payable
     *
     * For discarding a subscription that was never paid for, such as an
     * abandoned attempt still awaiting its first payment. Unlike
     * `cancelSubscription()`, nothing runs on to a period end: any open first
     * invoice stops being payable, so a stale payment form cannot complete it
     * later. Not for ending one the payer has paid for; that is
     * `cancelSubscription()`, which keeps the paid-through time.
     *
     * @param string $providerSubscriptionId The provider's id for the recurring payment
     *
     * @throws \Ubix\Exception\DtoException If no such subscription exists at the provider, or it is unreachable
     *
     * @return ProviderSubscription The provider's view after cancellation
     */
    public function cancelSubscriptionNow(string $providerSubscriptionId): ProviderSubscription;

    /**
     * Return money for a payment the provider has already taken
     *
     * @param string $providerPaymentReference The provider's id for the payment being refunded
     * @param ?int   $amountMinorUnits         How much to return, in minor units; null refunds the full remaining amount, which is the only form some providers support
     *
     * @throws \Ubix\Exception\DtoException If the payment is unknown, already fully refunded, the amount exceeds what remains, or the provider is unreachable
     *
     * @return RefundResult The accepted refund, with a positive amount; a host applies its own ledger sign convention
     */
    public function refundPayment(string $providerPaymentReference, ?int $amountMinorUnits): RefundResult;

    /**
     * The payment reference an invoice was settled by, if the provider has one
     *
     * A recurring charge arrives as an invoice, but a refund of it arrives
     * naming the underlying payment. Some providers' invoice payloads carry no
     * link between the two -- Stripe's, since its 2025-03-31 API release,
     * carries neither the payment intent nor the charge -- so a host that
     * records the invoice has no way to match the later refund back to it.
     * Asking once, when the invoice is paid, lets the host record the payment
     * reference the refund will actually name.
     *
     * @param string $invoiceId The provider's invoice id
     *
     * @throws \Ubix\Exception\DtoException If the provider is unreachable
     *
     * @return ?string The provider's payment reference, or null when the invoice was settled some other way (credit balance, zero amount)
     */
    public function getPaymentReferenceForInvoice(string $invoiceId): ?string;

    /*
     * In-app billing: the payer never leaves the host's pages. The host shows
     * the provider's embedded payment form (Stripe's Payment Element), which
     * collects card details inside the provider's own frames, so card numbers
     * still never reach the host. These calls create what that form confirms.
     */

    /**
     * Create a payer on the provider, to own saved cards and subscriptions
     *
     * @param CustomerRequest $request Who the payer is
     *
     * @return string The provider's customer id
     */
    public function createCustomer(CustomerRequest $request): string;

    /**
     * Create a recurring price, once, for reuse by every subscription at it
     *
     * @param RecurringPriceRequest $request The amount, period and product name
     *
     * @return string The provider's price id
     */
    public function createRecurringPrice(RecurringPriceRequest $request): string;

    /**
     * Create a subscription that waits for its first payment, confirmed in the page
     *
     * The subscription exists on the provider immediately but is not active
     * until the payer confirms the first invoice with the returned secret;
     * the provider expires it if that never happens. Activation arrives, as
     * ever, by webhook.
     *
     * @param PendingSubscriptionRequest $request The customer, price and metadata
     *
     * @return PendingPayment The subscription id and the first invoice's confirmation secret
     */
    public function createPendingSubscription(PendingSubscriptionRequest $request): PendingPayment;

    /**
     * Create a one-off payment to confirm in the page
     *
     * @param PaymentIntentRequest $request The amount, description and metadata
     *
     * @return PendingPayment The payment id and its confirmation secret
     */
    public function createPaymentIntent(PaymentIntentRequest $request): PendingPayment;

    /**
     * Start saving a card for later payments, confirmed in the page
     *
     * @param string $customerReference The provider's customer id
     *
     * @return PendingPayment The setup id and its confirmation secret
     */
    public function createSetupIntent(string $customerReference): PendingPayment;

    /**
     * Make a saved card the one future invoices charge
     *
     * @param string $customerReference      The provider's customer id
     * @param string $paymentMethodReference The provider's payment method id
     *
     * @return void
     */
    public function setDefaultPaymentMethod(string $customerReference, string $paymentMethodReference): void;

    /**
     * The card future invoices will charge, as much as a payer needs to recognise it
     *
     * @param string $customerReference The provider's customer id
     *
     * @return ?CardSummary Brand, last four and expiry; null when there is no default card
     */
    public function getDefaultCardSummary(string $customerReference): ?CardSummary;
}
