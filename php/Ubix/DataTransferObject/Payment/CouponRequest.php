<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * A discount to create once and apply to subscriptions
 *
 * Exactly one of `percentOff` and `amountOffMinorUnits` is set. `durationMonths`
 * null means the discount applies to the first invoice only; a number means
 * every invoice for that many months.
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createCoupon()}.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\CouponRequestTest PHPUnit test case
 */
final readonly class CouponRequest implements Dto
{
    /**
     * Constructor
     *
     * @param ?int                  $percentOff          Percentage off, 1-100; null when a fixed amount is taken off
     * @param ?int                  $amountOffMinorUnits Fixed amount off, in minor units; null when a percentage is taken off
     * @param ?string               $currency            ISO 4217 currency of a fixed amount, lower-case
     * @param ?int                  $durationMonths      Months it repeats for; null for the first invoice only
     * @param string                $name                What the payer sees on the invoice
     * @param array<string, string> $metadata            Host-side context stored on the coupon
     */
    public function __construct(
        public readonly ?int $percentOff = null,
        public readonly ?int $amountOffMinorUnits = null,
        public readonly ?string $currency = null,
        public readonly ?int $durationMonths = null,
        public readonly string $name = '',
        public readonly array $metadata = [],
    ) {
    }
}
