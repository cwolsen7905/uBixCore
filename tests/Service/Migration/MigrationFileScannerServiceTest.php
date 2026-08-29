<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\Migration\DestructiveStatementDetectorService;
use Ubix\Service\Migration\HotTableAlterDetectorService;
use Ubix\Service\Migration\MigrationFileParserService;
use Ubix\Service\Migration\MigrationFileScannerService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Migration\MigrationFileScannerService
 *
 * @coversDefaultClass \Ubix\Service\Migration\MigrationFileScannerService
 * @coversDefaultClass \Ubis\Service\Migration\MigrationFileScannerService
 */
final class MigrationFileScannerServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const KNOWN_MIGRATION_ID = '00000000000000_init_schema_migrations';

    /**
     * Absolute paths of temp files/dirs created by a test, removed in
     * tearDown.
     *
     * @var string[]
     */
    private array $tempPaths = [];

    /**
     * Remove any temp files/dirs a test created (files first, then dirs).
     *
     * @return void
     */
    public function tearDown(): void
    {
        foreach ($this->tempPaths as $tempPath) {
            if (is_file($tempPath)) {
                unlink($tempPath);
            }
        }
        foreach ($this->tempPaths as $tempPath) {
            if (is_dir($tempPath)) {
                rmdir($tempPath);
            }
        }
        $this->tempPaths = [];
    }

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationFileScannerService::class);
    }

    /**
     * Scans the real committed migrations directory, parses every file in
     * chronological (sorted) order, and includes a known migration.
     *
     * @return void
     *
     * @covers ::scan
     */
    public function testScanParsesAndSortsAllMigrationFiles(): void
    {
        $migrations = $this->service()->scan();

        $this->assertNotEmpty($migrations);

        $ids = [];
        foreach ($migrations as $migration) {
            $ids[] = $migration->id;
        }

        $this->assertContains(self::KNOWN_MIGRATION_ID, $ids);

        $sortedIds = $ids;
        sort($sortedIds);
        $this->assertSame($sortedIds, $ids);
    }

    /**
     * Throws when the migrations directory does not exist.
     *
     * @return void
     *
     * @covers ::scan
     */
    public function testScanThrowsWhenDirectoryMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service('/nonexistent/migrations/directory')->scan();
    }

    /**
     * Throws when the directory contains a file whose name does not match
     * the standard `YYYYMMDDHHMMSS_<snake_case>.sql` format.
     *
     * @return void
     *
     * @covers ::scan
     */
    public function testScanThrowsForNonStandardFilename(): void
    {
        $dir = rtrim(sys_get_temp_dir(), '/') . '/ubix_migrations_bad_' . getmypid();
        mkdir($dir);
        $this->tempPaths[] = $dir;

        $badFile = $dir . '/not-a-valid-migration-name.sql';
        file_put_contents($badFile, "-- Migration: x\n");
        $this->tempPaths[] = $badFile;

        $this->expectException(InvalidArgumentException::class);

        $this->service($dir)->scan();
    }

    /**
     * Build the scanner with a real parser, pointed at the real committed
     * migrations directory unless a path override is supplied.
     *
     * @param ?string $migrationsPath Override migrations directory (default: the repo's sql/migrations)
     *
     * @return MigrationFileScannerService
     */
    private function service(?string $migrationsPath = null): MigrationFileScannerService
    {
        $logger = $this->createStub(Logger::class);

        return new MigrationFileScannerService(
            $logger,
            new MigrationFileParserService($logger, new DestructiveStatementDetectorService($logger), new HotTableAlterDetectorService($logger)),
            $migrationsPath ?? dirname(__DIR__, 3) . '/sql/migrations',
        );
    }
}
