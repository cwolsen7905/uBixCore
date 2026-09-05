<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Migration;

use DateTimeInterface;
use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * One row of `SYSTEMS.Schema_Migrations` — recorded when a
 * migration is applied (or reconciled).
 *
 * Per `docs/standards/migrations.md` §3 the master tracker carries
 * the migration `id` (filename without extension), the target
 * database, the description, the SHA-256 checksum of the file's
 * body (used by `migrate:status --verify` to detect after-apply
 * edits), the apply timestamp, the actor (`cli:<unix-username>` /
 * `ci:<gitlab-user-login>` / `manual:<username>+destructive-ack`),
 * and the duration.
 *
 * @see \Ubix\Tests\DataTransferObject\Migration\AppliedMigrationTest PHPUnit test case
 */
final readonly class AppliedMigration implements Dto
{
    /**
     * Constructor
     *
     * @param string            $id             Migration ID — filename without `.sql` extension
     * @param string            $targetDatabase Target database (`VSCASH`, `SYSTEMS`, `ntl_db`, `flirt4free`, etc.)
     * @param string            $description    Description from the `Description:` header
     * @param string            $checksum       SHA-256 hex digest of the migration file's body at the moment of apply
     * @param DateTimeInterface $appliedAt      When the migration finished applying
     * @param string            $appliedBy      Actor identifier — see standard §3 for shape
     * @param int               $durationMs     Wall-clock duration of the apply, in milliseconds
     */
    public function __construct(
        public string $id,
        public string $targetDatabase,
        public string $description,
        public string $checksum,
        public DateTimeInterface $appliedAt,
        public string $appliedBy,
        public int $durationMs,
    ) {
    }
}
