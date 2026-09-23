<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for moving money from the platform to a connected account
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createTransfer()}.
 *
 * `$idempotencyKey` is required rather than optional, which no other request
 * type here does. A retried checkout shows someone a second payment form; a
 * retried transfer pays someone twice with money that is already gone. The
 * caller owns the key because only the caller knows what "the same transfer"
 * means -- typically one owner within one payout period -- and a key invented
 * per call would be no protection at all.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\TransferRequestTest PHPUnit test case
 */
final readonly class TransferRequest implements Dto
{
    /**
     * Constructor
     *
     * @param string                $destinationAccountReference The provider's id for the account being paid
     * @param int                   $amountMinorUnits            How much to move, in minor units, as a positive number
     * @param string                $currency                    ISO 4217 currency code, lower-case
     * @param string                $idempotencyKey              Caller-owned key identifying this transfer; replaying it must not move money twice
     * @param string                $description                 What this transfer is for, as the account holder will see it
     * @param array<string, string> $metadata                    Host-side context stored on the transfer; never secret or personally identifying
     */
    public function __construct(
        public readonly string $destinationAccountReference = '',
        public readonly int $amountMinorUnits = 0,
        public readonly string $currency = '',
        public readonly string $idempotencyKey = '',
        public readonly string $description = '',
        public readonly array $metadata = [],
    ) {
    }
}
