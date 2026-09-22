<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * A payer to create on the provider, so saved cards and subscriptions have an owner
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createCustomer()}.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\CustomerRequestTest PHPUnit test case
 */
final readonly class CustomerRequest implements Dto
{
    /**
     * Constructor
     *
     * @param string                $email    The payer's email; the provider emails receipts here if receipts are on
     * @param string                $name     The payer's display name, as the provider's dashboard shows it
     * @param array<string, string> $metadata Host-side context; never secret or personally identifying beyond the above
     */
    public function __construct(
        public readonly string $email = '',
        public readonly string $name = '',
        public readonly array $metadata = [],
    ) {
    }
}
