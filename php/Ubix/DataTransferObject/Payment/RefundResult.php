<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a refund the provider has accepted
 *
 * Returned by {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::refundPayment()}.
 *
 * `$amountMinorUnits` is positive here -- it is the size of the refund, not a
 * signed ledger movement. A host that records refunds as negative rows against
 * the original charge applies that sign itself, because whether a refund is
 * negative depends on the host's ledger conventions, not the provider's.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\RefundResultTest PHPUnit test case
 */
final readonly class RefundResult implements Dto
{
    /**
     * Constructor
     *
     * @param string $providerRefundId         The provider's id for the refund
     * @param string $providerPaymentReference The provider's id for the payment that was refunded
     * @param int    $amountMinorUnits         How much was refunded, in minor units, as a positive number
     * @param string $currency                 ISO 4217 currency code, lower-case
     */
    public function __construct(
        public readonly string $providerRefundId = '',
        public readonly string $providerPaymentReference = '',
        public readonly int $amountMinorUnits = 0,
        public readonly string $currency = '',
    ) {
    }
}
