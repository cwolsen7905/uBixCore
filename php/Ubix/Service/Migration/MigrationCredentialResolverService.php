<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\MysqlConnectionParameters;

/**
 * Single source of truth for the MySQL connection parameters
 * consumed by the migration runner's shell-out services
 * (`MigrationApplyService`, `DestructiveBackupService`,
 * `SchemaDiffService`, `SeedApplyService`).
 *
 * Resolution rules:
 *
 * - **PHPUnit context** (`PHPUNIT_RUNNING=1`): all four fields
 *   come from the `TEST_MYSQL_WRITE_*` env vars. Tests run
 *   against the local sandbox `root` user which holds every
 *   grant; there's no need for a separate `TEST_MYSQL_MIGRATION_*`
 *   set.
 * - **Runtime** (every other context): `host` and `port` always
 *   come from `MYSQL_WRITE_*` (the cluster doesn't change; only
 *   the cred set does). `username` and `password` prefer the
 *   `MYSQL_MIGRATION_*` pair when **either** is non-empty,
 *   otherwise fall back to `MYSQL_WRITE_*`.
 *
 * The `MYSQL_MIGRATION_*` override exists because the app-level
 * write user (typically `livewrite2`) lacks DDL grants — `CREATE`,
 * `ALTER`, `DROP`. The cutover runbook
 * (`docs/projects/migrations/cutover-runbook.md` §2) calls for a
 * dedicated DBA-or-migration-role cred set when running
 * `migrate:up` against a real cluster; this resolver wires those
 * env vars into every shell-out path in the migration runner.
 *
 * Lives in `Ubix\Service\Migration\` rather than as a generic
 * connection-resolver because the resolution rules are
 * migration-tier-specific (the app's PDO-backed `SqlService`
 * paths read `MYSQL_WRITE_*` directly via the container, not
 * through this service).
 *
 * @see \Ubix\Tests\Service\Migration\MigrationCredentialResolverServiceTest PHPUnit test case
 */
final class MigrationCredentialResolverService
{
    /**
     * Constructor
     *
     * @param Logger $logger PSR-3 logger (Ubix standards-test requires every service to accept one)
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Resolve the active MySQL connection parameters for a
     * migration-tier shell-out.
     *
     * @return MysqlConnectionParameters Connection tuple (host, port, username, password)
     */
    public function resolve(): MysqlConnectionParameters
    {
        if (getenv('PHPUNIT_RUNNING') === '1') {
            return new MysqlConnectionParameters(
                host:     (string) getenv('TEST_MYSQL_WRITE_HOST'),
                port:     (string) getenv('TEST_MYSQL_WRITE_PORT'),
                username: (string) getenv('TEST_MYSQL_WRITE_USERNAME'),
                password: (string) getenv('TEST_MYSQL_WRITE_PASSWORD'),
            );
        }

        $migrationUsername = (string) getenv('MYSQL_MIGRATION_USERNAME');
        $migrationPassword = (string) getenv('MYSQL_MIGRATION_PASSWORD');
        $overrideActive    = $migrationUsername !== '' || $migrationPassword !== '';

        return new MysqlConnectionParameters(
            host:     (string) getenv('MYSQL_WRITE_HOST'),
            port:     (string) getenv('MYSQL_WRITE_PORT'),
            username: $overrideActive ? $migrationUsername : (string) getenv('MYSQL_WRITE_USERNAME'),
            password: $overrideActive ? $migrationPassword : (string) getenv('MYSQL_WRITE_PASSWORD'),
        );
    }
}
