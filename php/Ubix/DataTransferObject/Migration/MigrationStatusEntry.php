<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Migration;

use DateTimeInterface;
use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * One row of `bin/ubix migrate:status` output — a per-migration
 * cross-reference of the on-disk file against the
 * `SYSTEMS.Schema_Migrations` row (if any).
 *
 * Per `docs/standards/migrations.md` §4.2.
 *
 * @see \Ubix\Tests\DataTransferObject\Migration\MigrationStatusEntryTest PHPUnit test case
 */
final readonly class MigrationStatusEntry implements Dto
{
    /**
     * Constructor
     *
     * @param string             $id              Migration ID — filename without `.sql` extension
     * @param string             $targetDatabase  Target database from the file's `Database:` header
     * @param string             $description     Description from the file's `Description:` header
     * @param bool               $isApplied       True when a `SYSTEMS.Schema_Migrations` row exists for this id
     * @param bool               $checksumMatches Whether the on-disk file's checksum matches the recorded value. True when not applied, true when applied + matches, false when applied + drift detected. Slice 3 (`migrate:status --verify`) is the consumer; in slice 1 the value is always true for pending and unknown for applied (we don't recompute in slice 1).
     * @param bool               $isDestructive   True when the on-disk file declared a `Destructive:` header per `migrations.md` §11.2 — surfaces the 🔥 marker in `migrate:status` output so reviewers see destructive pending work at a glance
     * @param ?DateTimeInterface $appliedAt       When the migration was applied; null when pending
     * @param ?string            $appliedBy       Actor that applied the migration; null when pending
     */
    public function __construct(
        public string $id,
        public string $targetDatabase,
        public string $description,
        public bool $isApplied,
        public bool $checksumMatches,
        public bool $isDestructive,
        public ?DateTimeInterface $appliedAt = null,
        public ?string $appliedBy = null,
    ) {
    }
}
