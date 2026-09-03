<?php

declare(strict_types=1);

namespace Ubix\Tests;

use DI\Container;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Abstract class for a PHPUnit test case with added Container singleton functionality
 *
 * Ships with uBixCore (`vendor/ubixsys/ubixcore`), so it locates the host project
 * through `UBIX_PROJECT_ROOT` (exported by `Ubix\Bootstrap\environment()`) or the
 * working directory - never through its own `__DIR__`. A host whose CLI container
 * definitions live somewhere other than `app/UbixCli/src/Dependencies.php`
 * overrides `getDependenciesFile()`.
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
        return $this->getProjectRoot() . '/app/UbixCli/src/Dependencies.php';
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
                putenv('APP_NAME=NeptuneCli');
            }

            Dotenv::createUnsafeImmutable($this->getProjectRoot())->load();

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
}
