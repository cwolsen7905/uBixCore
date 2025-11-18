<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of admin users
 *
 * @see \Ubix\Repository\FanClubComment\AdminUserSqlRepository This DTO is used by the admin user SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\AdminUserOptionsTest PHPUnit test case
 */
final readonly class AdminUserOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int            $id               The admin user's ID (optional) (default: null)
     * @param ?string         $emailAddress     The admin user's email address (optional) (default: null)
     * @param int|string|null $notificationType The admin user's notification type - integers will filter by notification ID while strings will filter by notification name (optional) (default: null)
     * @param ?int            $limit            The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $emailAddress = null,
        public readonly int|string|null $notificationType = null,
        public readonly ?int $limit = null,
    ) {
    }
}
