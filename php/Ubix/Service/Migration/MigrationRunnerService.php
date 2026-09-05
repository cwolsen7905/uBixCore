<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\Repository\SchemaMigration\SchemaMigrationReaderInterface as SchemaMigrationReader;

/**
 * Glue between the migration runner's Console command and its
 * supporting repos. Owns:
 *
 * - The pending-list builder (scanner ⨯ tracker) — needed in two
 *   places: `migrate:up` and slice-3's `migrate:status --verify`.
 * - The advisory-lock lifecycle so concurrent `migrate:up` runs
 *   from multiple deploy pipelines serialise on a single MariaDB
 *   mutex.
 *
 * Lives behind the service layer because the codebase's standards
 * test enforces "repositories are only injected into services" —
 * commands consume this service instead of the underlying readers
 * directly.
 *
 * @see \Ubix\Tests\Service\Migration\MigrationRunnerServiceTest PHPUnit test case
 */
final class MigrationRunnerService
{
    /**
     * Advisory-lock name used to serialise concurrent runners.
     * Same name lives in the `migrations.md` standard; keep in
     * sync if either changes.
     */
    public const string ADVISORY_LOCK_NAME = 'ubix_migrations';

    /**
     * Default seconds to wait for the advisory lock before bailing.
     * 30s is generous for a single deploy pipeline.
     */
    public const int DEFAULT_ADVISORY_LOCK_TIMEOUT_SECONDS = 30;

    /**
     * Constructor
     *
     * @param Logger                      $logger      PSR-3 logger
     * @param MigrationFileScannerService $fileScanner Scans `sql/migrations/` for parsed file DTOs
     * @param SchemaMigrationReader       $reader      Reads tracker rows + table existence + advisory lock
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private MigrationFileScannerService $fileScanner,
        private SchemaMigrationReader $reader,
    ) {
    }

    /**
     * Look up a single on-disk migration by ID. Returns null when
     * no file matches. Backs `migrate:reconcile`'s file-existence
     * check.
     *
     * @param string $id Migration ID — filename without `.sql`
     *
     * @return ?MigrationFile Parsed file or null when missing
     */
    public function getMigrationById(string $id): ?MigrationFile
    {
        foreach ($this->fileScanner->scan() as $file) {
            if ($file->id === $id) {
                return $file;
            }
        }
        return null;
    }

    /**
     * Whether a `Schema_Migrations` row with the given ID already
     * exists. Backs `migrate:reconcile`'s already-applied guard.
     *
     * @param string $id Migration ID
     *
     * @return bool True when an applied row is on file
     */
    public function isMigrationApplied(string $id): bool
    {
        if (! $this->reader->trackerTableExists()) {
            return false;
        }
        return $this->reader->getById($id) !== null;
    }

    /**
     * Build the pending-migration list. Bootstrap state — when the
     * tracker table doesn't exist — surfaces every on-disk file as
     * pending; the bootstrap migration sorts first by zero-prefix.
     *
     * @param ?string $databaseFilter Restrict to one target database
     *
     * @return MigrationFile[] Pending migrations in filename (timestamp) order
     */
    public function getPendingMigrations(?string $databaseFilter = null): array
    {
        $files = $this->fileScanner->scan();
        if ($databaseFilter !== null) {
            $filtered = [];
            foreach ($files as $file) {
                if ($file->targetDatabase === $databaseFilter) {
                    $filtered[] = $file;
                }
            }
            $files = $filtered;
        }

        if (! $this->reader->trackerTableExists()) {
            return $files;
        }

        $appliedIds = [];
        foreach ($this->reader->getAll() as $row) {
            $appliedIds[$row->id] = true;
        }

        $pending = [];
        foreach ($files as $file) {
            if (! isset($appliedIds[$file->id])) {
                $pending[] = $file;
            }
        }
        return $pending;
    }

    /**
     * Acquire the migration advisory lock. Returns true on success,
     * false when the lock is held by another connection or the
     * timeout elapsed.
     *
     * @param int $timeoutSeconds How long to wait for the lock; defaults to the standard's 30s
     *
     * @return bool True when the lock was acquired
     */
    public function acquireAdvisoryLock(int $timeoutSeconds = self::DEFAULT_ADVISORY_LOCK_TIMEOUT_SECONDS): bool
    {
        return $this->reader->acquireMigrationLock($timeoutSeconds);
    }

    /**
     * Release the migration advisory lock. Best-effort.
     *
     * @return void
     */
    public function releaseAdvisoryLock(): void
    {
        $this->reader->releaseMigrationLock();
    }
}
