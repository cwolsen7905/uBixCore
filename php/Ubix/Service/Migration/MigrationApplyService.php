<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use DateTime;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\DataTransferObject\Migration\AppliedMigration;
use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\Repository\SchemaMigration\SchemaMigrationReaderInterface as SchemaMigrationReader;
use Ubix\Repository\SchemaMigration\SchemaMigrationWriterInterface as SchemaMigrationWriter;
use Ubix\Service\ProcessService;

/**
 * Applies a single parsed `MigrationFile` against the target
 * database and records the row in `SYSTEMS.Schema_Migrations`.
 *
 * Per `docs/standards/migrations.md` §4.1. Steps:
 *
 * 1. **Apply** — pipe the body through the `mariadb` CLI via
 *    `ProcessService`. PDO's prepare/execute path doesn't run
 *    multi-statement bodies safely, and DDL on MariaDB is
 *    non-transactional regardless, so the CLI shell-out is the
 *    standard tool here. Same approach the existing
 *    `Ubix\Console\Command\Database\ResetSchemaCommand` uses.
 * 2. **Record** — on success, insert the `Schema_Migrations` row
 *    via the writer repo with the wall-clock duration in ms.
 * 3. **Bootstrap special case** — `applyBootstrap()` runs the
 *    `00000000000000_init_schema_migrations` file before its
 *    tracker table exists; the row insertion happens through the
 *    same writer immediately afterwards.
 *
 * Pre-flight per §4.1 step 2: every non-`IF NOT EXISTS`
 * `CREATE TABLE` reference in the body is looked up against
 * `INFORMATION_SCHEMA.TABLES` via the reader. A live target
 * triggers the §8 manual-application abort with the
 * `migrate:reconcile` hint so operators don't waste time
 * debugging a "Table already exists" mariadb CLI error.
 *
 * @see \Ubix\Tests\Service\Migration\MigrationApplyServiceTest PHPUnit test case
 */
final class MigrationApplyService
{
    /**
     * Bootstrap migration ID — has the reserved zero-prefix per
     * `migrations.md` §5. The runner applies it specially because
     * its target table doesn't exist until it runs.
     */
    public const string BOOTSTRAP_MIGRATION_ID = '00000000000000_init_schema_migrations';

    /**
     * Constructor
     *
     * @param Logger                             $logger             PSR-3 logger
     * @param SchemaMigrationReader              $reader             Reads `INFORMATION_SCHEMA` for the pre-flight clash check
     * @param SchemaMigrationWriter              $writer             Inserts tracker rows
     * @param ProcessService                     $processService     Shells out to the `mariadb` CLI for the apply step
     * @param MigrationCredentialResolverService $credentialResolver Picks MySQL connection params; prefers `MYSQL_MIGRATION_*` over `MYSQL_WRITE_*`
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private SchemaMigrationReader $reader,
        private SchemaMigrationWriter $writer,
        private ProcessService $processService,
        private MigrationCredentialResolverService $credentialResolver,
    ) {
    }

    /**
     * Apply a single migration end-to-end and record the tracker
     * row. Throws on any failure; on success returns the recorded
     * `AppliedMigration`.
     *
     * @param MigrationFile $file      Parsed migration to apply
     * @param string        $appliedBy Actor identifier per `migrations.md` §3
     *
     * @return AppliedMigration Recorded tracker row
     */
    public function apply(MigrationFile $file, string $appliedBy): AppliedMigration
    {
        $this->preflightCreateTableClashes($file);
        return $this->runAndRecord($file, $appliedBy);
    }

    /**
     * Apply the bootstrap migration in the special case where the
     * tracker table doesn't yet exist. Mirrors `apply()` minus
     * any future pre-flight differences.
     *
     * @param MigrationFile $file      Bootstrap migration — must have id `00000000000000_init_schema_migrations`
     * @param string        $appliedBy Actor identifier
     *
     * @return AppliedMigration Recorded tracker row
     *
     * @throws InvalidArgumentException When `$file` is not the bootstrap
     */
    public function applyBootstrap(MigrationFile $file, string $appliedBy): AppliedMigration
    {
        if ($file->id !== self::BOOTSTRAP_MIGRATION_ID) {
            throw new InvalidArgumentException(sprintf(
                'applyBootstrap() called with non-bootstrap migration `%s`.',
                $file->id,
            ));
        }
        return $this->runAndRecord($file, $appliedBy);
    }

    /**
     * Record a `Schema_Migrations` row for a migration that has
     * already been applied out-of-band — does NOT run the body.
     * Backs `bin/ubix migrate:reconcile` per
     * `docs/standards/migrations.md` §8.
     *
     * The row's `applied_by` carries the `manual:` prefix so
     * dashboards can filter for emergency reconciles vs the
     * normal `cli:` / `ci:` apply path. The original
     * `Description:` header gets the reason appended in the
     * recorded row so reviewers see WHY the manual step happened
     * without leaving the tracker.
     *
     * @param MigrationFile $file     Parsed migration to mark as applied
     * @param string        $username Operator's unix username; the row's `applied_by` is `manual:<username>`
     * @param string        $reason   Free-form rationale; appended to the recorded `description`
     *
     * @return AppliedMigration Synthesised tracker row
     */
    public function reconcile(MigrationFile $file, string $username, string $reason): AppliedMigration
    {
        $appliedBy   = 'manual:' . $username;
        $description = sprintf('%s (Reconciled: %s)', $file->description, $reason);

        $this->writer->insert(
            id:             $file->id,
            targetDatabase: $file->targetDatabase,
            description:    $description,
            checksum:       $file->checksum,
            appliedBy:      $appliedBy,
            durationMs:     0,
        );

        return new AppliedMigration(
            id:             $file->id,
            targetDatabase: $file->targetDatabase,
            description:    $description,
            checksum:       $file->checksum,
            appliedAt:      new DateTime(),
            appliedBy:      $appliedBy,
            durationMs:     0,
        );
    }

    /**
     * For each non-`IF NOT EXISTS` `CREATE TABLE` reference in
     * the migration body, look the table up via the reader. An
     * existing match triggers the §8 manual-application abort.
     *
     * SQL comments are stripped before regex matching so a
     * commented-out `CREATE TABLE` doesn't false-positive.
     *
     * @param MigrationFile $file Migration being preflighted
     *
     * @throws InvalidArgumentException When a target object already exists
     *
     * @return void
     */
    private function preflightCreateTableClashes(MigrationFile $file): void
    {
        $sanitised = $this->stripSqlComments($file->body);
        if (
            preg_match_all(
                '/\bCREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS\b)([A-Za-z0-9_.`]+)/i',
                $sanitised,
                $matches,
            ) === false
        ) {
            return;
        }

        // Mirror the body-rewrite in `runBodyViaMariadbCli()`: when
        // `DATABASE_PREFIX` is set, the qualified refs we parse out
        // of the on-disk body name the UNPREFIXED schema, but the
        // actual create will target `<prefix><schema>`. Check the
        // prefixed schema for the clash so the test-DB pass doesn't
        // false-positive on tables that exist in the runtime
        // schema's twin.
        $prefix = (string) getenv('DATABASE_PREFIX');

        foreach ($matches[1] as $rawRef) {
            [$schema, $table] = $this->splitObjectRef($rawRef, $file->targetDatabase);
            $effectiveSchema  = $prefix . $schema;
            if ($this->reader->tableExists($effectiveSchema, $table)) {
                throw new InvalidArgumentException(sprintf(
                    'Object `%s.%s` already exists; this migration appears to have been applied manually. Run `migrate:reconcile %s` to record it.',
                    $effectiveSchema,
                    $table,
                    $file->id,
                ));
            }
        }
    }

    /**
     * Strip `--` line and `/* ... *\/` block comments from a SQL
     * body, replacing comment bytes with spaces so byte offsets
     * and line numbers survive intact. Same logic as the
     * destructive-statement detector; duplicated here rather than
     * shared because the dependency would point the wrong
     * direction (apply → detector creates a circular shape).
     *
     * @param string $body Raw migration body
     *
     * @return string Comment-stripped body, same length
     */
    private function stripSqlComments(string $body): string
    {
        $sanitised = preg_replace_callback(
            '/\/\*[\s\S]*?\*\//',
            function (array $match): string {
                return preg_replace('/[^\r\n]/', ' ', $match[0]) ?? $match[0];
            },
            $body,
        ) ?? $body;

        return preg_replace_callback(
            '/--[^\r\n]*/',
            function (array $match): string {
                return str_repeat(' ', strlen($match[0]));
            },
            $sanitised,
        ) ?? $sanitised;
    }

    /**
     * Split a possibly-qualified object reference like
     * `VSCASH.Foo` or `\`VSCASH\`.\`Foo\`` into a (schema, table)
     * tuple. When the ref is unqualified, the migration's target
     * database fills in.
     *
     * @param string $rawRef          The captured reference text from the regex
     * @param string $defaultDatabase Fallback schema when `$rawRef` is unqualified
     *
     * @return array{0: string, 1: string} Tuple of (schema, table)
     */
    private function splitObjectRef(string $rawRef, string $defaultDatabase): array
    {
        $clean = str_replace('`', '', $rawRef);
        if (str_contains($clean, '.')) {
            [$schema, $table] = explode('.', $clean, 2);
            return [$schema, $table];
        }
        return [$defaultDatabase, $clean];
    }

    /**
     * Run the migration body, time it, record the tracker row, and
     * return a synthesised `AppliedMigration`. Shared between the
     * normal and bootstrap apply paths.
     *
     * @param MigrationFile $file      Migration about to apply
     * @param string        $appliedBy Actor identifier
     *
     * @return AppliedMigration Synthesised from the inputs + measured duration
     */
    private function runAndRecord(MigrationFile $file, string $appliedBy): AppliedMigration
    {
        $startMs = (int) round(microtime(true) * 1000);
        $this->runBodyViaMariadbCli($file);
        $endMs      = (int) round(microtime(true) * 1000);
        $durationMs = max(0, $endMs - $startMs);

        // Record the EFFECTIVE target database (with `DATABASE_PREFIX`
        // applied) so `migrate:status` against the prefixed tracker
        // shows coherent rows — both pending file entries (built via
        // UbixDatabase->databaseName()) and applied rows from the
        // tracker carry the same prefixed form.
        $prefix      = (string) getenv('DATABASE_PREFIX');
        $effectiveDb = $prefix . $file->targetDatabase;

        $this->writer->insert(
            id:             $file->id,
            targetDatabase: $effectiveDb,
            description:    $file->description,
            checksum:       $file->checksum,
            appliedBy:      $appliedBy,
            durationMs:     $durationMs,
        );

        return new AppliedMigration(
            id:             $file->id,
            targetDatabase: $effectiveDb,
            description:    $file->description,
            checksum:       $file->checksum,
            appliedAt:      new DateTime(),
            appliedBy:      $appliedBy,
            durationMs:     $durationMs,
        );
    }

    /**
     * Repoint every schema-qualified reference to the migration's
     * declared target database at `<prefix><database>`, so a
     * prefixed run (test DB / `--prefix=`) never reaches the
     * unprefixed runtime schema.
     *
     * Both quoting styles must be handled: the unquoted
     * `ntl_db.transact` and the backtick-quoted form name the same
     * object, and the backtick-quoted one is the more conventional.
     * The original plain `str_replace('<db>.', …)` matched only the
     * unquoted form, so a backticked body kept its unprefixed schema and
     * the apply died with `ERROR 1146 ... doesn't exist` against
     * the real cluster's schema name (dev pipeline, 2026-08-17 —
     * `20260817221748_add_bin_8_column_to_ntl_db_transaction_tables`).
     *
     * The leading lookbehind keeps the match anchored to a whole
     * identifier, so a table whose name merely ENDS with the
     * database name (`archive_ntl_db.x`) is left alone.
     *
     * @param string $body     Raw migration body (on-disk bytes)
     * @param string $database The migration's declared `Database:` header value
     * @param string $prefix   Non-empty `DATABASE_PREFIX` value
     *
     * @return string Body with qualified references repointed at the prefixed schema
     */
    private function prefixQualifiedReferences(string $body, string $database, string $prefix): string
    {
        $pattern = sprintf('/(?<![A-Za-z0-9_`])`?%s`?(\s*\.)/', preg_quote($database, '/'));

        return preg_replace_callback(
            $pattern,
            static function (array $match) use ($database, $prefix): string {
                return sprintf('`%s%s`%s', $prefix, $database, $match[1]);
            },
            $body,
        ) ?? $body;
    }

    /**
     * Pipe the body through the `mariadb` CLI. Credentials come
     * from `MigrationCredentialResolverService::resolve()`, which
     * prefers `MYSQL_MIGRATION_*` over `MYSQL_WRITE_*` so the
     * apply path can run with DDL-grant-bearing creds while the
     * rest of the app continues to use `livewrite2`. Under
     * PHPUnit the resolver returns `TEST_MYSQL_WRITE_*` so test
     * isolation is preserved.
     *
     * The migration body is written to a temp file and piped via
     * `<` redirect rather than `--execute` so multi-statement
     * bodies and embedded semicolons in string literals work
     * without shell-escaping headaches.
     *
     * @param MigrationFile $file Migration whose body is being applied
     *
     * @throws RuntimeException When the CLI exits non-zero
     *
     * @return void
     */
    private function runBodyViaMariadbCli(MigrationFile $file): void
    {
        $params = $this->credentialResolver->resolve();

        // When `DATABASE_PREFIX` is set (typically by
        // `--prefix=TEST_` via `MigrationConnectionTargetService::apply()`,
        // or by the test bootstrap), rewrite the migration's
        // declared target database AND any fully-qualified
        // `<DB>.<Table>` references in the body to point at the
        // prefixed schema. The body rewrite is scoped to the
        // migration's declared `Database:` header value only —
        // cross-DB references inside a migration body (rare; the
        // 8 current migrations don't have any) would need explicit
        // handling. Migration checksum is unaffected: `$file->body`
        // (the input to SHA-256) stays the original on-disk bytes;
        // only the temp file fed to the mariadb CLI is rewritten.
        $prefix       = (string) getenv('DATABASE_PREFIX');
        $effectiveDb  = $prefix . $file->targetDatabase;
        $bodyForApply = $prefix === '' ? $file->body : $this->prefixQualifiedReferences($file->body, $file->targetDatabase, $prefix);

        $tempPath = tempnam(sys_get_temp_dir(), 'ubix-migration-');
        if ($tempPath === false) {
            throw new RuntimeException('Could not allocate a temp file for the migration body.');
        }
        if (file_put_contents($tempPath, $bodyForApply) === false) {
            unlink($tempPath);
            throw new RuntimeException(sprintf('Could not write migration body to `%s`.', $tempPath));
        }

        $command = sprintf(
            'mariadb --ssl=0 --host=%s --port=%s --user=%s --password=%s --database=%s < %s',
            escapeshellarg($params->host),
            escapeshellarg($params->port),
            escapeshellarg($params->username),
            escapeshellarg($params->password),
            escapeshellarg($effectiveDb),
            escapeshellarg($tempPath),
        );

        try {
            // `executeAsSubprocess()` passes the command string to
            // `proc_open()`, which PHP runs via `/bin/sh -c <command>`
            // automatically — POSIX shell is available in every container
            // image including Alpine. Wrapping in an extra `bash -c …`
            // here double-shelled and broke on the production image
            // (Alpine `ubix-php84` base has no `bash`).
            $result = $this->processService->executeAsSubprocess($command);
        } finally {
            unlink($tempPath);
        }

        if ($result->exitCode !== 0) {
            throw new RuntimeException(sprintf(
                'Migration `%s` failed during apply. Exit code: %d. STDERR: %s',
                $file->id,
                $result->exitCode,
                $result->stderrOutput === '' ? '<empty>' : $result->stderrOutput,
            ));
        }
    }
}
