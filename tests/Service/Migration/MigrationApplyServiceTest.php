<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\Enum\UbixDatabase;
use Ubix\Repository\SchemaMigration\SchemaMigrationSqlRepository;
use Ubix\Service\Migration\MigrationApplyService;
use Ubix\Service\Migration\MigrationCredentialResolverService;
use Ubix\Service\ProcessService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Migration\MigrationApplyService
 *
 * @coversDefaultClass \Ubix\Service\Migration\MigrationApplyService
 * @coversDefaultClass \Ubis\Service\Migration\MigrationApplyService
 */
final class MigrationApplyServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const APPLIED_BY = 'cli:ubix-test';

    private const APPLY_MIGRATION_ID = '90120001_ubix_apply_service_apply';

    private const APPLY_TABLE = 'Ubix_Apply_Service_Apply_90120001';

    private const BACKTICK_MIGRATION_ID = '90120004_ubix_apply_service_backtick';

    private const BACKTICK_TABLE = 'Ubix_Apply_Service_Backtick_90120004';

    private const CLASH_MIGRATION_ID = '90120002_ubix_apply_service_clash';

    private const CLASH_TABLE = 'Ubix_Apply_Service_Clash_90120002';

    private const RECONCILE_MIGRATION_ID = '90120003_ubix_apply_service_reconcile';

    /**
     * Drop any tables/rows this test created so re-runs stay deterministic.
     * Targeted DELETEs / DROPs only — never a truncate.
     *
     * @return void
     */
    public function tearDown(): void
    {
        $systems = UbixDatabase::SYSTEMS->databaseName();

        $this->getSqlService()->query(
            'DROP TABLE IF EXISTS ' . $systems . '.' . self::APPLY_TABLE,
            [],
        );
        $this->getSqlService()->query(
            'DROP TABLE IF EXISTS ' . $systems . '.' . self::CLASH_TABLE,
            [],
        );
        $this->getSqlService()->query(
            'DROP TABLE IF EXISTS ' . $systems . '.' . self::BACKTICK_TABLE,
            [],
        );
        $this->getSqlService()->query(
            'DELETE FROM ' . $systems . '.Schema_Migrations WHERE id IN (:apply, :backtick, :clash, :reconcile)',
            [
                'apply'     => self::APPLY_MIGRATION_ID,
                'backtick'  => self::BACKTICK_MIGRATION_ID,
                'clash'     => self::CLASH_MIGRATION_ID,
                'reconcile' => self::RECONCILE_MIGRATION_ID,
            ],
        );
    }

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationApplyService::class);
    }

    /**
     * `apply()` runs the body through the mariadb CLI and records the tracker
     * row: the target table is created and a `Schema_Migrations` row exists
     * for the migration id with the supplied actor.
     *
     * @return void
     *
     * @covers ::apply
     */
    public function testApplyRunsBodyAndRecordsTrackerRow(): void
    {
        $systems = UbixDatabase::SYSTEMS->databaseName();
        $file    = $this->migrationFile(
            id:   self::APPLY_MIGRATION_ID,
            body: 'CREATE TABLE SYSTEMS.' . self::APPLY_TABLE . ' (id INT UNSIGNED NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB;',
        );

        $applied = $this->service()->apply($file, self::APPLIED_BY);

        $this->assertSame(self::APPLY_MIGRATION_ID, $applied->id);
        $this->assertSame($systems, $applied->targetDatabase);
        $this->assertSame(self::APPLIED_BY, $applied->appliedBy);

        $tableExists = $this->reader()->tableExists($systems, self::APPLY_TABLE);
        $this->assertTrue($tableExists);

        $recorded = $this->reader()->getById(self::APPLY_MIGRATION_ID);
        $this->assertNotNull($recorded);
        $this->assertSame(self::APPLIED_BY, $recorded->appliedBy);
    }

    /**
     * A body whose schema qualifier is backtick-quoted
     * (``CREATE TABLE `SYSTEMS`.`Foo` ``) must be repointed at the
     * `DATABASE_PREFIX`-prefixed schema exactly like the unquoted
     * form: the table lands in the prefixed database, never in the
     * runtime one. Regression guard for the 2026-08-17 dev-pipeline
     * failure, where a backtick-quoted `ALTER TABLE` slipped past
     * the plain-string rewrite and died with `ERROR 1146` against
     * the unprefixed schema.
     *
     * @return void
     *
     * @covers ::apply
     */
    public function testApplyRepointsBacktickQuotedSchemaAtThePrefixedDatabase(): void
    {
        // The guard is only meaningful while a prefix is in force —
        // without one there is nothing to rewrite.
        $prefix = (string) getenv('DATABASE_PREFIX');
        if ($prefix === '') {
            $this->markTestSkipped('Prefixed test schemas (DATABASE_PREFIX) are not wired in ubixcore yet — see docs/projects/ci-parity CP-07');
        }

        $systems = UbixDatabase::SYSTEMS->databaseName();
        $file    = $this->migrationFile(
            id:   self::BACKTICK_MIGRATION_ID,
            body: 'CREATE TABLE `SYSTEMS`.`' . self::BACKTICK_TABLE . '` (id INT UNSIGNED NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB;',
        );

        $applied = $this->service()->apply($file, self::APPLIED_BY);

        $this->assertSame($systems, $applied->targetDatabase);

        $tableExists = $this->reader()->tableExists($systems, self::BACKTICK_TABLE);
        $this->assertTrue($tableExists);
    }

    /**
     * `apply()` aborts before touching the body when a non-`IF NOT EXISTS`
     * `CREATE TABLE` target already exists, with the §8 reconcile hint —
     * so operators do not chase a raw "Table already exists" CLI error.
     *
     * @return void
     *
     * @covers ::apply
     */
    public function testApplyAbortsWhenTargetTableAlreadyExists(): void
    {
        $systems = UbixDatabase::SYSTEMS->databaseName();
        $this->getSqlService()->query(
            'CREATE TABLE ' . $systems . '.' . self::CLASH_TABLE . ' (id INT UNSIGNED NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB',
            [],
        );

        $file = $this->migrationFile(
            id:   self::CLASH_MIGRATION_ID,
            body: 'CREATE TABLE SYSTEMS.' . self::CLASH_TABLE . ' (id INT UNSIGNED NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB;',
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('migrate:reconcile');
        $this->service()->apply($file, self::APPLIED_BY);
    }

    /**
     * `applyBootstrap()` rejects any migration whose id is not the reserved
     * bootstrap id, since that path skips the pre-flight clash check.
     *
     * @return void
     *
     * @covers ::applyBootstrap
     */
    public function testApplyBootstrapRejectsNonBootstrapMigration(): void
    {
        $file = $this->migrationFile(
            id:   self::APPLY_MIGRATION_ID,
            body: 'SELECT 1;',
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('non-bootstrap migration');
        $this->service()->applyBootstrap($file, self::APPLIED_BY);
    }

    /**
     * `reconcile()` records a tracker row WITHOUT running the body: the row
     * carries the `manual:` actor prefix, the reason appended to the
     * description, and a zero duration.
     *
     * @return void
     *
     * @covers ::reconcile
     */
    public function testReconcileRecordsManualTrackerRowWithoutRunningBody(): void
    {
        $file = $this->migrationFile(
            id:   self::RECONCILE_MIGRATION_ID,
            body: 'CREATE TABLE SYSTEMS.Should_Never_Be_Created_90120003 (id INT);',
        );

        $applied = $this->service()->reconcile($file, 'opsperson', 'Applied by hand during incident');

        $this->assertSame('manual:opsperson', $applied->appliedBy);
        $this->assertSame('Seed description (Reconciled: Applied by hand during incident)', $applied->description);
        $this->assertSame(0, $applied->durationMs);

        $recorded = $this->reader()->getById(self::RECONCILE_MIGRATION_ID);
        $this->assertNotNull($recorded);
        $this->assertSame('manual:opsperson', $recorded->appliedBy);
        $this->assertSame('Seed description (Reconciled: Applied by hand during incident)', $recorded->description);
        $this->assertSame(0, $recorded->durationMs);

        // The body must NOT have run: no table should have been created.
        $bodyTableExists = $this->reader()->tableExists(
            UbixDatabase::SYSTEMS->databaseName(),
            'Should_Never_Be_Created_90120003',
        );
        $this->assertFalse($bodyTableExists);
    }

    /**
     * Build a `MigrationFile` targeting `SYSTEMS` with a stable description
     * and checksum so assertions can pin the recorded values.
     *
     * @param string $id   Migration id (filename without `.sql`)
     * @param string $body SQL body to apply
     *
     * @return MigrationFile
     */
    private function migrationFile(string $id, string $body): MigrationFile
    {
        return new MigrationFile(
            id:             $id,
            targetDatabase: 'SYSTEMS',
            description:    'Seed description',
            author:         'Ubix Test',
            body:           $body,
            checksum:       hash('sha256', $body),
            filePath:       '/tmp/' . $id . '.sql',
        );
    }

    /**
     * Build the real SQL-backed reader/writer repository against the test DB.
     *
     * @return SchemaMigrationSqlRepository
     */
    private function reader(): SchemaMigrationSqlRepository
    {
        return new SchemaMigrationSqlRepository(
            $this->createStub(Logger::class),
            $this->getSqlService(),
        );
    }

    /**
     * Build the service with real collaborators; only the boundary logger
     * is stubbed.
     *
     * @return MigrationApplyService
     */
    private function service(): MigrationApplyService
    {
        $logger     = $this->createStub(Logger::class);
        $repository = new SchemaMigrationSqlRepository($logger, $this->getSqlService());

        return new MigrationApplyService(
            logger:             $logger,
            reader:             $repository,
            writer:             $repository,
            processService:     new ProcessService($logger),
            credentialResolver: new MigrationCredentialResolverService($logger),
        );
    }
}
