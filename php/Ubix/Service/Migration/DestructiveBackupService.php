<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\Enum\Env;
use Ubix\Service\ProcessService;

/**
 * Pre-apply backup snapshot for destructive migrations on staging /
 * prod per `docs/standards/migrations.md` §11.4.
 *
 * For any migration carrying a `Destructive:` header running
 * against `Env::STAGING` or `Env::PROD`, this service runs:
 *
 * ```
 * mariadb-dump --single-transaction --skip-comments <DB> <table1> <table2> …
 *   | gzip > /var/ubix-migration-backups/<id>/<env>-<UTC_timestamp>.sql.gz
 * ```
 *
 * Tables are extracted from the migration body via regex against
 * the §11.1 destructive-statement patterns. `RENAME TABLE A TO B`
 * captures both source and target. The backup directory is
 * configurable via `NEPTUNE_MIGRATION_BACKUP_DIR`; the standard's
 * default is `/var/ubix-migration-backups`.
 *
 * On dev / sandbox the service is a no-op — engineers iterate fast
 * and re-bootstrap the schema cheaply if anything goes sideways.
 *
 * @see \Ubix\Tests\Service\Migration\DestructiveBackupServiceTest PHPUnit test case
 */
final class DestructiveBackupService
{
    /**
     * Default backup root directory. Override with the
     * `NEPTUNE_MIGRATION_BACKUP_DIR` env var.
     */
    public const string DEFAULT_BACKUP_DIRECTORY = '/var/ubix-migration-backups';

    /**
     * Constructor
     *
     * @param Logger                             $logger             PSR-3 logger
     * @param ProcessService                     $processService     Shells out to `mariadb-dump | gzip`
     * @param MigrationCredentialResolverService $credentialResolver Picks MySQL connection params; prefers `MYSQL_MIGRATION_*` over `MYSQL_WRITE_*`
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private ProcessService $processService,
        private MigrationCredentialResolverService $credentialResolver,
    ) {
    }

    /**
     * Take a backup snapshot of every table referenced by the
     * destructive statements in `$file`. Returns the path to the
     * `.sql.gz` artefact, or null when the current environment is
     * dev / sandbox (in which case no snapshot was needed).
     *
     * @param MigrationFile $file Destructive migration; called only after the runtime guard's `--i-acknowledge-destructive` flag passed
     * @param Env           $env  Resolved environment — controls whether the snapshot runs
     *
     * @return ?string Absolute path to the written snapshot, or null when skipped
     *
     * @throws RuntimeException When `mariadb-dump` exits non-zero
     */
    public function snapshot(MigrationFile $file, Env $env): ?string
    {
        if ($env === Env::DEV || $env === Env::SANDBOX || $env === Env::TEST) {
            return null;
        }

        $tables = $this->extractAffectedTables($file);
        if ($tables === []) {
            // Destructive header set but no parseable table refs —
            // very unusual (DELETE FROM with a CTE? exotic syntax?)
            // Fail loud rather than silently skip the backup.
            throw new RuntimeException(sprintf(
                'Could not extract any table references from destructive migration `%s`; refusing to apply without a verifiable backup snapshot.',
                $file->id,
            ));
        }

        $snapshotPath = $this->buildSnapshotPath($file, $env);
        $this->ensureDirectoryExists(dirname($snapshotPath));

        $params = $this->credentialResolver->resolve();

        $tableArgs = implode(' ', array_map('escapeshellarg', $tables));
        $command   = sprintf(
            'mariadb-dump --ssl=0 --single-transaction --skip-comments --host=%s --port=%s --user=%s --password=%s %s %s | gzip > %s',
            escapeshellarg($params->host),
            escapeshellarg($params->port),
            escapeshellarg($params->username),
            escapeshellarg($params->password),
            escapeshellarg($file->targetDatabase),
            $tableArgs,
            escapeshellarg($snapshotPath),
        );

        // `executeAsSubprocess()` already runs the command via PHP's
        // `proc_open()` → `/bin/sh -c <command>` — no need to wrap in
        // an extra `bash -c` (and bash isn't installed in the Alpine
        // production image).
        $result = $this->processService->executeAsSubprocess($command);
        if ($result->exitCode !== 0) {
            throw new RuntimeException(sprintf(
                'mariadb-dump for destructive migration `%s` failed. Exit code: %d. STDERR: %s',
                $file->id,
                $result->exitCode,
                $result->stderrOutput === '' ? '<empty>' : $result->stderrOutput,
            ));
        }

        return $snapshotPath;
    }

    /**
     * Walk the migration body for destructive statement table refs.
     * Returns table names (no DB prefix) since the dump command is
     * already scoped to `targetDatabase`.
     *
     * @param MigrationFile $file Destructive migration
     *
     * @return string[] Sorted, deduplicated list of table names
     */
    private function extractAffectedTables(MigrationFile $file): array
    {
        $body  = $file->body;
        $names = [];

        // DROP TABLE … / TRUNCATE TABLE … / ALTER TABLE …
        if (
            preg_match_all(
                '/\b(?:DROP|TRUNCATE|ALTER)\s+TABLE\s+(?:IF\s+EXISTS\s+)?([A-Za-z0-9_.`]+)/i',
                $body,
                $matches,
            ) !== false
        ) {
            foreach ($matches[1] as $rawRef) {
                $names[] = $this->stripDatabasePrefix($rawRef, $file->targetDatabase);
            }
        }

        // RENAME TABLE A TO B[, C TO D, …] — capture every source.
        // The target name comes into existence during the rename
        // and isn't part of the pre-state we need to dump.
        if (
            preg_match_all(
                '/\bRENAME\s+TABLE\s+([A-Za-z0-9_.`]+)\s+TO\s+[A-Za-z0-9_.`]+/i',
                $body,
                $renameMatches,
            ) !== false
        ) {
            foreach ($renameMatches[1] as $rawRef) {
                $names[] = $this->stripDatabasePrefix($rawRef, $file->targetDatabase);
            }
        }

        // DELETE FROM <table> [WHERE …] — only the table name; the
        // detector already filtered out WHERE-bearing variants.
        if (
            preg_match_all(
                '/\bDELETE\s+FROM\s+([A-Za-z0-9_.`]+)/i',
                $body,
                $deleteMatches,
            ) !== false
        ) {
            foreach ($deleteMatches[1] as $rawRef) {
                $names[] = $this->stripDatabasePrefix($rawRef, $file->targetDatabase);
            }
        }

        $unique = array_values(array_unique($names));
        sort($unique);
        return $unique;
    }

    /**
     * Strip the `<DB>.` prefix from an object reference when it
     * matches the migration's target database, and remove backticks
     * either way. The dump command is scoped to a single DB so
     * cross-DB refs would error out — but the detector is permissive,
     * so this normalises both forms.
     *
     * @param string $rawRef          Captured reference text
     * @param string $defaultDatabase Migration's target database
     *
     * @return string Bare table name
     */
    private function stripDatabasePrefix(string $rawRef, string $defaultDatabase): string
    {
        $clean  = str_replace('`', '', $rawRef);
        $prefix = $defaultDatabase . '.';
        if (str_starts_with($clean, $prefix)) {
            return substr($clean, strlen($prefix));
        }
        return $clean;
    }

    /**
     * Build the absolute snapshot path:
     * `<root>/<migration_id>/<env>-<UTC_timestamp>.sql.gz`.
     *
     * @param MigrationFile $file Destructive migration
     * @param Env           $env  Resolved environment
     *
     * @return string Absolute path the gzip output gets written to
     */
    private function buildSnapshotPath(MigrationFile $file, Env $env): string
    {
        $rootRaw = getenv('NEPTUNE_MIGRATION_BACKUP_DIR');
        $root    = is_string($rootRaw) && $rootRaw !== '' ? $rootRaw : self::DEFAULT_BACKUP_DIRECTORY;
        $stamp   = gmdate('Y-m-d\\TH-i-s\\Z');
        return rtrim($root, '/') . '/' . $file->id . '/' . $env->value . '-' . $stamp . '.sql.gz';
    }

    /**
     * Create the snapshot directory if it doesn't exist. Permissions
     * are 0o755 (owner-write, world-read) — backups are sensitive
     * so the directory is NOT world-writable.
     *
     * @param string $directory Absolute path to the directory to ensure
     *
     * @throws RuntimeException When `mkdir` fails
     *
     * @return void
     */
    private function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }
        if (! mkdir($directory, 0o755, true) && ! is_dir($directory)) {
            throw new RuntimeException(sprintf(
                'Could not create snapshot directory `%s`.',
                $directory,
            ));
        }
    }
}
