<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object describing a single, non-recurring payment to collect
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createOneOffCheckout()}.
 *
 * The amount is in **minor units** and is always accompanied by its currency,
 * because a bare integer amount is meaningless and a float amount is wrong:
 * 19.99 has no exact binary representation, so money that round-trips through a
 * float eventually fails to reconcile by a cent. A host that wants to display
 * "$19.99" formats it at the edge from these two fields.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\OneOffCheckoutRequestTest PHPUnit test case
 */
final readonly class OneOffCheckoutRequest implements Dto
{
    /**
     * Constructor
     *
     * @param int                   $amountMinorUnits  The amount to collect, in the currency's minor units (cents for USD); never a float, and never separable from $currency
     * @param string                $currency          ISO 4217 currency code, lower-case (for example `usd`)
     * @param string                $description       What the payer is paying for, shown on the hosted page and on their statement where the provider supports it
     * @param string                $successUrl        Where the provider returns the payer after a completed payment; a host must treat arrival here as "the payer says it worked", never as proof of payment -- only a verified webhook is proof (see {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::verifyWebhook()})
     * @param string                $cancelUrl         Where the provider returns the payer if they abandon the payment
     * @param ?string               $customerReference The provider's own id for a payer this host has transacted with before, if it has one; null creates a new customer on the provider's side
     * @param string                $clientReference   The host's own opaque reference for this payment, echoed back on every webhook about it; this is how a host reconnects a provider event to its own records without trusting anything else in the payload
     * @param array<string, string> $metadata          Additional host-side key/value context stored with the payment on the provider; never put anything secret or personally identifying here, since it is visible in the provider's dashboard and API
     */
    public function __construct(
        public readonly int $amountMinorUnits = 0,
        public readonly string $currency = '',
        public readonly string $description = '',
        public readonly string $successUrl = '',
        public readonly string $cancelUrl = '',
        public readonly ?string $customerReference = null,
        public readonly string $clientReference = '',
        public readonly array $metadata = [],
    ) {
    }
}
