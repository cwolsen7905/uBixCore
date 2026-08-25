<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\MigrationFile;

/**
 * Parses a single migration file into a `MigrationFile` DTO.
 *
 * Per `docs/standards/migrations.md` §2.1, every migration file
 * begins with a structured 4-line comment header:
 *
 * ```
 * -- Migration: 20260505143045_pre_attribution_referrer_tables
 * -- Database: VSCASH
 * -- Description: Two lookup tables for the Pre-Attribution chain.
 * -- Author: Christopher W. Olsen
 * ```
 *
 * Optional 5th `Destructive:` line is required when the body
 * matches any pattern in `migrations.md` §11.1 (DROP / TRUNCATE /
 * RENAME / DROP COLUMN / MODIFY / unbounded DELETE). The parser
 * delegates that check to `DestructiveStatementDetectorService`;
 * a missing header on a destructive body fails parse with a quoted
 * line number per §11.2. Continuation lines beneath each header
 * (e.g. wrapped Description) are joined into the parent value with
 * a single space.
 *
 * An optional `RequiresDBA:` line (§11.8) is captured verbatim into
 * `requiresDbaReason` — a purely header-declared marker (no body
 * detection) for migrations the automated runner must not apply.
 * `UpCommand` aborts on it for every tier.
 *
 * Anything after the header block (until EOF) is treated as the
 * SQL body; whitespace-trimmed at both ends. The body's SHA-256 is
 * computed and stored on the resulting DTO; the checksum is what
 * `migrate:status --verify` compares against the recorded value in
 * `Schema_Migrations`.
 *
 * Bootstrap migration `00000000000000_init_schema_migrations`
 * is parsed identically — its `00000000000000_` prefix is reserved
 * for the runner's special-case bootstrap path but the file shape
 * is the same.
 *
 * @see \Ubix\Tests\Service\Migration\MigrationFileParserServiceTest PHPUnit test case
 */
final class MigrationFileParserService
{
    /**
     * The complete header vocabulary. Only these keys start a header line —
     * a continuation line whose text happens to contain `word:` (e.g.
     * "`migrate:reconcile.`" inside a `RequiresDBA:` note) must NOT be
     * misparsed as a fresh header, which silently truncated the reason the
     * §11.8 hold banner shows operators (Claude review of 04d1fe2b,
     * finding 2).
     */
    private const array KNOWN_HEADERS = ['AlterAck', 'Author', 'Database', 'Description', 'Destructive', 'Migration', 'RequiresDBA'];

    /**
     * Required header field names, in the canonical order they
     * appear in a well-formed migration file.
     */
    private const array REQUIRED_HEADERS = ['Migration', 'Database', 'Description', 'Author'];

    /**
     * Migrations with an ID at or after this cutoff must acknowledge DDL on
     * pre-existing tables (§11.9: `RequiresDBA:` or `AlterAck:` header).
     * Older applied migrations are grandfathered — editing them to add
     * headers would be pointless churn.
     */
    private const string HOT_TABLE_ENFORCEMENT_SINCE = '20260730000000';

    /**
     * Constructor
     *
     * @param Logger                              $logger              PSR-3 logger
     * @param DestructiveStatementDetectorService $destructiveDetector Scans bodies for §11.1 destructive patterns
     * @param HotTableAlterDetectorService        $hotTableDetector    Scans bodies for §11.9 DDL on pre-existing tables
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private DestructiveStatementDetectorService $destructiveDetector,
        private HotTableAlterDetectorService $hotTableDetector,
    ) {
    }

    /**
     * Parse the migration file at `$filePath` into a `MigrationFile`
     * DTO.
     *
     * @param string $filePath Absolute path to the migration file
     *
     * @return MigrationFile
     *
     * @throws InvalidArgumentException When the file is missing, the header is malformed, the `Migration:` header doesn't match the filename, or any required header is absent
     */
    public function parse(string $filePath): MigrationFile
    {
        if (! is_readable($filePath)) {
            throw new InvalidArgumentException(sprintf('Migration file `%s` is not readable.', $filePath));
        }

        $contents = file_get_contents($filePath);
        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Migration file `%s` could not be read.', $filePath));
        }

        // Normalize line endings so the +1-per-line $byteOffset
        // arithmetic below matches the actual delimiter width;
        // also stabilizes the SHA-256 checksum across editors.
        $contents = str_replace(["\r\n", "\r"], "\n", $contents);

        $expectedId = $this->idFromFilePath($filePath);
        $headers    = [];
        $bodyStart  = 0;

        $lines      = explode("\n", $contents);
        $lineCount  = count($lines);
        $byteOffset = 0;
        $currentKey = null;

        for ($i = 0; $i < $lineCount; $i++) {
            $line        = $lines[$i];
            $byteOffset += strlen($line) + 1; // +1 for the line break we split on

            // Header lines look like `-- Key: value` for a KNOWN key, or
            // `--              continuation` (any other comment row while a
            // header is open — colons in continuation text are fine).
            if (! str_starts_with($line, '--')) {
                // First non-comment line ends the header block;
                // body starts at the offset of THIS line.
                $bodyStart = $byteOffset - strlen($line) - 1;
                break;
            }

            $stripped = ltrim(substr($line, 2));

            if ($stripped === '') {
                // Blank comment line inside the header — treat as
                // a separator; body still hasn't started.
                $currentKey = null;
                continue;
            }

            if (preg_match('/^([A-Za-z][A-Za-z0-9_-]*):\s*(.*)$/', $stripped, $match) === 1 && in_array($match[1], self::KNOWN_HEADERS, true)) {
                $currentKey           = $match[1];
                $headers[$currentKey] = trim($match[2]);
            } elseif ($currentKey !== null) {
                // The `?? ''` arm is runtime-unreachable ($currentKey is only
                // ever set in lockstep with $headers[$currentKey], two branches
                // up) — it exists because PHPStan's offset analysis cannot
                // carry that invariant across loop iterations.
                $headers[$currentKey] = trim(($headers[$currentKey] ?? '') . ' ' . $stripped);
            }
            // Other comment lines (no `Key:` and no current
            // continuation context) are ignored — e.g. legacy
            // commentary above the structured header.
            $bodyStart = $byteOffset; // Body starts AFTER this line if no non-comment line follows
        }

        // Validate required headers
        foreach (self::REQUIRED_HEADERS as $required) {
            if (! isset($headers[$required]) || $headers[$required] === '') {
                throw new InvalidArgumentException(sprintf(
                    'Migration file `%s` is missing required header `%s:`.',
                    $filePath,
                    $required,
                ));
            }
        }

        // Validate Migration header matches filename
        if ($headers['Migration'] !== $expectedId) {
            throw new InvalidArgumentException(sprintf(
                'Migration file `%s` declares `Migration: %s` but the filename suggests `%s`.',
                $filePath,
                $headers['Migration'],
                $expectedId,
            ));
        }

        $body     = trim(substr($contents, $bodyStart));
        $checksum = hash('sha256', $body);

        $destructiveReason = isset($headers['Destructive']) && $headers['Destructive'] !== '' ? $headers['Destructive'] : null;
        $requiresDbaReason = isset($headers['RequiresDBA']) && $headers['RequiresDBA'] !== '' ? $headers['RequiresDBA'] : null;
        $alterAckReason    = isset($headers['AlterAck']) && $headers['AlterAck'] !== '' ? $headers['AlterAck'] : null;

        $destructiveMatches = $this->destructiveDetector->detect($body);
        if ($destructiveMatches !== [] && $destructiveReason === null) {
            $rendered = [];
            foreach ($destructiveMatches as $match) {
                $rendered[] = sprintf('%s on line %d', $match->kind->value, $match->lineNumber);
            }
            throw new InvalidArgumentException(sprintf(
                'Migration `%s` contains destructive statement(s) (%s) but lacks a `Destructive:` header. Either soften the change or document the rationale per docs/standards/migrations.md §11.2.',
                $headers['Migration'],
                implode(', ', $rendered),
            ));
        }

        //  §11.9: inline DDL against a pre-existing table replays single-
        //  threaded on replicas and can stall replication (2026-07-29 BILLING
        //  incident). Newer migrations must route it via RequiresDBA (out-of-
        //  band pt-online-schema-change) or vouch the table is small (AlterAck).
        if ($headers['Migration'] >= self::HOT_TABLE_ENFORCEMENT_SINCE && $requiresDbaReason === null && $alterAckReason === null) {
            $hotTableOffenders = $this->hotTableDetector->detect($body);
            if ($hotTableOffenders !== []) {
                throw new InvalidArgumentException(sprintf(
                    'Migration `%s` alters pre-existing table(s) (%s) with no `RequiresDBA:` or `AlterAck:` header. Large-table DDL stalls replication — route it via `RequiresDBA:` (applied out-of-band with pt-online-schema-change) or, if every listed table is verifiably small, add `AlterAck: <why inline is safe>` per docs/standards/migrations.md §11.9.',
                    $headers['Migration'],
                    implode(', ', $hotTableOffenders),
                ));
            }
        }

        return new MigrationFile(
            id:                $headers['Migration'],
            targetDatabase:    $headers['Database'],
            description:       $headers['Description'],
            author:            $headers['Author'],
            body:              $body,
            checksum:          $checksum,
            filePath:          $filePath,
            destructiveReason: $destructiveReason,
            requiresDbaReason: $requiresDbaReason,
            alterAckReason:    $alterAckReason,
        );
    }

    /**
     * Extract the migration ID from a file path — the filename
     * minus the `.sql` extension.
     *
     * @param string $filePath Absolute path to the migration file
     *
     * @return string
     */
    private function idFromFilePath(string $filePath): string
    {
        $basename = basename($filePath);
        if (str_ends_with($basename, '.sql')) {
            return substr($basename, 0, -4);
        }
        return $basename;
    }
}
