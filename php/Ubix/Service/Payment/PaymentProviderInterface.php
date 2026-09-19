<?php

declare(strict_types=1);

namespace Ubix\Service\Payment;

use Ubix\DataTransferObject\Payment\CheckoutSession;
use Ubix\DataTransferObject\Payment\OneOffCheckoutRequest;
use Ubix\DataTransferObject\Payment\ProviderSubscription;
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
interface PaymentProviderInterface
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
    public function fetchSubscription(string $providerSubscriptionId): ProviderSubscription;

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
}
