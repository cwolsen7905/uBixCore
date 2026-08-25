<?php

declare(strict_types=1);

namespace Ubix\Repository\SchemaMigration;

/**
 * Writes rows to `SYSTEMS.Schema_Migrations` — the master tracker of
 * applied migrations.
 *
 * Per `docs/standards/migrations.md` §3 + §4.1 step 4. Slice 2 of the
 * runner is the first consumer (the apply path); slice 4
 * (`migrate:reconcile`) reuses the same `save()` entry point with
 * `applied_by = manual:<unix-username>`.
 *
 * The writer takes primitives rather than a DTO because Ubix's
 * standards-test enforces "writer params must be Model types or
 * primitives"; lifting a DTO into a Model just to satisfy the
 * checker would distort the model layer.
 *
 * @see \Ubix\Tests\Repository\SchemaMigration\SchemaMigrationSqlRepositoryTest PHPUnit test case
 */
interface SchemaMigrationWriterInterface
{
    /**
     * Insert one row into `SYSTEMS.Schema_Migrations` for a
     * just-applied migration. Throws on duplicate-key collision so
     * a re-apply is surfaced to the caller.
     *
     * @param string $id             Migration ID — matches `MigrationFile::$id`
     * @param string $targetDatabase Target database from the file's `Database:` header
     * @param string $description    Description from the file's `Description:` header
     * @param string $checksum       SHA-256 hex digest of the body bytes at apply time
     * @param string $appliedBy      Actor identifier — see standard §3 for shape (e.g. `cli:olsenchristopher`, `ci:deploy-prod-…`, `manual:<username>+destructive-ack`)
     * @param int    $durationMs     Wall-clock duration of the apply, in milliseconds
     *
     * @return void
     */
    public function insert(
        string $id,
        string $targetDatabase,
        string $description,
        string $checksum,
        string $appliedBy,
        int $durationMs,
    ): void;
}
