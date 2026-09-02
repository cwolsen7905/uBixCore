<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Options for querying tiers
 *
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\TierOptionsTest PHPUnit test case
 */
final readonly class TierOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id         Restrict to one tier id
     * @param ?int    $creatorId  Restrict to one creator
     * @param ?string $status     Restrict to one TierStatus value
     * @param bool    $activeOnly Shorthand for status=active
     * @param ?int    $limit      Maximum number of rows
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $creatorId = null,
        public readonly ?string $status = null,
        public readonly bool $activeOnly = false,
        public readonly ?int $limit = null,
    ) {
    }
}
