<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Enough about a saved card to show the payer which one it is, and nothing more
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\CardSummaryTest PHPUnit test case
 */
final readonly class CardSummary implements Dto
{
    /**
     * Constructor
     *
     * @param string $brand    The card network, e.g. visa
     * @param string $last4    The last four digits
     * @param int    $expMonth Expiry month
     * @param int    $expYear  Expiry year
     */
    public function __construct(
        public readonly string $brand = '',
        public readonly string $last4 = '',
        public readonly int $expMonth = 0,
        public readonly int $expYear = 0,
    ) {
    }
}
