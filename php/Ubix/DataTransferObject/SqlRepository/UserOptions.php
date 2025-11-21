<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of users
 *
 * @see \Ubix\Repository\User\UserSqlRepository This DTO is used by the user SQL repository
 */
final readonly class UserOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id       The user's ID (optional) (default: null)
     * @param ?string $username The user's username (optional) (default: null)
     * @param ?string $email    The user's email address (optional) (default: null)
     * @param ?int    $limit    The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $username = null,
        public readonly ?string $email = null,
        public readonly ?int $limit = null,
    ) {
    }
}
