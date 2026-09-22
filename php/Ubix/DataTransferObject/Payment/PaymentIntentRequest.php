<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * A single payment to confirm in the page (no hosted page)
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createPaymentIntent()}.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\PaymentIntentRequestTest PHPUnit test case
 */
final readonly class PaymentIntentRequest implements Dto
{
    /**
     * Constructor
     *
     * @param int                   $amountMinorUnits  The amount, in minor units; never a float
     * @param string                $currency          ISO 4217 currency code, lower-case
     * @param string                $description       What the payer is paying for
     * @param ?string               $customerReference The provider's customer id, so the payment appears in their history; null for none
     * @param array<string, string> $metadata          Host-side context echoed on the payment's webhooks
     */
    public function __construct(
        public readonly int $amountMinorUnits = 0,
        public readonly string $currency = '',
        public readonly string $description = '',
        public readonly ?string $customerReference = null,
        public readonly array $metadata = [],
    ) {
    }
}
