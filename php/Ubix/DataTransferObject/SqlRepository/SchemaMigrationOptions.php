<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Options DTO for `SchemaMigrationSqlRepository`.
 *
 * Slice 1 of the migration runner consumes only `id` (single-row
 * lookups) and `targetDatabase` (per-DB filtering for
 * `migrate:status --database=…`). `limit` is wired for the same
 * `LIMIT 1` shape every other Ubix SQL repository uses.
 *
 * @see \Ubix\Repository\SchemaMigration\SchemaMigrationSqlRepository This DTO is used by the schema migration SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\SchemaMigrationOptionsTest PHPUnit test case
 */
final readonly class SchemaMigrationOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?string $id             Filter to a single migration ID
     * @param ?string $targetDatabase Filter to one target database
     * @param ?int    $limit          The query's LIMIT value
     */
    public function __construct(
        public ?string $id = null,
        public ?string $targetDatabase = null,
        public ?int $limit = null,
    ) {
    }
}
