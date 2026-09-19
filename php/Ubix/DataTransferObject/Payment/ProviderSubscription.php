<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the provider's own view of a recurring payment
 *
 * Returned by {@see \Ubix\Service\Payment\PaymentProviderInterface::fetchSubscription()}
 * and {@see \Ubix\Service\Payment\PaymentProviderInterface::cancelSubscription()}.
 *
 * `$status` is deliberately a plain string in the *provider's* vocabulary
 * rather than an enum of this framework's choosing. The provider is the system
 * of record for whether money is still being collected, and an enum here would
 * force every provider's status set into one shape -- silently mapping an
 * unfamiliar status onto a familiar-looking one, which for payment state is the
 * kind of quiet wrongness that grants access to someone who stopped paying. A
 * host maps this string to its own status explicitly, and decides what an
 * unrecognised value means (the safe answer being "not entitled").
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\ProviderSubscriptionTest PHPUnit test case
 */
final readonly class ProviderSubscription implements Dto
{
    /**
     * Constructor
     *
     * @param string $providerSubscriptionId        The provider's id for the recurring payment
     * @param string $providerCustomerId            The provider's id for the payer
     * @param string $status                        The provider's own status string, unmapped; a host translates it and treats anything it does not recognise as not-entitled
     * @param int    $currentPeriodEndUnixTimestamp When the currently-paid-for period ends; 0 when the provider does not report one
     * @param ?int   $canceledAtUnixTimestamp       When it was cancelled, or null if it has not been; a cancelled subscription may still be within a paid period, so this is not the same question as "is it active"
     */
    public function __construct(
        public readonly string $providerSubscriptionId = '',
        public readonly string $providerCustomerId = '',
        public readonly string $status = '',
        public readonly int $currentPeriodEndUnixTimestamp = 0,
        public readonly ?int $canceledAtUnixTimestamp = null,
    ) {
    }
}
