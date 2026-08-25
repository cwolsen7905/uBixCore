<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Migration;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Per-database schema-drift report. Produced by
 * `SchemaDiffService::diffAll()`.
 *
 * Per `docs/standards/migrations.md` §9. Structured rather than a
 * single `unified-diff` string so the command can colour-code lines
 * and CI can post structured findings.
 *
 * `extraInLive` — lines present in the live `mariadb-dump` output but
 * NOT in the checked-in `sql/<DB>.sql` reference dump. These are
 * drift: a manual `ALTER TABLE`, an in-place edit, or a migration
 * applied without being recorded.
 *
 * `missingFromLive` — lines in the reference dump but NOT in the
 * live cluster. These are migrations that were merged to `sql/`
 * but never applied to the live cluster — `migrate:up` is the fix.
 *
 * @see \Ubix\Tests\DataTransferObject\Migration\SchemaDiffResultTest PHPUnit test case
 */
final readonly class SchemaDiffResult implements Dto
{
    /**
     * Constructor
     *
     * @param string   $database        Target database (e.g. `VSCASH`)
     * @param bool     $hasDrift        True when either of the line lists is non-empty
     * @param string[] $extraInLive     Lines present live but not in the reference dump
     * @param string[] $missingFromLive Lines present in the reference dump but not live
     * @param ?string  $errorMessage    Non-null when the comparison failed (e.g. mariadb-dump exited non-zero, reference file missing); presence implies `hasDrift = false` because the comparison itself was inconclusive
     */
    public function __construct(
        public string $database,
        public bool $hasDrift,
        public array $extraInLive,
        public array $missingFromLive,
        public ?string $errorMessage = null,
    ) {
    }
}
