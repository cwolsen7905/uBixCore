<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\Migration\MigrationCredentialResolverService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\Service\Migration\MigrationCredentialResolverService
 *
 * @coversDefaultClass \Ubix\Service\Migration\MigrationCredentialResolverService
 * @coversDefaultClass \Ubis\Service\Migration\MigrationCredentialResolverService
 */
final class MigrationCredentialResolverServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * The environment variables this service reads, captured before each
     * test and restored after so the toggling never leaks.
     */
    private const MANAGED_ENV_VARS = [
        'PHPUNIT_RUNNING',
        'MYSQL_MIGRATION_USERNAME',
        'MYSQL_MIGRATION_PASSWORD',
        'MYSQL_WRITE_HOST',
        'MYSQL_WRITE_PORT',
        'MYSQL_WRITE_USERNAME',
        'MYSQL_WRITE_PASSWORD',
        'TEST_MYSQL_WRITE_HOST',
        'TEST_MYSQL_WRITE_PORT',
        'TEST_MYSQL_WRITE_USERNAME',
        'TEST_MYSQL_WRITE_PASSWORD',
    ];

    /**
     * Original values of the managed env vars (false = was unset).
     *
     * @var array<string, string|false>
     */
    private array $originalEnv = [];

    /**
     * Capture the managed env vars before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        foreach (self::MANAGED_ENV_VARS as $name) {
            $this->originalEnv[$name] = getenv($name);
        }
    }

    /**
     * Restore the managed env vars after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        foreach ($this->originalEnv as $name => $value) {
            if ($value === false) {
                putenv($name);
            } else {
                putenv($name . '=' . $value);
            }
        }
    }

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationCredentialResolverService::class);
    }

    /**
     * Under PHPUnit the resolver routes to the TEST_MYSQL_WRITE_* tuple so
     * migration shell-outs never touch a runtime cluster.
     *
     * @return void
     *
     * @covers ::resolve
     */
    public function testResolveUsesTestCredentialsUnderPhpunit(): void
    {
        putenv('PHPUNIT_RUNNING=1');
        putenv('TEST_MYSQL_WRITE_HOST=test-host');
        putenv('TEST_MYSQL_WRITE_PORT=3399');
        putenv('TEST_MYSQL_WRITE_USERNAME=test-user');
        putenv('TEST_MYSQL_WRITE_PASSWORD=test-pass');

        $params = $this->service()->resolve();

        $this->assertSame('test-host', $params->host);
        $this->assertSame('3399', $params->port);
        $this->assertSame('test-user', $params->username);
        $this->assertSame('test-pass', $params->password);
    }

    /**
     * Outside PHPUnit, dedicated migration credentials override the
     * standard write credentials when present.
     *
     * @return void
     *
     * @covers ::resolve
     */
    public function testResolveUsesMigrationOverrideOutsidePhpunit(): void
    {
        putenv('PHPUNIT_RUNNING=0');
        putenv('MYSQL_WRITE_HOST=write-host');
        putenv('MYSQL_WRITE_PORT=3306');
        putenv('MYSQL_WRITE_USERNAME=write-user');
        putenv('MYSQL_WRITE_PASSWORD=write-pass');
        putenv('MYSQL_MIGRATION_USERNAME=migration-user');
        putenv('MYSQL_MIGRATION_PASSWORD=migration-pass');

        $params = $this->service()->resolve();

        $this->assertSame('write-host', $params->host);
        $this->assertSame('migration-user', $params->username);
        $this->assertSame('migration-pass', $params->password);
    }

    /**
     * Outside PHPUnit, with no migration credentials set, the resolver
     * falls back to the standard write credentials.
     *
     * @return void
     *
     * @covers ::resolve
     */
    public function testResolveFallsBackToWriteCredentialsOutsidePhpunit(): void
    {
        putenv('PHPUNIT_RUNNING=0');
        putenv('MYSQL_MIGRATION_USERNAME');
        putenv('MYSQL_MIGRATION_PASSWORD');
        putenv('MYSQL_WRITE_HOST=write-host');
        putenv('MYSQL_WRITE_PORT=3306');
        putenv('MYSQL_WRITE_USERNAME=write-user');
        putenv('MYSQL_WRITE_PASSWORD=write-pass');

        $params = $this->service()->resolve();

        $this->assertSame('write-user', $params->username);
        $this->assertSame('write-pass', $params->password);
    }

    /**
     * Build the service (its only dependency is a logger).
     *
     * @return MigrationCredentialResolverService
     */
    private function service(): MigrationCredentialResolverService
    {
        return new MigrationCredentialResolverService($this->createStub(Logger::class));
    }
}
