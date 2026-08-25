<?php

declare(strict_types=1);

namespace Ubix\Repository\SchemaMigration;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Throwable;
use Ubix\DataTransferObject\Migration\AppliedMigration;
use Ubix\DataTransferObject\PdoError;
use Ubix\DataTransferObject\SqlRepository\SchemaMigrationOptions;
use Ubix\Enum\UbixDatabase;
use Ubix\Exception\DtoException;
use Ubix\Repository\SchemaMigration\SchemaMigrationReaderInterface as SchemaMigrationReader;
use Ubix\Repository\SchemaMigration\SchemaMigrationWriterInterface as SchemaMigrationWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * SQL-backed reader + writer for `Schema_Migrations`.
 *
 * Per `docs/standards/migrations.md` §3, the canonical tracker is
 * `SYSTEMS.Schema_Migrations`. The schema name is parameterized via
 * the `DATABASE_PREFIX` env var so that test runs (and any
 * other prefixed-database setup) target a parallel tracker without
 * polluting the real `SYSTEMS.Schema_Migrations` row set:
 *
 * - Empty prefix (default): tracker is `SYSTEMS.Schema_Migrations`.
 * - `DATABASE_PREFIX=TEST_` (set by
 *   `AbstractMigrationCommand::applyTargetOptions()` when
 *   `--prefix=TEST_` is passed): tracker is
 *   `TEST_SYSTEMS.Schema_Migrations`.
 *
 * The prefix is resolved lazily on first repository call rather than
 * at construction so the `bin/ubix` boot-time DI container doesn't
 * lock in the empty default before the CLI parses `--prefix`.
 *
 * @see \Ubix\Tests\Repository\SchemaMigration\SchemaMigrationSqlRepositoryTest PHPUnit test case
 */
final class SchemaMigrationSqlRepository implements SchemaMigrationReader, SchemaMigrationWriter
{
    /**
     * MySQL error codes treated as the legitimate "tracker not
     * bootstrapped yet" state in `trackerTableExists()` — every
     * other error (auth fail 1045, host unreachable 2002/2003,
     * permission denied on the tracker table 1142/1143, broken
     * connection 2006, etc.) is a real problem the operator needs
     * to see, so we re-throw to surface a clean error rather than
     * silently masquerading as "all migrations pending".
     *
     * - **1146** `ER_NO_SUCH_TABLE` — tracker not yet created (first run).
     * - **1049** `ER_BAD_DB_ERROR` — tracker schema itself missing (fresh cluster).
     */
    private const array TRACKER_MISSING_MYSQL_ERROR_CODES = [1146, 1049];

    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param SqlService $sqlService SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @return AppliedMigration[]
     */
    public function getAll(): array
    {
        return $this->query(new SchemaMigrationOptions());
    }

    /**
     * {@inheritDoc}
     */
    public function getById(string $id): ?AppliedMigration
    {
        $rows = $this->query(new SchemaMigrationOptions(id: $id, limit: 1));
        return $rows[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function insert(
        string $id,
        string $targetDatabase,
        string $description,
        string $checksum,
        string $appliedBy,
        int $durationMs,
    ): void {
        $this->sqlService->query(
            'INSERT INTO ' . $this->trackerTable() . '
				(id, target_database, description, checksum, applied_by, duration_ms)
			 VALUES
				(:id, :target_database, :description, :checksum, :applied_by, :duration_ms)',
            [
                'applied_by'      => $appliedBy,
                'checksum'        => $checksum,
                'description'     => $description,
                'duration_ms'     => $durationMs,
                'id'              => $id,
                'target_database' => $targetDatabase,
            ],
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the failure isn't a "tracker missing" case — auth, connection, or permission errors propagate so the operator sees them
     */
    public function trackerTableExists(): bool
    {
        try {
            $this->sqlService->getColumn(
                'SELECT 1 FROM ' . $this->trackerTable() . ' LIMIT 1',
                [],
            );
            return true;
        } catch (DtoException $exception) {
            // `AbstractPdoSqlService::getPdoStatement()` wraps any
            // PDOException in a `DtoException` carrying a `PdoError`
            // DTO with the underlying driver code. Pull the MySQL
            // errno out of that DTO and swallow only the specific
            // errnos that mean "tracker not bootstrapped yet";
            // anything else (auth fail, connection refused,
            // table-level permission denied, etc.) is a real bug
            // the operator needs to see — re-throw to surface it.
            $dto = $exception->getDto();
            if (! $dto instanceof PdoError) {
                throw $exception;
            }
            $mysqlErrno = $dto->driverCode !== null ? (int) $dto->driverCode : null;
            if (in_array($mysqlErrno, self::TRACKER_MISSING_MYSQL_ERROR_CODES, true)) {
                return false;
            }
            throw $exception;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function tableExists(string $schema, string $table): bool
    {
        // No try/catch here: `INFORMATION_SCHEMA.TABLES` always exists on any reachable
        // MySQL/MariaDB instance, so the only failure modes are real connection / auth /
        // permission problems — let those propagate.
        $result = $this->sqlService->getColumn(
            'SELECT 1
			 FROM INFORMATION_SCHEMA.TABLES
			 WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table
			 LIMIT 1',
            ['schema' => $schema, 'table' => $table],
        );
        return $result !== null && $result !== false;
    }

    /**
     * {@inheritDoc}
     */
    public function acquireMigrationLock(int $timeoutSeconds): bool
    {
        // No try/catch: `GET_LOCK` returns 0 on timeout / NULL on error in-band, it
        // doesn't throw for the "lock unavailable" case. A PDOException here would be
        // a real connection / auth problem — let it propagate.
        $result = $this->sqlService->getColumn(
            'SELECT GET_LOCK(:name, :timeout)',
            ['name' => 'ubix_migrations', 'timeout' => $timeoutSeconds],
        );
        return (int) $result === 1;
    }

    /**
     * {@inheritDoc}
     */
    public function releaseMigrationLock(): void
    {
        try {
            $this->sqlService->getColumn(
                'SELECT RELEASE_LOCK(:name)',
                ['name' => 'ubix_migrations'],
            );
        } catch (Throwable $exception) {
            unset($exception); // No surface to write to here; the connection drop releases the lock anyway.
        }
    }

    /**
     * Compute the fully-qualified tracker table reference for the
     * current invocation. `UbixDatabase::SYSTEMS->databaseName()`
     * reads `DATABASE_PREFIX` lazily so a CLI `--prefix=TEST_`
     * applied after container build flows through; defaults to plain
     * `SYSTEMS.Schema_Migrations` when no prefix is set.
     *
     * @return string Fully-qualified tracker table reference
     */
    private function trackerTable(): string
    {
        return UbixDatabase::SYSTEMS->databaseName() . '.Schema_Migrations';
    }

    /**
     * Generate and execute a database query then return its
     * results.
     *
     * @param SchemaMigrationOptions $options DTO of options to generate the query
     *
     * @return AppliedMigration[]
     */
    private function query(SchemaMigrationOptions $options): array
    {
        $sql = 'SELECT
                    id,
                    target_database,
                    description,
                    checksum,
                    applied_at,
                    applied_by,
                    duration_ms
                FROM ' . $this->trackerTable() . '
                WHERE 1 = 1';

        $parameters = [];

        if ($options->id !== null) {
            $sql             .= ' AND id = :id';
            $parameters['id'] = $options->id;
        }

        if ($options->targetDatabase !== null) {
            $sql                          .= ' AND target_database = :target_database';
            $parameters['target_database'] = $options->targetDatabase;
        }

        $sql .= ' ORDER BY applied_at ASC';

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        $objects = [];

        /**
         * @var array{
         *     id: string,
         *     target_database: string,
         *     description: string,
         *     checksum: string,
         *     applied_at: string,
         *     applied_by: string,
         *     duration_ms: int,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters, true) as $row) {
            $objects[] = new AppliedMigration(
                id:             $row['id'],
                targetDatabase: $row['target_database'],
                description:    $row['description'],
                checksum:       $row['checksum'],
                appliedAt:      new DateTime($row['applied_at']),
                appliedBy:      $row['applied_by'],
                durationMs:     $row['duration_ms'],
            );
        }

        return $objects;
    }
}
