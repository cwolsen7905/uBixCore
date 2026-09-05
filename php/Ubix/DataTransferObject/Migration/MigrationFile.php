<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Migration;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * One parsed migration file on disk. Built by
 * `MigrationFileParserService::parse()` from a `.sql` file under
 * `sql/migrations/`.
 *
 * Per `docs/standards/migrations.md` §2.1 the parsed header carries
 * the four required fields (Migration / Database / Description /
 * Author). The `body` is the SQL that follows the header. The
 * `checksum` is SHA-256 of the body bytes (NOT the header) — the
 * header carries metadata that may be amended after a migration
 * lands (e.g. an Author rename) without invalidating the lock-in.
 *
 * The `id` field is the migration's filename without `.sql`
 * extension, e.g. `20260505143045_pre_attribution_referrer_tables`.
 * The runner verifies this matches the `Migration:` header.
 *
 * `destructiveReason` carries the value of the optional
 * `Destructive:` 5th header line per §11.2. Non-null whenever the
 * file declares destructive intent; the parser refuses to build
 * this DTO with a null `destructiveReason` when the body matches
 * any §11.1 pattern.
 *
 * `requiresDbaReason` carries the value of the optional
 * `RequiresDBA:` header line per §11.8 — a header-declared (NOT
 * statement-detected) marker for migrations the automated runner
 * must not apply (heavy / online-DDL operations the MariaDB team
 * runs out-of-band). Orthogonal to `destructiveReason`; a migration
 * may carry both. Non-null whenever the file declares the marker.
 *
 * @see \Ubix\Tests\DataTransferObject\Migration\MigrationFileTest PHPUnit test case
 */
final readonly class MigrationFile implements Dto
{
    /**
     * Constructor
     *
     * @param string  $id                Filename without `.sql` extension; matches the `Migration:` header value
     * @param string  $targetDatabase    Target database name from the `Database:` header (e.g. `VSCASH`, `SYSTEMS`)
     * @param string  $description       One-line description from the `Description:` header (multi-line headers are joined with a single space)
     * @param string  $author            Author name from the `Author:` header
     * @param string  $body              SQL body (everything after the header); whitespace-trimmed at both ends
     * @param string  $checksum          SHA-256 hex digest of the `body` bytes
     * @param string  $filePath          Absolute path to the source file on disk
     * @param ?string $destructiveReason Free-form rationale from the optional `Destructive:` header; null when the migration is non-destructive
     * @param ?string $requiresDbaReason Free-form rationale from the optional `RequiresDBA:` header (§11.8); null when the migration is not DBA-gated
     * @param ?string $alterAckReason    Free-form rationale from the optional `AlterAck:` header (§11.9 — author vouches the pre-existing tables altered inline are small); null when absent
     */
    public function __construct(
        public string $id,
        public string $targetDatabase,
        public string $description,
        public string $author,
        public string $body,
        public string $checksum,
        public string $filePath,
        public ?string $destructiveReason = null,
        public ?string $requiresDbaReason = null,
        public ?string $alterAckReason = null,
    ) {
    }
}
