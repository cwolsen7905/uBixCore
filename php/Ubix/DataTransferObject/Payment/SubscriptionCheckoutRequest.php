<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object describing a recurring payment to set up
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderInterface::createSubscriptionCheckout()}.
 *
 * This is deliberately a separate DTO from {@see OneOffCheckoutRequest} rather
 * than the same one carrying an optional interval. The two have different
 * consequences -- one takes a payment, the other creates an ongoing obligation
 * that will take payments unattended until something cancels it -- and a
 * nullable field is a poor guard against calling the wrong one by accident.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\SubscriptionCheckoutRequestTest PHPUnit test case
 */
final readonly class SubscriptionCheckoutRequest implements Dto
{
    /**
     * Constructor
     *
     * @param int                   $amountMinorUnits  The amount to collect each interval, in the currency's minor units
     * @param string                $currency          ISO 4217 currency code, lower-case (for example `usd`)
     * @param int                   $intervalMonths    How many months between charges; 1 is monthly, 12 annual
     * @param string                $description       What the payer is subscribing to, shown on the hosted page
     * @param string                $successUrl        Where the provider returns the payer after the subscription is set up; arrival here is not proof the first payment succeeded -- only a verified webhook is
     * @param string                $cancelUrl         Where the provider returns the payer if they abandon the signup
     * @param ?string               $customerReference The provider's own id for a payer this host has transacted with before, if it has one; null creates a new customer on the provider's side
     * @param string                $clientReference   The host's own opaque reference, echoed back on every webhook about this subscription
     * @param array<string, string> $metadata          Additional host-side key/value context; never secret or personally identifying, since it is visible in the provider's dashboard
     */
    public function __construct(
        public readonly int $amountMinorUnits = 0,
        public readonly string $currency = '',
        public readonly int $intervalMonths = 1,
        public readonly string $description = '',
        public readonly string $successUrl = '',
        public readonly string $cancelUrl = '',
        public readonly ?string $customerReference = null,
        public readonly string $clientReference = '',
        public readonly array $metadata = [],
    ) {
    }
}
