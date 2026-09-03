<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\SchemaDiffResult;
use Ubix\Service\ProcessService;
use Ubix\Service\ProjectRootService;

/**
 * Compares the live cluster schema for each Ubix-consumed
 * database against the checked-in `sql/<DB>.sql` reference dump.
 *
 * Per `docs/standards/migrations.md` §9. Slice 5 ships
 * `--mode=reference-dump` only — comparison against the
 * checked-in dump, not against a replay of every migration into a
 * scratch DB. Replay mode is M2 work because it requires a
 * scratch-DB lifecycle on the runner host.
 *
 * Pipeline per database:
 *
 * 1. `mariadb-dump --no-data --skip-comments --skip-extended-insert
 *    --skip-add-drop-table <DB>` for the live snapshot.
 * 2. Read `sql/<DB>.sql` for the reference snapshot.
 * 3. Normalise both — strip the version-banner header lines, strip
 *    `AUTO_INCREMENT=N` counters, strip the dump-timestamp line,
 *    drop blank lines.
 * 4. Diff line-by-line, splitting into `extraInLive` (drift in the
 *    live cluster) and `missingFromLive` (un-applied migrations).
 *
 * @see \Ubix\Tests\Service\Migration\SchemaDiffServiceTest PHPUnit test case
 */
final class SchemaDiffService
{
    /**
     * Ubix-consumed databases. Mirrors the sandbox list in
     * `Ubix\Console\Command\Database\ResetSchemaCommand`. If the
     * Ubix database set changes, update both.
     */
    private const array DATABASES = [
        'ADSERVER',
        'BILLING',
        'CHAT_SYSTEM_LOG',
        'CHAT_SYSTEM',
        'FLIRT_REWARDS',
        'flirt4free',
        'MAILINGS',
        'MESSAGING',
        'ntl_db',
        'STUDIOS_STATS',
        'STUDIOS',
        'SYSTEMS',
        'VSCASH_STATS',
        'VSCASH',
    ];

    /**
     * Constructor
     *
     * @param Logger                             $logger             PSR-3 logger
     * @param ProcessService                     $processService     Shells out to `mariadb-dump`
     * @param MigrationCredentialResolverService $credentialResolver Picks MySQL connection params; prefers `MYSQL_MIGRATION_*` over `MYSQL_WRITE_*`
     * @param ProjectRootService                 $projectRoot        Resolves `sql/` under the host project root
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private ProcessService $processService,
        private MigrationCredentialResolverService $credentialResolver,
        private ProjectRootService $projectRoot,
    ) {
    }

    /**
     * Run the diff for every Ubix-consumed database (or one,
     * when `$databaseFilter` is non-null). Returns one
     * `SchemaDiffResult` per database considered; databases for
     * which the reference dump is missing surface a result with
     * `errorMessage` populated.
     *
     * @param ?string $databaseFilter Restrict to one target database
     *
     * @return SchemaDiffResult[]
     */
    public function diffAll(?string $databaseFilter = null): array
    {
        $databases = self::DATABASES;
        if ($databaseFilter !== null) {
            $databases = in_array($databaseFilter, self::DATABASES, true) ? [$databaseFilter] : [];
            if ($databases === []) {
                return [
                    new SchemaDiffResult(
                        database:        $databaseFilter,
                        hasDrift:        false,
                        extraInLive:     [],
                        missingFromLive: [],
                        errorMessage:    sprintf(
                            'Database `%s` is not in the Ubix-consumed set; nothing to diff.',
                            $databaseFilter,
                        ),
                    ),
                ];
            }
        }

        $results = [];
        foreach ($databases as $database) {
            $results[] = $this->diffOne($database);
        }
        return $results;
    }

    /**
     * Run the diff for a single database.
     *
     * @param string $database Target database
     *
     * @return SchemaDiffResult
     */
    private function diffOne(string $database): SchemaDiffResult
    {
        $referencePath = $this->referenceDumpPath($database);
        if (! is_readable($referencePath)) {
            return new SchemaDiffResult(
                database:        $database,
                hasDrift:        false,
                extraInLive:     [],
                missingFromLive: [],
                errorMessage:    sprintf('Reference dump `%s` is missing or not readable.', $referencePath),
            );
        }
        $reference = (string) file_get_contents($referencePath);

        $live = $this->dumpLiveSchema($database);
        if ($live === null) {
            return new SchemaDiffResult(
                database:        $database,
                hasDrift:        false,
                extraInLive:     [],
                missingFromLive: [],
                errorMessage:    sprintf('Could not capture live schema for `%s` via mariadb-dump.', $database),
            );
        }

        $referenceLines = $this->normalise($reference);
        $liveLines      = $this->normalise($live);

        $extraInLive     = array_values(array_diff($liveLines, $referenceLines));
        $missingFromLive = array_values(array_diff($referenceLines, $liveLines));

        return new SchemaDiffResult(
            database:        $database,
            hasDrift:        $extraInLive !== [] || $missingFromLive !== [],
            extraInLive:     $extraInLive,
            missingFromLive: $missingFromLive,
        );
    }

    /**
     * Capture the live schema for `$database` via mariadb-dump.
     * Returns null when the dump exits non-zero — caller surfaces
     * this as an error result.
     *
     * @param string $database Target database
     *
     * @return ?string Captured stdout, or null on failure
     */
    private function dumpLiveSchema(string $database): ?string
    {
        $params = $this->credentialResolver->resolve();

        $command = sprintf(
            'mariadb-dump --ssl=0 --no-data --skip-comments --skip-extended-insert --skip-add-drop-table --single-transaction --host=%s --port=%s --user=%s --password=%s %s',
            escapeshellarg($params->host),
            escapeshellarg($params->port),
            escapeshellarg($params->username),
            escapeshellarg($params->password),
            escapeshellarg($database),
        );

        // `executeAsSubprocess()` already runs the command via PHP's
        // `proc_open()` → `/bin/sh -c <command>` — no need to wrap in
        // an extra `bash -c` (and bash isn't installed in the Alpine
        // production image).
        $result = $this->processService->executeAsSubprocess($command);
        if ($result->exitCode !== 0) {
            return null;
        }
        return $result->stdoutOutput;
    }

    /**
     * Normalise a SQL dump for diffing. Returns the cleaned content
     * as an array of trimmed non-empty lines.
     *
     * Stripped: leading/trailing whitespace, blank lines, mariadb-dump
     * version-banner lines (`-- MySQL dump …`, `-- Host:`,
     * `-- Server version`, `-- Dump completed on …`), the
     * conditional-comment SET-block, and `AUTO_INCREMENT=N`
     * counters (which drift naturally as rows are inserted and
     * are not a real schema-difference).
     *
     * @param string $dump Raw SQL dump output
     *
     * @return string[] Cleaned non-empty lines
     */
    private function normalise(string $dump): array
    {
        $stripped = preg_replace('/\s+AUTO_INCREMENT=\d+/i', '', $dump) ?? $dump;

        $lines = preg_split('/\r\n|\n|\r/', $stripped) ?: [];
        $clean = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }
            if ($this->isIgnorableHeaderLine($trimmed)) {
                continue;
            }
            $clean[] = $trimmed;
        }
        return $clean;
    }

    /**
     * Whether a line should be dropped from the normalised output —
     * mariadb-dump banners that vary by host / version / time and the
     * `/*!N SET …*` conditional-compatibility block.
     *
     * @param string $trimmed Line with leading / trailing whitespace stripped
     *
     * @return bool True when the line should be dropped
     */
    private function isIgnorableHeaderLine(string $trimmed): bool
    {
        // All `--` SQL comment lines — covers mariadb-dump banner
        // header (`-- MySQL dump`, `-- Host:`, `-- Server version`,
        // `-- Dump completed on …`), per-table narration
        // (`-- Table structure for table …`, `-- Dumping data for
        // table …`), and any other operator commentary. Schema
        // structure lives in CREATE / ALTER, not in comments.
        if (str_starts_with($trimmed, '--')) {
            return true;
        }
        // MariaDB conditional-compatibility comment lines: `/*!N SET …*/;`
        // (MySQL-style) and `/*M! … */` (MariaDB-only banner like
        // "enable the sandbox mode"). Both vary by dump-source server
        // version and aren't real schema state.
        if (str_starts_with($trimmed, '/*!') && str_ends_with($trimmed, '*/;')) {
            return true;
        }
        if (str_starts_with($trimmed, '/*M!') && (str_ends_with($trimmed, '*/') || str_ends_with($trimmed, '*/;'))) {
            return true;
        }
        // `DROP TABLE IF EXISTS` setup chrome — present when the dump
        // is taken without `--skip-add-drop-table` and absent when it
        // is. Strip both forms so the diff doesn't false-positive on
        // dump-flag differences.
        return preg_match('/^DROP\s+TABLE\s+IF\s+EXISTS\b/i', $trimmed) === 1;
    }

    /**
     * Build the absolute path to the checked-in reference dump for
     * `$database`. Lives at `<repo>/sql/<DB>.sql`.
     *
     * @param string $database Target database
     *
     * @return string Absolute path
     */
    private function referenceDumpPath(string $database): string
    {
        return $this->projectRoot->getPath('sql', $database . '.sql');
    }
}
