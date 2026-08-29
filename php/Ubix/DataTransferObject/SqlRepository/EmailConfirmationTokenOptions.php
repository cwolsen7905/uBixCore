<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of email confirmation tokens
 *
 * @see \Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenSqlRepository This DTO is used by the email confirmation token SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\EmailConfirmationTokenOptionsTest PHPUnit test case
 */
final readonly class EmailConfirmationTokenOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id         The token's ID (optional) (default: null)
     * @param ?int    $userId     The token's user ID (optional) (default: null)
     * @param ?string $token      The token string (optional) (default: null)
     * @param bool    $activeOnly Only match unused, unexpired tokens (optional) (default: false)
     * @param ?int    $limit      The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $userId = null,
        public readonly ?string $token = null,
        public readonly bool $activeOnly = false,
        public readonly ?int $limit = null,
    ) {
    }
}
