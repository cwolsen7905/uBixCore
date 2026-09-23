<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a transfer the provider has accepted
 *
 * Returned by {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createTransfer()}.
 *
 * Accepted is not the same as arrived. The provider has moved the money out of
 * the platform's balance and onto the connected account's; when it reaches the
 * account holder's bank is the provider's own payout schedule, which this seam
 * does not speak for.
 *
 * `$amountMinorUnits` is positive, as it is on {@see RefundResult}: this
 * records the size of a movement, and whether a host writes it as a debit is
 * the host's ledger convention, not the provider's.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\TransferResultTest PHPUnit test case
 */
final readonly class TransferResult implements Dto
{
    /**
     * Constructor
     *
     * @param string $providerTransferId          The provider's id for the transfer
     * @param string $destinationAccountReference The provider's id for the account that was paid
     * @param int    $amountMinorUnits            How much was moved, in minor units, as a positive number
     * @param string $currency                    ISO 4217 currency code, lower-case
     */
    public function __construct(
        public readonly string $providerTransferId = '',
        public readonly string $destinationAccountReference = '',
        public readonly int $amountMinorUnits = 0,
        public readonly string $currency = '',
    ) {
    }
}
