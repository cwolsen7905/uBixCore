<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for opening an account the platform can pay money to
 *
 * Passed to {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::createConnectedAccount()}.
 *
 * Deliberately thin. Nothing here identifies a person: no legal name, date of
 * birth, government identifier, address or bank detail, because the provider
 * collects all of that directly from the account holder through its own hosted
 * flow. A host that could pass identity data here would end up holding it, and
 * the point of the seam is that it never can.
 *
 * `$email` is the only contact detail, and only so the provider can reach the
 * account holder about their own account; it is not required.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\ConnectedAccountRequestTest PHPUnit test case
 */
final readonly class ConnectedAccountRequest implements Dto
{
    /**
     * Constructor
     *
     * @param string                $country  ISO 3166-1 alpha-2 country the account holder is in; determines which rules the provider applies to them
     * @param ?string               $email    Where the provider may contact the account holder, or null to let it ask them
     * @param array<string, string> $metadata Host-side context stored on the account; never secret or personally identifying, since it is visible in the provider's dashboard
     */
    public function __construct(
        public readonly string $country = '',
        public readonly ?string $email = null,
        public readonly array $metadata = [],
    ) {
    }
}
