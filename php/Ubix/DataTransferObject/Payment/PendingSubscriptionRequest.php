<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * A subscription to create awaiting its first payment, confirmed in the page
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createPendingSubscription()}.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\PendingSubscriptionRequestTest PHPUnit test case
 */
final readonly class PendingSubscriptionRequest implements Dto
{
    /**
     * Constructor
     *
     * @param string                $customerReference The provider's customer id
     * @param string                $priceReference    The provider's recurring price id
     * @param array<string, string> $metadata          Host-side context stored on the subscription and echoed on every invoice webhook
     */
    public function __construct(
        public readonly string $customerReference = '',
        public readonly string $priceReference = '',
        public readonly array $metadata = [],
    ) {
    }
}
