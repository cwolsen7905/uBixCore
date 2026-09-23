<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * The provider's current view of an account the platform can pay money to
 *
 * Returned by {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::getConnectedAccount()}.
 *
 * The provider is the system of record for whether an account may be paid, and
 * that answer changes without the host doing anything: verification completes
 * overnight, a document expires, a review restricts an account years in. So
 * this is a reading taken at a moment, not a fact a host may cache and trust
 * indefinitely -- a host mirrors it for fast queries but re-reads before it
 * moves money.
 *
 * `$payoutsEnabled` and `$chargesEnabled` are the only two flags worth acting
 * on. `$detailsSubmitted` says the holder finished the form, which is not the
 * same as being approved, and is useful only for telling someone whether to
 * send them back into it. `$requirementsDue` names what the provider is still
 * waiting for, in the provider's own vocabulary, for a host that wants to say
 * more than "not yet".
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\ConnectedAccountTest PHPUnit test case
 */
final readonly class ConnectedAccount implements Dto
{
    /**
     * Constructor
     *
     * @param string             $providerAccountId The provider's id for the account
     * @param bool               $payoutsEnabled    Whether the provider will currently pay this account out
     * @param bool               $chargesEnabled    Whether the provider will currently accept charges on this account's behalf
     * @param bool               $detailsSubmitted  Whether the holder has completed the provider's onboarding form; completing it is not the same as being approved
     * @param array<int, string> $requirementsDue   What the provider is still waiting for, in its own vocabulary; empty when nothing is outstanding
     * @param ?string            $disabledReason    Why the provider has disabled the account, in its own vocabulary, or null when it has not
     */
    public function __construct(
        public readonly string $providerAccountId = '',
        public readonly bool $payoutsEnabled = false,
        public readonly bool $chargesEnabled = false,
        public readonly bool $detailsSubmitted = false,
        public readonly array $requirementsDue = [],
        public readonly ?string $disabledReason = null,
    ) {
    }
}
