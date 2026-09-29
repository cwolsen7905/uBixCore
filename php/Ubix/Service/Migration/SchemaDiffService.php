<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\SchemaDiffResult;
use Ubix\Enum\Migration\SchemaDiffMode;
use Ubix\Service\ProcessServiceInterface as ProcessService;
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
 * 3. Normalise both through `SchemaDumpNormaliserService`, which drops
 *    what varies between two servers rendering one schema (dump chrome,
 *    `AUTO_INCREMENT=N`, a column-level charset/collation that only
 *    repeats the table default, MariaDB's auto-generated `json_valid`
 *    check) and attributes every line to its table.
 * 4. Diff line-by-line, splitting into `extraInLive` (drift in the
 *    live cluster) and `missingFromLive` (un-applied migrations).
 *
 * @see \Ubix\Tests\Service\Migration\SchemaDiffServiceTest PHPUnit test case
 */
final class SchemaDiffService
{
    /**
     * Constructor
     *
     * @param Logger                             $logger             PSR-3 logger
     * @param ProcessService                     $processService     Shells out to `mariadb-dump`
     * @param MigrationCredentialResolverService $credentialResolver Picks MySQL connection params; prefers `MYSQL_MIGRATION_*` over `MYSQL_WRITE_*`
     * @param ProjectRootService                 $projectRoot        Resolves `sql/` under the host project root
     * @param SchemaDumpNormaliserService        $normaliser         Reduces a dump to comparable, table-attributed lines
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private ProcessService $processService,
        private MigrationCredentialResolverService $credentialResolver,
        private ProjectRootService $projectRoot,
        private SchemaDumpNormaliserService $normaliser,
    ) {
    }

    /**
     * Run the diff for every Ubix-consumed database (or one,
     * when `$databaseFilter` is non-null). Returns one
     * `SchemaDiffResult` per database considered; databases for
     * which the reference dump is missing surface a result with
     * `errorMessage` populated.
     *
     * @param ?string        $databaseFilter Restrict to one target database
     * @param SchemaDiffMode $mode           How the expected schema is built (§9)
     * @param string         $replayPrefix   Replay mode: the prefix the expected schema was rebuilt under
     *
     * @return SchemaDiffResult[]
     */
    public function diffAll(
        ?string $databaseFilter = null,
        SchemaDiffMode $mode = SchemaDiffMode::REFERENCE_DUMP,
        string $replayPrefix = '',
    ): array {
        $known     = $this->databases();
        $databases = $known;
        if ($databaseFilter !== null) {
            $databases = in_array($databaseFilter, $known, true) ? [$databaseFilter] : [];
            if ($databases === []) {
                return [
                    new SchemaDiffResult(
                        database:        $databaseFilter,
                        hasDrift:        false,
                        extraInLive:     [],
                        missingFromLive: [],
                        errorMessage:    sprintf(
                            'Database `%s` has no schema baseline at sql/<database>.sql; nothing to diff.',
                            $databaseFilter,
                        ),
                    ),
                ];
            }
        }

        $results = [];
        foreach ($databases as $database) {
            $results[] = $this->diffOne($database, $mode, $replayPrefix);
        }
        return $results;
    }

    /**
     * Run the diff for a single database.
     *
     * @param string         $database     Target database
     * @param SchemaDiffMode $mode         How the expected schema is built
     * @param string         $replayPrefix Replay mode: the prefix the expected schema was rebuilt under
     *
     * @return SchemaDiffResult
     */
    private function diffOne(string $database, SchemaDiffMode $mode, string $replayPrefix): SchemaDiffResult
    {
        if ($mode === SchemaDiffMode::REPLAY) {
            if ($replayPrefix === '') {
                return new SchemaDiffResult(
                    database:        $database,
                    hasDrift:        false,
                    extraInLive:     [],
                    missingFromLive: [],
                    errorMessage:    'Replay mode needs --replay-prefix: the schema rebuilt from baseline + migrations lives at `<prefix><database>` on the TEST connection. Build it first with `database:resetSchema --prefix=<prefix>` then `migrate:up --target=test --prefix=<prefix>`.',
                );
            }

            $reference = $this->dumpRebuiltSchema($replayPrefix . $database);
            if ($reference === null) {
                return new SchemaDiffResult(
                    database:        $database,
                    hasDrift:        false,
                    extraInLive:     [],
                    missingFromLive: [],
                    errorMessage:    sprintf(
                        'Replay mode could not read the rebuilt schema `%s` on the TEST connection. Build it first with `database:resetSchema --prefix=%s` then `migrate:up --target=test --prefix=%s`.',
                        $replayPrefix . $database,
                        $replayPrefix,
                        $replayPrefix,
                    ),
                );
            }
        } else {
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
        }

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

        $referenceLines = $this->normaliser->normalise($reference);
        $liveLines      = $this->normaliser->normalise($live);

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
     * Dump the rebuilt expected schema from the TEST connection
     *
     * Replay mode's expected schema is built by `database:resetSchema --prefix=` plus
     * `migrate:up --target=test --prefix=`, which put it at `<prefix><database>` on the
     * unit-test server. This only reads it.
     *
     * The TEST connection rather than the tier being diffed, deliberately: the tier is
     * only ever read from, so a drift check can run against production without holding
     * CREATE or DROP on it.
     *
     * Same dump flags as the live capture. Not tidiness -- a difference in dump options
     * would surface as drift in every comparison.
     *
     * @param string $schema The prefixed schema name
     *
     * @return ?string The dump, or null when it could not be read
     */
    private function dumpRebuiltSchema(string $schema): ?string
    {
        $host     = (string) getenv('TEST_MYSQL_WRITE_HOST');
        $port     = (string) getenv('TEST_MYSQL_WRITE_PORT');
        $username = (string) getenv('TEST_MYSQL_WRITE_USERNAME');
        $password = (string) getenv('TEST_MYSQL_WRITE_PASSWORD');

        if ($host === '' || $username === '') {
            return null;
        }

        // The password goes in a 0600 file, never argv: the process list is world
        // readable, and this class's sibling commands already learned that lesson.
        $defaultsFile = $this->writeDefaultsFile($username, $password, $host, $port === '' ? '3306' : $port);
        if ($defaultsFile === null) {
            return null;
        }

        try {
            $command = sprintf(
                'mariadb-dump --defaults-extra-file=%s --ssl=0 --no-data --skip-comments --skip-extended-insert --skip-add-drop-table --single-transaction %s',
                escapeshellarg($defaultsFile),
                escapeshellarg($schema),
            );

            $result = $this->processService->executeAsSubprocess($command);

            return $result->exitCode === 0 ? $result->stdoutOutput : null;
        } finally {
            unlink($defaultsFile);
        }
    }

    /**
     * Write connection settings to a private file the client reads instead of argv
     *
     * @param string $user     Database user
     * @param string $password Database password
     * @param string $host     Database host
     * @param string $port     Database port
     *
     * @return ?string Path to the file, or null when it could not be created
     */
    private function writeDefaultsFile(string $user, string $password, string $host, string $port): ?string
    {
        $path = tempnam(sys_get_temp_dir(), 'ubix-diff-');
        if ($path === false) {
            return null;
        }

        if (chmod($path, 0600) === false) {
            unlink($path);

            return null;
        }

        $contents = sprintf(
            "[client]\nuser=%s\npassword=%s\nhost=%s\nport=%s\n",
            $user,
            $password,
            $host,
            $port,
        );

        if (file_put_contents($path, $contents) === false) {
            unlink($path);

            return null;
        }

        return $path;
    }

    /**
     * The host's databases, one per sql/<name>.sql baseline
     *
     * @return string[]
     */
    private function databases(): array
    {
        //
        //  The databases are the host's: every sql/<name>.sql baseline it ships is one
        //  schema to diff. Same rule as ResetSchemaCommand, so the two cannot drift.
        //
        $databases = [];
        foreach (glob($this->projectRoot->getPath('sql', '*.sql')) ?: [] as $baseline) {
            $databases[] = basename($baseline, '.sql');
        }
        sort($databases);

        return $databases;
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
