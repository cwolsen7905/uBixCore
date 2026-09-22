<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * A recurring price to create once and reuse for every subscription at that price
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createRecurringPrice()}.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\RecurringPriceRequestTest PHPUnit test case
 */
final readonly class RecurringPriceRequest implements Dto
{
    /**
     * Constructor
     *
     * @param int                   $amountMinorUnits The amount per period, in minor units; never a float
     * @param string                $currency         ISO 4217 currency code, lower-case
     * @param int                   $intervalMonths   Billing period in months: 1 (monthly) or 12 (yearly)
     * @param string                $productName      What is being sold, as statements and receipts show it
     * @param array<string, string> $metadata         Host-side context stored on the price
     */
    public function __construct(
        public readonly int $amountMinorUnits = 0,
        public readonly string $currency = '',
        public readonly int $intervalMonths = 1,
        public readonly string $productName = '',
        public readonly array $metadata = [],
    ) {
    }
}
