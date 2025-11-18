<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of platform user payments
 *
 * @see \Ubix\Repository\PlatformUserPayment\PlatformUserPaymentSqlRepository This DTO is used by the platform user SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PlatformUserPaymentOptionsTest PHPUnit test case
 */
final readonly class PlatformUserPaymentOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $userId The platform user's ID (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $userId = null,
    ) {
    }
}
