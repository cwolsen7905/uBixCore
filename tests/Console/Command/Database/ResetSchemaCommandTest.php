<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Database;

use ReflectionClass;
use Ubix\Console\Command\Database\ResetSchemaCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Database\ResetSchemaCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Database\ResetSchemaCommand
 */
final class ResetSchemaCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ResetSchemaCommand::class);
    }

    /**
     * The credentials file is private to its owner and holds the password, so the
     * password never reaches argv — which the process list exposes to every local
     * user, and which this command echoes verbatim when a client call fails.
     *
     * @return void
     *
     * @covers ::writeDefaultsFile
     */
    public function testDefaultsFileKeepsCredentialsOffTheCommandLine(): void
    {
        $password = 'pa55word-that-must-not-leak';
        $path     = $this->invokeWriteDefaultsFile('schema_writer', $password, '127.0.0.1', '3307');

        $this->assertIsString($path);
        $contents = file_get_contents($path);
        $this->assertIsString($contents);

        $this->assertStringContainsString('[client]', $contents);
        $this->assertStringContainsString('password=' . $password, $contents);
        $this->assertStringContainsString('user=schema_writer', $contents);
        $this->assertStringContainsString('host=127.0.0.1', $contents);
        $this->assertStringContainsString('port=3307', $contents);

        $permissions = fileperms($path);
        $this->assertIsInt($permissions);
        $this->assertSame(0600, $permissions & 0777);

        unlink($path);
    }

    /**
     * Hosts name their own client binary. MariaDB renamed it and deprecated the
     * `mysql` name; MySQL ships no `mariadb`. The framework must assume neither.
     *
     * @return void
     *
     * @covers ::clientBinary
     */
    public function testClientBinaryHonoursTheHostSetting(): void
    {
        putenv('MYSQL_CLIENT_BINARY=/opt/db/bin/mariadb');

        try {
            $reflection = new ReflectionClass(ResetSchemaCommand::class);
            $command    = $reflection->newInstanceWithoutConstructor();

            $this->assertSame('/opt/db/bin/mariadb', $reflection->getMethod('clientBinary')->invoke($command));
        } finally {
            putenv('MYSQL_CLIENT_BINARY');
        }
    }

    /**
     * Write a defaults file through the private method under test.
     *
     * @param string $user     Database user
     * @param string $password Database password
     * @param string $host     Database host
     * @param string $port     Database port
     *
     * @return ?string Path to the file
     */
    private function invokeWriteDefaultsFile(string $user, string $password, string $host, string $port): ?string
    {
        $reflection = new ReflectionClass(ResetSchemaCommand::class);
        $command    = $reflection->newInstanceWithoutConstructor();

        $path = $reflection->getMethod('writeDefaultsFile')->invoke($command, $user, $password, $host, $port);
        assert($path === null || is_string($path));

        return $path;
    }
}
