<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Migration;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * MySQL connection-parameter tuple consumed by the migration
 * runner's shell-out services (`MigrationApplyService`,
 * `DestructiveBackupService`, `SchemaDiffService`,
 * `SeedApplyService`).
 *
 * Produced by `MigrationCredentialResolverService::resolve()`
 * which centralises env-var picking so all four sites share one
 * source of truth — see `docs/standards/migrations.md` §11.5
 * (DDL-credential separation).
 *
 * @see \Ubix\Tests\DataTransferObject\Migration\MysqlConnectionParametersTest PHPUnit test case
 */
final readonly class MysqlConnectionParameters implements Dto
{
    /**
     * Constructor
     *
     * @param string $host     MySQL host (FQDN or IP)
     * @param string $port     Numeric port as string (matches the env-var shape)
     * @param string $username Connection username
     * @param string $password Connection password
     */
    public function __construct(
        public string $host,
        public string $port,
        public string $username,
        public string $password,
    ) {
    }
}
