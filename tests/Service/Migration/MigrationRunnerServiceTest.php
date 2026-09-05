<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\Enum\UbixDatabase;
use Ubix\Repository\SchemaMigration\SchemaMigrationSqlRepository;
use Ubix\Service\Migration\DestructiveStatementDetectorService;
use Ubix\Service\Migration\HotTableAlterDetectorService;
use Ubix\Service\Migration\MigrationFileParserService;
use Ubix\Service\Migration\MigrationFileScannerService;
use Ubix\Service\Migration\MigrationRunnerService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Migration\MigrationRunnerService
 *
 * @coversDefaultClass \Ubix\Service\Migration\MigrationRunnerService
 * @coversDefaultClass \Ubis\Service\Migration\MigrationRunnerService
 */
final class MigrationRunnerServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * A migration ID that is guaranteed to exist on disk in the committed
     * `sql/migrations/` directory — the same one the scanner test pins.
     */
    private const KNOWN_MIGRATION_ID = '20260828000000_runner_service_fixture';

    /**
     * The target database declared in the known migration's header.
     */
    private const KNOWN_MIGRATION_DATABASE = 'ubixcore_test'; // The framework's fixture schema (sql/ubixcore_test.sql)

    /**
     * Seed-only tracker IDs this test inserts; removed in tearDown via
     * targeted DELETEs so the shared tracker is left untouched.
     */
    private const SEED_TRACKER_ID = '9014000_runner_service_seed';

    private string $migrationsPath = '';

    /**
     * Remove every tracker row this test seeded — both the synthetic
     * seed ID and any real committed ID we recorded as applied.
     *
     * @return void
     */

    /**
     * Write one fixture migration into a private temp directory so the runner
     * scans a known file set (the committed sql/migrations/ only holds the
     * bootstrap migration, which the scanner special-cases).
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->migrationsPath = rtrim(sys_get_temp_dir(), '/') . '/ubix_runner_migrations_' . getmypid();
        if (! is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath);
        }

        file_put_contents(
            $this->migrationsPath . '/' . self::KNOWN_MIGRATION_ID . '.sql',
            implode("\n", [
                '-- Migration: ' . self::KNOWN_MIGRATION_ID,
                '-- Database: ' . self::KNOWN_MIGRATION_DATABASE,
                '-- Description: Runner service fixture',
                '-- Author: Ubix Test',
                '',
                'CREATE TABLE IF NOT EXISTS ' . self::KNOWN_MIGRATION_DATABASE . '.Runner_Service_Fixture (id INT UNSIGNED NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB;',
                '',
            ]),
        );
    }

    /**
     * Remove the fixture directory and any tracker rows this test wrote.
     *
     * @return void
     */
    public function tearDown(): void
    {
        foreach ((array) glob($this->migrationsPath . '/*') as $file) {
            if (is_string($file)) {
                unlink($file);
            }
        }
        if (is_dir($this->migrationsPath)) {
            rmdir($this->migrationsPath);
        }

        $table = UbixDatabase::SYSTEMS->databaseName() . '.Schema_Migrations';
        $this->getSqlService()->query(
            'DELETE FROM ' . $table . ' WHERE id IN (:seedId, :knownId)',
            ['knownId' => self::KNOWN_MIGRATION_ID, 'seedId' => self::SEED_TRACKER_ID],
        );
    }

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationRunnerService::class);
    }

    /**
     * Looks up a single on-disk migration by ID and returns its parsed DTO.
     *
     * @return void
     *
     * @covers ::getMigrationById
     */
    public function testGetMigrationByIdReturnsParsedFileForKnownId(): void
    {
        $file = $this->service()->getMigrationById(self::KNOWN_MIGRATION_ID);

        $this->assertNotNull($file);
        $this->assertSame(self::KNOWN_MIGRATION_ID, $file->id);
        $this->assertSame(self::KNOWN_MIGRATION_DATABASE, $file->targetDatabase);
    }

    /**
     * Returns null when no on-disk migration matches the given ID.
     *
     * @return void
     *
     * @covers ::getMigrationById
     */
    public function testGetMigrationByIdReturnsNullForUnknownId(): void
    {
        $this->assertNull($this->service()->getMigrationById('99999999999999_no_such_migration'));
    }

    /**
     * Reports an unrecorded migration as not applied.
     *
     * @return void
     *
     * @covers ::isMigrationApplied
     */
    public function testIsMigrationAppliedReturnsFalseWhenNotRecorded(): void
    {
        $this->assertFalse($this->service()->isMigrationApplied(self::SEED_TRACKER_ID));
    }

    /**
     * Reports a migration as applied once its tracker row exists.
     *
     * @return void
     *
     * @covers ::isMigrationApplied
     */
    public function testIsMigrationAppliedReturnsTrueWhenTrackerRowExists(): void
    {
        $this->seedTrackerRow(self::SEED_TRACKER_ID, 'SYSTEMS');

        $this->assertTrue($this->service()->isMigrationApplied(self::SEED_TRACKER_ID));
    }

    /**
     * Surfaces a committed on-disk migration as pending while it has no
     * tracker row.
     *
     * @return void
     *
     * @covers ::getPendingMigrations
     */
    public function testGetPendingMigrationsIncludesUnappliedFile(): void
    {
        $pendingIds = $this->idsOf($this->service()->getPendingMigrations());

        $this->assertContains(self::KNOWN_MIGRATION_ID, $pendingIds);
    }

    /**
     * Drops a migration from the pending list once its tracker row is
     * recorded as applied.
     *
     * @return void
     *
     * @covers ::getPendingMigrations
     */
    public function testGetPendingMigrationsExcludesAppliedFile(): void
    {
        $service = $this->service();

        $this->assertContains(self::KNOWN_MIGRATION_ID, $this->idsOf($service->getPendingMigrations()));

        $this->seedTrackerRow(self::KNOWN_MIGRATION_ID, self::KNOWN_MIGRATION_DATABASE);

        $this->assertNotContains(self::KNOWN_MIGRATION_ID, $this->idsOf($service->getPendingMigrations()));
    }

    /**
     * Restricts the pending list to a single target database.
     *
     * @return void
     *
     * @covers ::getPendingMigrations
     */
    public function testGetPendingMigrationsHonoursDatabaseFilter(): void
    {
        $pending = $this->service()->getPendingMigrations(self::KNOWN_MIGRATION_DATABASE);

        $this->assertContains(self::KNOWN_MIGRATION_ID, $this->idsOf($pending));
        foreach ($pending as $file) {
            $this->assertSame(self::KNOWN_MIGRATION_DATABASE, $file->targetDatabase);
        }
    }

    /**
     * Returns an empty pending list for a target database that owns no
     * committed migration files.
     *
     * @return void
     *
     * @covers ::getPendingMigrations
     */
    public function testGetPendingMigrationsReturnsEmptyForUnknownDatabase(): void
    {
        $this->assertSame([], $this->service()->getPendingMigrations('NO_SUCH_DATABASE'));
    }

    /**
     * Acquires the advisory lock against the live connection and then
     * releases it, leaving the mutex free for the next acquirer.
     *
     * @return void
     *
     * @covers ::acquireAdvisoryLock
     * @covers ::releaseAdvisoryLock
     */
    public function testAcquireAndReleaseAdvisoryLock(): void
    {
        $service = $this->service();

        $this->assertTrue($service->acquireAdvisoryLock(0));

        $service->releaseAdvisoryLock();

        $this->assertTrue($service->acquireAdvisoryLock(0));
        $service->releaseAdvisoryLock();
    }

    /**
     * Build the runner with its real collaborators: a real file scanner
     * pointed at the committed `sql/migrations/` directory and a real
     * SQL-backed Schema_Migrations reader against the test tracker.
     *
     * @return MigrationRunnerService
     */
    private function service(): MigrationRunnerService
    {
        $logger = $this->createStub(Logger::class);

        $scanner = new MigrationFileScannerService(
            $logger,
            new MigrationFileParserService($logger, new DestructiveStatementDetectorService($logger), new HotTableAlterDetectorService($logger)),
            $this->migrationsPath,
        );

        $reader = new SchemaMigrationSqlRepository($logger, $this->getSqlService());

        return new MigrationRunnerService($logger, $scanner, $reader);
    }

    /**
     * Collect the IDs of a list of migration file DTOs.
     *
     * @param MigrationFile[] $files Parsed migration files
     *
     * @return string[]
     */
    private function idsOf(array $files): array
    {
        $ids = [];
        foreach ($files as $file) {
            $ids[] = $file->id;
        }
        return $ids;
    }

    /**
     * Insert one applied-migration tracker row supplying every NOT-NULL
     * column without a default per the bootstrap schema.
     *
     * @param string $id             Migration ID to record as applied
     * @param string $targetDatabase Target database name for the row
     *
     * @return void
     */
    private function seedTrackerRow(string $id, string $targetDatabase): void
    {
        $table = UbixDatabase::SYSTEMS->databaseName() . '.Schema_Migrations';
        $this->insertSeedData(
            'INSERT INTO ' . $table . '
                (id, target_database, description, checksum, applied_by, duration_ms)
             VALUES
                (:id, :target_database, :description, :checksum, :applied_by, :duration_ms)',
            [
                'applied_by'      => 'phpunit',
                'checksum'        => str_repeat('0', 64),
                'description'     => 'Seeded by MigrationRunnerServiceTest',
                'duration_ms'     => 0,
                'id'              => $id,
                'target_database' => $targetDatabase,
            ],
        );
    }
}
