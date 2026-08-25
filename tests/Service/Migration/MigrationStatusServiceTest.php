<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\UbixDatabase;
use Ubix\Repository\SchemaMigration\SchemaMigrationSqlRepository;
use Ubix\Service\Migration\DestructiveStatementDetectorService;
use Ubix\Service\Migration\HotTableAlterDetectorService;
use Ubix\Service\Migration\MigrationFileParserService;
use Ubix\Service\Migration\MigrationFileScannerService;
use Ubix\Service\Migration\MigrationStatusService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\Service\Migration\MigrationStatusService
 *
 * @coversDefaultClass \Ubix\Service\Migration\MigrationStatusService
 * @coversDefaultClass \Ubis\Service\Migration\MigrationStatusService
 */
final class MigrationStatusServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const APPLIED_BY = 'cli:ubix-status-test';

    private const APPLIED_MIGRATION_ID = '20269015000001_ubix_status_service_applied';

    private const DESTRUCTIVE_MIGRATION_ID = '20269015000003_ubix_status_service_destructive';

    private const DRIFT_MIGRATION_ID = '20269015000004_ubix_status_service_drift';

    private const OTHER_DATABASE_MIGRATION_ID = '20269015000005_ubix_status_service_other_database';

    private const PENDING_MIGRATION_ID = '20269015000002_ubix_status_service_pending';

    /**
     * Absolute path to the per-test temp migrations directory.
     */
    private string $migrationsPath = '';

    /**
     * Create an empty per-test migrations directory.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->migrationsPath = rtrim(sys_get_temp_dir(), '/') . '/ubix_status_migrations_' . getmypid();
        if (! is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath);
        }
    }

    /**
     * Remove the temp migration files/dir and any seeded tracker rows.
     *
     * @return void
     */
    public function tearDown(): void
    {
        if (is_dir($this->migrationsPath)) {
            foreach ((array) glob($this->migrationsPath . '/*') as $file) {
                if (is_string($file) && is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->migrationsPath);
        }

        $this->getSqlService()->query(
            'DELETE FROM ' . UbixDatabase::SYSTEMS->databaseName() . '.Schema_Migrations
             WHERE id IN (:applied, :destructive, :drift, :other, :pending)',
            [
                'applied'     => self::APPLIED_MIGRATION_ID,
                'destructive' => self::DESTRUCTIVE_MIGRATION_ID,
                'drift'       => self::DRIFT_MIGRATION_ID,
                'other'       => self::OTHER_DATABASE_MIGRATION_ID,
                'pending'     => self::PENDING_MIGRATION_ID,
            ],
        );
    }

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationStatusService::class);
    }

    /**
     * Produces one entry per on-disk file in filename order; an applied file
     * surfaces its recorded actor and apply timestamp while a pending file
     * carries null actor/timestamp and an unset applied flag.
     *
     * @return void
     *
     * @covers ::getStatus
     */
    public function testGetStatusJoinsAppliedAndPendingFiles(): void
    {
        $appliedBody = 'CREATE TABLE SYSTEMS.Ubix_Status_Applied_90150001 (id INT);';
        $this->writeMigrationFile(self::APPLIED_MIGRATION_ID, 'SYSTEMS', 'Applied table', $appliedBody);
        $this->writeMigrationFile(
            self::PENDING_MIGRATION_ID,
            'SYSTEMS',
            'Pending table',
            'CREATE TABLE SYSTEMS.Ubix_Status_Pending_90150002 (id INT);',
        );

        $this->recordApplied(self::APPLIED_MIGRATION_ID, 'SYSTEMS', 'Applied table', hash('sha256', $appliedBody));

        $entries = $this->service()->getStatus();

        $this->assertCount(2, $entries);

        $applied = $entries[0];
        $this->assertSame(self::APPLIED_MIGRATION_ID, $applied->id);
        $this->assertTrue($applied->isApplied);
        $this->assertTrue($applied->checksumMatches);
        $this->assertSame(self::APPLIED_BY, $applied->appliedBy);
        $this->assertNotNull($applied->appliedAt);

        $pending = $entries[1];
        $this->assertSame(self::PENDING_MIGRATION_ID, $pending->id);
        $this->assertFalse($pending->isApplied);
        $this->assertTrue($pending->checksumMatches);
        $this->assertNull($pending->appliedBy);
        $this->assertNull($pending->appliedAt);
    }

    /**
     * Reports drift when an applied file's on-disk checksum no longer matches
     * the value recorded in the tracker at apply time.
     *
     * @return void
     *
     * @covers ::getStatus
     */
    public function testGetStatusReportsChecksumDriftForEditedAppliedFile(): void
    {
        $this->writeMigrationFile(
            self::DRIFT_MIGRATION_ID,
            'SYSTEMS',
            'Drifted table',
            'CREATE TABLE SYSTEMS.Ubix_Status_Drift_90150004 (id INT, edited_after_apply INT);',
        );

        // Record a checksum that deliberately differs from the on-disk body.
        $this->recordApplied(self::DRIFT_MIGRATION_ID, 'SYSTEMS', 'Drifted table', hash('sha256', 'original body before edit'));

        $entries = $this->service()->getStatus();

        $this->assertCount(1, $entries);
        $this->assertSame(self::DRIFT_MIGRATION_ID, $entries[0]->id);
        $this->assertTrue($entries[0]->isApplied);
        $this->assertFalse($entries[0]->checksumMatches);
    }

    /**
     * Narrows the report to a single target database when a filter is given.
     *
     * @return void
     *
     * @covers ::getStatus
     */
    public function testGetStatusFiltersByTargetDatabase(): void
    {
        $this->writeMigrationFile(
            self::PENDING_MIGRATION_ID,
            'SYSTEMS',
            'Systems table',
            'CREATE TABLE SYSTEMS.Ubix_Status_Pending_90150002 (id INT);',
        );
        $this->writeMigrationFile(
            self::OTHER_DATABASE_MIGRATION_ID,
            'VSCASH',
            'Vscash table',
            'CREATE TABLE VSCASH.Ubix_Status_Other_90150005 (id INT);',
        );

        $entries = $this->service()->getStatus('VSCASH');

        $this->assertCount(1, $entries);
        $this->assertSame(self::OTHER_DATABASE_MIGRATION_ID, $entries[0]->id);
        $this->assertSame('VSCASH', $entries[0]->targetDatabase);
    }

    /**
     * Flags a file as destructive when it declares a `Destructive:` header.
     *
     * @return void
     *
     * @covers ::getStatus
     */
    public function testGetStatusFlagsDestructiveFile(): void
    {
        $this->writeMigrationFile(
            self::DESTRUCTIVE_MIGRATION_ID,
            'SYSTEMS',
            'Drops a table',
            'DROP TABLE SYSTEMS.Ubix_Status_Destructive_90150003;',
            'Removing a retired table per the cutover runbook.',
        );
        $this->writeMigrationFile(
            self::PENDING_MIGRATION_ID,
            'SYSTEMS',
            'Pending table',
            'CREATE TABLE SYSTEMS.Ubix_Status_Pending_90150002 (id INT);',
        );

        $entries = $this->service()->getStatus();

        // Filename ascending: `...000002_pending` sorts before `...000003_destructive`.
        $this->assertCount(2, $entries);
        $this->assertSame(self::PENDING_MIGRATION_ID, $entries[0]->id);
        $this->assertFalse($entries[0]->isDestructive);
        $this->assertSame(self::DESTRUCTIVE_MIGRATION_ID, $entries[1]->id);
        $this->assertTrue($entries[1]->isDestructive);
    }

    /**
     * Build the real SQL-backed reader/writer repository against the test DB.
     *
     * @return SchemaMigrationSqlRepository
     */
    private function repository(): SchemaMigrationSqlRepository
    {
        return new SchemaMigrationSqlRepository(
            $this->createStub(Logger::class),
            $this->getSqlService(),
        );
    }

    /**
     * Record an applied migration row via the real repository writer.
     *
     * @param string $id             Migration id (filename without `.sql`)
     * @param string $targetDatabase Target database name
     * @param string $description    Migration description
     * @param string $checksum       SHA-256 hex digest recorded at apply time
     *
     * @return void
     */
    private function recordApplied(string $id, string $targetDatabase, string $description, string $checksum): void
    {
        $this->repository()->insert(
            id:             $id,
            targetDatabase: $targetDatabase,
            description:    $description,
            checksum:       $checksum,
            appliedBy:      self::APPLIED_BY,
            durationMs:     42,
        );
    }

    /**
     * Build the service with real collaborators; only the boundary logger is
     * stubbed.
     *
     * @return MigrationStatusService
     */
    private function service(): MigrationStatusService
    {
        $logger  = $this->createStub(Logger::class);
        $scanner = new MigrationFileScannerService(
            $logger,
            new MigrationFileParserService($logger, new DestructiveStatementDetectorService($logger), new HotTableAlterDetectorService($logger)),
            $this->migrationsPath,
        );

        return new MigrationStatusService($logger, $scanner, $this->repository());
    }

    /**
     * Write a well-formed migration file into the temp directory so the real
     * scanner/parser produce a `MigrationFile` DTO for it.
     *
     * @param string  $id                Migration id (filename without `.sql`)
     * @param string  $targetDatabase    Value for the `Database:` header
     * @param string  $description       Value for the `Description:` header
     * @param string  $body              SQL body following the header
     * @param ?string $destructiveReason Value for the `Destructive:` header, when the body is destructive
     *
     * @return void
     */
    private function writeMigrationFile(
        string $id,
        string $targetDatabase,
        string $description,
        string $body,
        ?string $destructiveReason = null,
    ): void {
        $headerLines = [
            '-- Migration: ' . $id,
            '-- Database: ' . $targetDatabase,
            '-- Description: ' . $description,
            '-- Author: Ubix Test',
        ];
        if ($destructiveReason !== null) {
            $headerLines[] = '-- Destructive: ' . $destructiveReason;
        }

        $contents = implode("\n", $headerLines) . "\n\n" . $body . "\n";
        file_put_contents($this->migrationsPath . '/' . $id . '.sql', $contents);
    }
}
