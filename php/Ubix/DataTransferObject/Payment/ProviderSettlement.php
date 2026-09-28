<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for what a payment cost once the provider settled it
 *
 * Returned by {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::getSettlementForPayment()}.
 *
 * A provider takes its own fee out of a payment, and that fee is a fact the
 * provider reports rather than a rate anyone may apply. A host that needs to
 * know what a payment actually yielded -- to pay a creator the remainder, or to
 * state the cost honestly -- cannot compute it: published rates carry rounding,
 * per-transaction components, currency conversion and account-specific pricing,
 * so a number derived locally is a guess about someone else's charge. This
 * object carries the reported figures and nothing derived.
 *
 * `$feeMinorUnits` is positive -- the size of the fee, not a signed ledger
 * movement -- for the same reason {@see RefundResult} keeps its amount
 * positive: whether a fee is stored negative is the host's ledger convention,
 * not the provider's.
 *
 * **Settlement is not immediate.** A payment can succeed and be reported
 * without its fee being known, because the provider settles afterwards. That
 * state is represented by a null return from the service method rather than by
 * a zero fee here: zero is a real value a provider may report, and conflating
 * "nothing yet" with "nothing charged" is how an unsettled payment silently
 * becomes free.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\ProviderSettlementTest PHPUnit test case
 */
final readonly class ProviderSettlement implements Dto
{
    /**
     * Constructor
     *
     * @param string $providerPaymentReference The provider's id for the payment that settled
     * @param string $providerSettlementId     The provider's id for the settlement record itself, for tracing back
     * @param int    $feeMinorUnits            What the provider charged, in minor units, as a positive number
     * @param int    $netMinorUnits            What the payment yielded after that fee, in minor units, as the provider reports it
     * @param string $currency                 ISO 4217 currency code, lower-case -- the settlement currency, which may differ from the payment's
     */
    public function __construct(
        public readonly string $providerPaymentReference = '',
        public readonly string $providerSettlementId = '',
        public readonly int $feeMinorUnits = 0,
        public readonly int $netMinorUnits = 0,
        public readonly string $currency = '',
    ) {
    }
}
