<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of platform user two factor authentication (2fa) entries
 *
 * @see \Ubix\Repository\PlatformUserTwoFactorAuthentication\PlatformUserTwoFactorAuthenticationSqlRepository This DTO is used by the platform user two factor authentication (2fa) entries SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PlatformUserTwoFactorAuthenticationOptionsTest PHPUnit test case
 */
final readonly class PlatformUserTwoFactorAuthenticationOptions implements Dto
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
