<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use DateTimeInterface;
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
 * `$metadata` and `$createdAt` are populated when this describes a transfer read
 * back from the provider ({@see \Ubix\Service\Payment\PaymentProviderServiceInterface::listTransfers()}),
 * and are empty and null respectively on the result of creating one — the caller
 * of `createTransfer()` already knows both. They exist because a host reconciling
 * its own records against the provider's has nothing else to match on: the
 * metadata it set at creation is the only link from a provider transfer back to
 * whatever the host was doing.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\TransferResultTest PHPUnit test case
 */
final readonly class TransferResult implements Dto
{
    /**
     * Constructor
     *
     * @param string                $providerTransferId          The provider's id for the transfer
     * @param string                $destinationAccountReference The provider's id for the account that was paid
     * @param int                   $amountMinorUnits            How much was moved, in minor units, as a positive number
     * @param string                $currency                    ISO 4217 currency code, lower-case
     * @param array<string, string> $metadata                    The metadata stored on the transfer, when it was read back from the provider
     * @param ?DateTimeInterface    $createdAt                   When the provider created it, when it was read back
     */
    public function __construct(
        public readonly string $providerTransferId = '',
        public readonly string $destinationAccountReference = '',
        public readonly int $amountMinorUnits = 0,
        public readonly string $currency = '',
        public readonly array $metadata = [],
        public readonly ?DateTimeInterface $createdAt = null,
    ) {
    }
}
