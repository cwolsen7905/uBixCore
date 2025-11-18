<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of pending platform users
 *
 * @see \Ubix\Repository\PendingPlatformUser\PendingPlatformUserSqlRepository This DTO is used by the pending platform user SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PendingPlatformUserOptionsTest PHPUnit test case
 */
final readonly class PendingPlatformUserOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?string $username The username (optional) (default: null)
     */
    public function __construct(
        public readonly ?string $username = null,
    ) {
    }
}
