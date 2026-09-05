<?php

declare(strict_types=1);

namespace Ubix\Repository\SchemaMigration;

use Ubix\DataTransferObject\Migration\AppliedMigration;

/**
 * Reads rows from `SYSTEMS.Schema_Migrations` — the master tracker
 * of applied migrations.
 *
 * Per `docs/standards/migrations.md` §3. Slice 1 of the runner only
 * needs read access; the writer interface lands in slice 2 alongside
 * the apply path.
 *
 * @see \Ubix\Tests\Repository\SchemaMigration\SchemaMigrationSqlRepositoryTest PHPUnit test case
 */
interface SchemaMigrationReaderInterface
{
    /**
     * Return every applied migration row, ordered by `applied_at`
     * ascending.
     *
     * @return AppliedMigration[]
     */
    public function getAll(): array;

    /**
     * Look up a single applied migration by its ID (filename without
     * `.sql`). Returns null when the row does not exist (i.e. the
     * migration is pending or has never run).
     *
     * @param string $id Migration ID
     *
     * @return ?AppliedMigration
     */
    public function getById(string $id): ?AppliedMigration;

    /**
     * Whether the `SYSTEMS.Schema_Migrations` table itself exists.
     * Used by the runner's bootstrap path: when the table is
     * missing, the runner runs the `00000000000000_init_schema_migrations`
     * file directly without going through the normal apply flow.
     *
     * @return bool
     */
    public function trackerTableExists(): bool;

    /**
     * Whether an arbitrary table exists in the live cluster.
     * Backs the `migrate:up` pre-flight `CREATE TABLE` clash check
     * per `migrations.md` §4.1 step 2 — when a target object
     * already exists the runner aborts with the §8 manual-apply
     * message that suggests `migrate:reconcile`.
     *
     * Lives on the migrations reader (rather than a generic
     * `InformationSchema*` repo) because the migrations subsystem
     * is the only consumer; spinning up a parallel repo for one
     * call site would be unjustified ceremony.
     *
     * @param string $schema MySQL schema name (e.g. `VSCASH`)
     * @param string $table  Table name (e.g. `Pre_Attribution_Referrers`)
     *
     * @return bool True when the table exists in `INFORMATION_SCHEMA.TABLES`
     */
    public function tableExists(string $schema, string $table): bool;

    /**
     * Acquire the migration advisory lock per `migrations.md`
     * §4.1 — `GET_LOCK('ubix_migrations', $timeoutSeconds)`.
     *
     * Lives on the reader (rather than a dedicated repo) because
     * a separate `*Reader/Writer` pair would force boilerplate
     * around `query()` + `Options` DTO that has no semantic role
     * for an advisory-lock primitive. The lock is part of the
     * Schema_Migrations subsystem and shares its connection.
     *
     * Connection-scoped — MariaDB releases the lock when the PHP
     * process exits.
     *
     * @param int $timeoutSeconds Seconds to wait before giving up
     *
     * @return bool True when the lock was acquired
     */
    public function acquireMigrationLock(int $timeoutSeconds): bool;

    /**
     * Release the migration advisory lock. Best-effort —
     * connection drop releases held locks anyway.
     *
     * @return void
     */
    public function releaseMigrationLock(): void;
}
