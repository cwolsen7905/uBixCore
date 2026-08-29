<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\Enum\Env;
use Ubix\Enum\UbixDatabase;
use Ubix\Service\Migration\DestructiveBackupService;
use Ubix\Service\Migration\MigrationCredentialResolverService;
use Ubix\Service\ProcessService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Migration\DestructiveBackupService
 *
 * @coversDefaultClass \Ubix\Service\Migration\DestructiveBackupService
 * @coversDefaultClass \Ubis\Service\Migration\DestructiveBackupService
 */
final class DestructiveBackupServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * A real table that exists in the prefixed SYSTEMS test schema, used so
     * the genuine `mariadb-dump` shell-out exits zero on the success path.
     */
    private const REAL_TABLE = 'Clusters';

    /**
     * Per-run backup root the service writes snapshots under; created in
     * setUp() and recursively removed in tearDown().
     */
    private string $backupDirectory = '';

    /**
     * Previous value of the backup-dir env var (false = was unset), so the
     * override never leaks to other tests.
     */
    private string|false $originalBackupDirectory = false;

    /**
     * Stand up an isolated backup directory and point the service at it.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->originalBackupDirectory = getenv('NEPTUNE_MIGRATION_BACKUP_DIR');
        $this->backupDirectory         = sys_get_temp_dir() . '/ubix-backup-test-9011000-' . getmypid();
        putenv('NEPTUNE_MIGRATION_BACKUP_DIR=' . $this->backupDirectory);
    }

    /**
     * Restore the backup-dir env var and remove every artefact written.
     *
     * @return void
     */
    public function tearDown(): void
    {
        if ($this->originalBackupDirectory === false) {
            putenv('NEPTUNE_MIGRATION_BACKUP_DIR');
        } else {
            putenv('NEPTUNE_MIGRATION_BACKUP_DIR=' . $this->originalBackupDirectory);
        }

        $this->removeDirectory($this->backupDirectory);
    }

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DestructiveBackupService::class);
    }

    /**
     * On dev the snapshot is a no-op: no directory, no shell-out, null return.
     *
     * @return void
     *
     * @covers ::snapshot
     */
    public function testSnapshotIsNoOpOnDev(): void
    {
        $file = $this->destructiveFile('DROP TABLE ' . self::REAL_TABLE . ';');

        $result = $this->service()->snapshot($file, Env::DEV);

        $this->assertNull($result);
        $this->assertDirectoryDoesNotExist($this->backupDirectory);
    }

    /**
     * On sandbox the snapshot is likewise skipped.
     *
     * @return void
     *
     * @covers ::snapshot
     */
    public function testSnapshotIsNoOpOnSandbox(): void
    {
        $file = $this->destructiveFile('DROP TABLE ' . self::REAL_TABLE . ';');

        $result = $this->service()->snapshot($file, Env::SANDBOX);

        $this->assertNull($result);
        $this->assertDirectoryDoesNotExist($this->backupDirectory);
    }

    /**
     * On the test environment the snapshot is also skipped.
     *
     * @return void
     *
     * @covers ::snapshot
     */
    public function testSnapshotIsNoOpOnTest(): void
    {
        $file = $this->destructiveFile('DROP TABLE ' . self::REAL_TABLE . ';');

        $result = $this->service()->snapshot($file, Env::TEST);

        $this->assertNull($result);
        $this->assertDirectoryDoesNotExist($this->backupDirectory);
    }

    /**
     * A destructive migration whose body has no parseable table reference
     * fails loud rather than applying without a verifiable snapshot.
     *
     * @return void
     *
     * @covers ::snapshot
     */
    public function testSnapshotThrowsWhenNoTablesCanBeExtracted(): void
    {
        $file = $this->destructiveFile('CALL some_destructive_procedure();', 'migration_9011000_no_tables');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('migration_9011000_no_tables');

        $this->service()->snapshot($file, Env::STAGING);
    }

    /**
     * On staging a destructive migration produces a real gzip snapshot whose
     * path is rooted at the configured backup directory and namespaced by the
     * migration id and environment.
     *
     * @return void
     *
     * @covers ::snapshot
     */
    public function testSnapshotWritesGzipArtefactOnStaging(): void
    {
        $file = $this->destructiveFile(
            'DROP TABLE ' . self::REAL_TABLE . ';',
            'migration_9011001_staging_drop',
        );

        $path = $this->service()->snapshot($file, Env::STAGING);

        $this->assertNotNull($path);
        $this->assertStringStartsWith(
            $this->backupDirectory . '/migration_9011001_staging_drop/',
            $path,
        );
        $this->assertStringStartsWith('staging-', basename($path));
        $this->assertStringEndsWith('.sql.gz', $path);
        $this->assertFileExists($path);
        $this->assertSame("\x1f\x8b", substr((string) file_get_contents($path), 0, 2));
    }

    /**
     * On prod the snapshot path is namespaced by the prod environment value,
     * confirming the env feeds the artefact naming.
     *
     * @return void
     *
     * @covers ::snapshot
     */
    public function testSnapshotNamesArtefactByProdEnvironment(): void
    {
        $file = $this->destructiveFile(
            'TRUNCATE TABLE ' . self::REAL_TABLE . ';',
            'migration_9011002_prod_truncate',
        );

        $path = $this->service()->snapshot($file, Env::PROD);

        $this->assertNotNull($path);
        $this->assertStringStartsWith('prod-', basename($path));
        $this->assertFileExists($path);
    }

    /**
     * Build a destructive MigrationFile targeting the prefixed SYSTEMS test
     * schema so the real mariadb-dump shell-out can find the seeded tables.
     *
     * @param string $body Destructive SQL body
     * @param string $id   Migration id / filename stem
     *
     * @return MigrationFile
     */
    private function destructiveFile(string $body, string $id = 'migration_9011000_destructive'): MigrationFile
    {
        return new MigrationFile(
            id:                $id,
            targetDatabase:    UbixDatabase::SYSTEMS->databaseName(),
            description:       'Destructive migration fixture',
            author:            'Test Author',
            body:              $body,
            checksum:          hash('sha256', $body),
            filePath:          '/tmp/' . $id . '.sql',
            destructiveReason: 'Removing a deprecated table',
        );
    }

    /**
     * Build the service with real collaborators (only the logger is stubbed,
     * per the no-mock-internals policy).
     *
     * @return DestructiveBackupService
     */
    private function service(): DestructiveBackupService
    {
        $logger = $this->createStub(Logger::class);

        return new DestructiveBackupService(
            $logger,
            new ProcessService($logger),
            new MigrationCredentialResolverService($logger),
        );
    }

    /**
     * Recursively delete a directory and its contents.
     *
     * @param string $directory Absolute path to remove
     *
     * @return void
     */
    private function removeDirectory(string $directory): void
    {
        if ($directory === '' || ! is_dir($directory)) {
            return;
        }

        $entries = scandir($directory);
        if ($entries === false) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $directory . '/' . $entry;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
