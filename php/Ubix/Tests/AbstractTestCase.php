<?php

declare(strict_types=1);

namespace Ubix\Tests;

use DI\Container;
use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Ubix\Service\JsonService;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\Service\Vault\VaultCredentialResolverService;
use Ubix\Service\Vault\VaultService;

/**
 * Abstract class for a PHPUnit test case with added Container singleton functionality
 *
 * Ships with uBixCore (`vendor/ubixsys/ubixcore`), so it locates the host project
 * through `UBIX_PROJECT_ROOT` (exported by `Ubix\Bootstrap\environment()`) or the
 * working directory - never through its own `__DIR__`. The CLI container comes from
 * `app/UbixCli/src/Dependencies.php` when the host has one, else the framework
 * default; override `getDependenciesFile()` for anything else.
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * @var array<string, bool> $seeded
     */
    protected static array $seeded = [];

    private static ?SqlService $sqlService = null;

    private static ?Container $container = null;

    /**
     * Get the host project root (`UBIX_PROJECT_ROOT`, else the working directory)
     *
     * @return string
     */
    protected function getProjectRoot(): string
    {
        $root = getenv('UBIX_PROJECT_ROOT');

        return is_string($root) && $root !== '' ? $root : (getcwd() ?: '.');
    }

    /**
     * Get the file that returns the CLI container-building closure
     *
     * @return string
     */
    protected function getDependenciesFile(): string
    {
        $hostFile = $this->getProjectRoot() . '/app/UbixCli/src/Dependencies.php';

        return file_exists($hostFile) ? $hostFile : __DIR__ . '/../Bootstrap/cli-dependencies.php';
    }

    /**
     * Get the SQL service
     *
     * @return SqlService
     */
    protected function getSqlService(): SqlService
    {
        if (self::$sqlService === null) {
            $sqlService = $this->getContainer()->get(SqlService::class);
            assert($sqlService instanceof SqlService);
            self::$sqlService = $sqlService;
        }
        return self::$sqlService;
    }

    /**
     * Get the DI container
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        if (self::$container === null) {
            if (getenv('APP_NAME') === false) {
                putenv('APP_NAME=UbixCli');
            }

            if (file_exists($this->getProjectRoot() . '/.env')) { // .env is a local-development convenience; CI and containers get their environment from uBixVault / the pod spec
                Dotenv::createUnsafeImmutable($this->getProjectRoot())->load();
            }

            $this->hydrateTestDatabaseFromVault();

            // Map The Databases
            putenv('MYSQL_READ_HOST=' . getenv('TEST_MYSQL_WRITE_HOST'));
            putenv('MYSQL_WRITE_HOST=' . getenv('TEST_MYSQL_WRITE_HOST'));
            putenv('MYSQL_READ_USERNAME=' . getenv('TEST_MYSQL_WRITE_USERNAME'));
            putenv('MYSQL_WRITE_USERNAME=' . getenv('TEST_MYSQL_WRITE_USERNAME'));
            putenv('MYSQL_READ_PASSWORD=' . getenv('TEST_MYSQL_WRITE_PASSWORD'));
            putenv('MYSQL_WRITE_PASSWORD=' . getenv('TEST_MYSQL_WRITE_PASSWORD'));
            putenv('MYSQL_READ_PORT=' . getenv('TEST_MYSQL_WRITE_PORT'));
            putenv('MYSQL_WRITE_PORT=' . getenv('TEST_MYSQL_WRITE_PORT'));
            putenv('MEMCACHE_SERVERS=localhost:11211');

            /**
             * @var callable(): Container $containerBuilder
             */
            $containerBuilder = require $this->getDependenciesFile();
            $container        = $containerBuilder();
            assert($container instanceof Container);
            self::$container = $container;
        }

        return self::$container;
    }

    /**
     * Insert seed data into the database
     *
     * @param string                                        $sql        The SQL query to execute
     * @param array<int|string, bool|float|int|string|null> $parameters The parameters for the SQL query
     *
     * @return void
     *
     * @throws RuntimeException Prevent unsafe operations on non-local databases
     */
    protected function insertSeedData(string $sql, array $parameters = []): void
    {
        if (stripos($sql, 'TRUNCATE') !== false && getenv('PHPUNIT_RUNNING') !== '1') {
            throw new RuntimeException('TRUNCATE statements are only allowed when PHPUNIT_RUNNING is set to 1 in the environment');
        }
        $this->getSqlService()->query($sql, $parameters);
    }

    /**
     * Resolve the test database connection from uBix Vault, when one is configured
     *
     * A no-op unless both `VAULT_ADDR` and `VAULT_TEST_DB_KV_PATH` are set, so a
     * host with no Vault keeps using its git-ignored `.env`. When they are set, the
     * suite needs **no database credentials in a file at all** — which is the point:
     * a password in a plaintext `.env` is copied between machines, survives in
     * backups, and is rotated only by someone remembering to.
     *
     * Runs after Dotenv so Vault wins over any stale value a `.env` still carries.
     *
     * @return void
     */
    private function hydrateTestDatabaseFromVault(): void
    {
        $vaultAddress = getenv('VAULT_ADDR');

        if (!is_string($vaultAddress) || trim($vaultAddress) === '' || getenv('VAULT_TEST_DB_KV_PATH') === false) {
            return;
        }

        $logger = new MonologLogger('vault-test-db');
        $logger->pushHandler(new StreamHandler('php://stderr', Level::Warning));

        $resolver = new VaultCredentialResolverService(
            $logger,
            new VaultService($logger, new Client(), new JsonService($logger)),
        );

        $resolver->hydrateTestDatabase(trim($vaultAddress));
    }
}
