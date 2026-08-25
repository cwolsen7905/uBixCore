<?php

declare(strict_types=1);

namespace Ubix\Service\Sql;

use Ubix\Service\Sql\AbstractPdoSqlService as PdoSqlService;

/**
 * Migration-tier MySQL PDO service. Same shape as
 * `MysqlPdoSqlService` but consumes the `MYSQL_MIGRATION_*`
 * cred override when set, falling back to `MYSQL_WRITE_*`
 * otherwise. Both read and write paths point at the writer
 * cluster (the master) — migration commands must read their
 * own tracker writes immediately and cannot tolerate
 * replica-lag, so we collapse to a single connection pool.
 *
 * Used exclusively by `SchemaMigrationSqlRepository` via an
 * explicit DI override in `UbixCli/src/Dependencies.php`
 * (not the global `SqlService` binding). Other repos in the
 * CLI app continue using `MysqlPdoSqlService` so commands like
 * `flag:set` keep using the app-level creds.
 *
 * Resolution rules mirror `MigrationCredentialResolverService`
 * (which covers the shell-out paths):
 *
 * - **PHPUnit** (`PHPUNIT_RUNNING=1`): both read and write pools
 *   use `TEST_MYSQL_WRITE_*`. Sandbox `root` has all grants.
 * - **Runtime**: host/port/database from `MYSQL_WRITE_*`;
 *   username/password prefer `MYSQL_MIGRATION_*` when either is
 *   non-empty, else fall back to `MYSQL_WRITE_*`.
 *
 * Env-var resolution is **deferred to first PDO use** via the
 * `ensureInitialized()` hook on `AbstractPdoSqlService` so that
 * a `migrate:*` / `seed:*` command's `--target` / `--username`
 * options (applied to the process env by
 * `MigrationConnectionTargetService` at the top of `execute()`)
 * take effect before the connection is built. Reading env in
 * the constructor would have locked in the boot-time values
 * because PHP-DI eagerly instantiates every command at
 * `bin/ubix` startup.
 *
 * @see \Ubix\Tests\Service\Sql\MigrationPdoSqlServiceTest PHPUnit test case
 */
final class MigrationPdoSqlService extends PdoSqlService
{
    private const DSN_SPRINTF_FORMAT = 'mysql:host=%s;port=%s;dbname=%s;charset=latin1';

    private bool $initialized = false;

    /**
     * {@inheritDoc}
     */
    protected function ensureInitialized(): void
    {
        if ($this->initialized) {
            return;
        }
        $this->initialized = true;

        $isPhpUnit = getenv('PHPUNIT_RUNNING') === '1';

        if ($isPhpUnit) {
            $host     = (string) getenv('TEST_MYSQL_WRITE_HOST');
            $port     = (string) getenv('TEST_MYSQL_WRITE_PORT');
            $database = (string) getenv('TEST_MYSQL_WRITE_DATABASE');
            $username = (string) getenv('TEST_MYSQL_WRITE_USERNAME');
            $password = (string) getenv('TEST_MYSQL_WRITE_PASSWORD');
        } else {
            $host     = (string) getenv('MYSQL_WRITE_HOST');
            $port     = (string) getenv('MYSQL_WRITE_PORT');
            $database = (string) getenv('MYSQL_WRITE_DATABASE');

            $migrationUsername = (string) getenv('MYSQL_MIGRATION_USERNAME');
            $migrationPassword = (string) getenv('MYSQL_MIGRATION_PASSWORD');
            $overrideActive    = $migrationUsername !== '' || $migrationPassword !== '';

            $username = $overrideActive ? $migrationUsername : (string) getenv('MYSQL_WRITE_USERNAME');
            $password = $overrideActive ? $migrationPassword : (string) getenv('MYSQL_WRITE_PASSWORD');
        }

        // Under `DATABASE_PREFIX` (the test-tier `--prefix=t<pipeline_id>_`
        // runs and the PHPUnit bootstrap), every schema is materialised as
        // `<prefix><DB>` — the body-apply path and the tracker table already
        // retarget at the prefixed schema, so the connection's home schema
        // must too, or it pins the UNPREFIXED `dbname` and fails with 1049.
        // No-op at runtime (empty prefix). See
        // AbstractPdoSqlService::applyDatabasePrefix().
        $database = $this->applyDatabasePrefix($database);

        $dsn = sprintf(self::DSN_SPRINTF_FORMAT, $host, $port, $database);

        $this->initializePdoConstructorParameters(
            readDsn:       $dsn,
            readUsername:  $username,
            readPassword:  $password,
            writeDsn:      $dsn,
            writeUsername: $username,
            writePassword: $password,
        );
    }
}
