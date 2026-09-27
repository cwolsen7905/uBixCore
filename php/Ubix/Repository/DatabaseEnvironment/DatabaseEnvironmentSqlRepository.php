<?php

declare(strict_types=1);

namespace Ubix\Repository\DatabaseEnvironment;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\DatabaseEnvironment\DatabaseEnvironment;
use Ubix\DataTransferObject\PdoError;
use Ubix\DataTransferObject\SqlRepository\DatabaseEnvironmentOptions;
use Ubix\Enum\Env;
use Ubix\Enum\UbixDatabase;
use Ubix\Exception\DtoException;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentReaderInterface as DatabaseEnvironmentReader;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentWriterInterface as DatabaseEnvironmentWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * `SYSTEMS.Database_Environment`: one row saying which environment this server is
 *
 * Created by the first `migrate:up` against a server (the migration account
 * has DDL rights), not by a migration file: the check that reads it runs
 * before any migration is applied, so it must cope with the table not
 * existing yet -- that simply means "unlabelled".
 *
 * @see \Ubix\Tests\Repository\DatabaseEnvironment\DatabaseEnvironmentSqlRepositoryTest PHPUnit test case
 */
final class DatabaseEnvironmentSqlRepository implements DatabaseEnvironmentReader, DatabaseEnvironmentWriter
{
    /**
     * MySQL errnos meaning the table (1146) or the SYSTEMS database (1049) does not exist
     */
    private const array MISSING_MYSQL_ERROR_CODES = [1146, 1049];

    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param SqlService $sqlService The database
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBix classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function current(): ?DatabaseEnvironment
    {
        return $this->query(new DatabaseEnvironmentOptions());
    }

    /**
     * {@inheritDoc}
     */
    public function ensureTable(): void
    {
        $this->sqlService->query(
            'CREATE TABLE IF NOT EXISTS ' . $this->table() . ' (
                id           TINYINT UNSIGNED NOT NULL DEFAULT 1,
                environment  VARCHAR(16)      NOT NULL,
                labelled_at  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
                labelled_by  VARCHAR(255)     NOT NULL,
                sanitised_at DATETIME         NULL,
                sanitised_by VARCHAR(255)     NULL,
                PRIMARY KEY (id),
                CONSTRAINT chk_database_environment_single_row CHECK (id = 1)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function label(Env $environment, string $actor): void
    {
        $this->sqlService->query(
            'REPLACE INTO ' . $this->table() . ' (id, environment, labelled_at, labelled_by, sanitised_at, sanitised_by)
             VALUES (1, :environment, NOW(), :actor, NULL, NULL)',
            ['actor' => $actor, 'environment' => $environment->value],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function markSanitised(string $actor): void
    {
        $this->sqlService->query(
            'UPDATE ' . $this->table() . ' SET sanitised_at = NOW(), sanitised_by = :actor WHERE id = 1',
            ['actor' => $actor],
        );
    }

    /**
     * The single row, hydrated
     *
     * @param DatabaseEnvironmentOptions $options Whether a missing table means "unlabelled"
     *
     * @throws DtoException When the read fails for any reason other than a missing table
     *
     * @return ?DatabaseEnvironment The label, or null
     */
    private function query(DatabaseEnvironmentOptions $options): ?DatabaseEnvironment
    {
        try {
            $row = $this->sqlService->getRow(
                'SELECT environment, labelled_at, labelled_by, sanitised_at, sanitised_by FROM ' . $this->table() . ' WHERE id = 1',
            );
        } catch (DtoException $exception) {
            $dto   = $exception->getDto();
            $errno = $dto instanceof PdoError && $dto->driverCode !== null ? (int) $dto->driverCode : null;
            if ($options->missingIsUnlabelled && in_array($errno, self::MISSING_MYSQL_ERROR_CODES, true)) {
                return null;
            }

            throw $exception;
        }

        if ($row === false || !is_string($row['environment'] ?? null)) {
            return null;
        }

        $environment = Env::tryFrom($row['environment']);
        if ($environment === null) {
            return null;
        }

        return new DatabaseEnvironment(
            environment: $environment,
            labelledAt:  is_string($row['labelled_at'] ?? null) ? $row['labelled_at'] : null,
            labelledBy:  is_string($row['labelled_by'] ?? null) ? $row['labelled_by'] : null,
            sanitisedAt: is_string($row['sanitised_at'] ?? null) ? $row['sanitised_at'] : null,
            sanitisedBy: is_string($row['sanitised_by'] ?? null) ? $row['sanitised_by'] : null,
        );
    }

    /**
     * The fully-qualified table, honouring `DATABASE_PREFIX`
     *
     * @return string The table reference
     */
    private function table(): string
    {
        return UbixDatabase::SYSTEMS->databaseName() . '.Database_Environment';
    }
}
