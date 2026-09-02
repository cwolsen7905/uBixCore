<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Options for querying password reset tokens
 *
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PasswordResetTokenOptionsTest PHPUnit test case
 */
final readonly class PasswordResetTokenOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id         Restrict to one token id
     * @param ?string $tokenHash  Restrict to one token hash
     * @param ?int    $userId     Restrict to tokens issued to one user
     * @param bool    $activeOnly Only unused, unexpired tokens
     * @param ?int    $limit      Maximum number of rows
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $tokenHash = null,
        public readonly ?int $userId = null,
        public readonly bool $activeOnly = false,
        public readonly ?int $limit = null,
    ) {
    }
}
