<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\AppliedMigration;
use Ubix\DataTransferObject\Migration\MigrationStatusEntry;
use Ubix\Repository\SchemaMigration\SchemaMigrationReaderInterface as SchemaMigrationReader;

/**
 * Cross-references on-disk migration files against the
 * `SYSTEMS.Schema_Migrations` tracker and produces a row-per-file
 * status report.
 *
 * Per `docs/standards/migrations.md` §4.2 (`migrate:status`).
 *
 * `MigrationStatusEntry::$checksumMatches` is computed by
 * comparing the on-disk file's SHA-256 against the recorded
 * `Schema_Migrations.checksum`. Pending rows are always
 * `checksumMatches = true` (the column has no recorded value to
 * compare against); applied rows surface drift when the on-disk
 * file's checksum diverges from the recorded one. The `--verify`
 * flag on `migrate:status` is what makes drift fatal — the
 * service always populates the field.
 *
 * @see \Ubix\Tests\Service\Migration\MigrationStatusServiceTest PHPUnit test case
 */
final class MigrationStatusService
{
    /**
     * Constructor
     *
     * @param Logger                      $logger                PSR-3 logger
     * @param MigrationFileScannerService $fileScanner           Scans `sql/migrations/` for parsed file DTOs
     * @param SchemaMigrationReader       $schemaMigrationReader Reads applied rows from `SYSTEMS.Schema_Migrations`
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private MigrationFileScannerService $fileScanner,
        private SchemaMigrationReader $schemaMigrationReader,
    ) {
    }

    /**
     * Build the status report. Returns one entry per on-disk
     * migration, joined to its `Schema_Migrations` row when one
     * exists.
     *
     * @param ?string $databaseFilter When non-null, only entries with this `target_database` are returned
     *
     * @return MigrationStatusEntry[]
     */
    public function getStatus(?string $databaseFilter = null): array
    {
        $files   = $this->fileScanner->scan();
        $applied = $this->indexApplied();

        $entries = [];
        foreach ($files as $file) {
            if ($databaseFilter !== null && $file->targetDatabase !== $databaseFilter) {
                continue;
            }
            $appliedRow      = $applied[$file->id] ?? null;
            $checksumMatches = $appliedRow === null ? true : $appliedRow->checksum === $file->checksum;
            $entries[]       = new MigrationStatusEntry(
                id:              $file->id,
                targetDatabase:  $file->targetDatabase,
                description:     $file->description,
                isApplied:       $appliedRow !== null,
                checksumMatches: $checksumMatches,
                isDestructive:   $file->destructiveReason !== null,
                appliedAt:       $appliedRow?->appliedAt,
                appliedBy:       $appliedRow?->appliedBy,
            );
        }

        return $entries;
    }

    /**
     * Index every applied migration by its ID for O(1) lookup
     * during the cross-reference loop.
     *
     * @return array<string, AppliedMigration>
     */
    private function indexApplied(): array
    {
        if (! $this->schemaMigrationReader->trackerTableExists()) {
            // Bootstrap not yet run; nothing has been applied.
            // Every on-disk file shows as pending.
            return [];
        }

        $indexed = [];
        foreach ($this->schemaMigrationReader->getAll() as $row) {
            $indexed[$row->id] = $row;
        }
        return $indexed;
    }
}
