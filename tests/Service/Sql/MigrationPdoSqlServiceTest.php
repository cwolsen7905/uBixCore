<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Sql;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\UbixDatabase;
use Ubix\Service\Sql\MigrationPdoSqlService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Sql\MigrationPdoSqlService
 *
 * @coversDefaultClass \Ubix\Service\Sql\MigrationPdoSqlService
 * @coversDefaultClass \Ubis\Service\Sql\MigrationPdoSqlService
 */
final class MigrationPdoSqlServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    // The Broadcasters.id column is mediumint(5) unsigned (max 16,777,215), so
    // the 9000000 seed base fits comfortably while staying clear of other agents.
    private const BROADCASTER_ID_ONE = 9000000;

    private const BROADCASTER_ID_THREE = 9000002;

    private const BROADCASTER_ID_TWO = 9000001;

    private const NAME_ONE = 'UbixMigrationSqlTestOne';

    private const NAME_THREE = 'UbixMigrationSqlTestThree';

    private const NAME_TWO = 'UbixMigrationSqlTestTwo';

    /**
     * Seed three broadcaster rows so the migration-tier connection can read,
     * count and iterate real data from the writer cluster.
     *
     * @return void
     */
    public function setUp(): void
    {
        $studios = UbixDatabase::STUDIOS->databaseName();

        $this->insertSeedData(
            'INSERT INTO ' . $studios . '.Broadcasters SET id=:id, name=:name, prospect_id=0, status=1',
            ['id' => self::BROADCASTER_ID_ONE, 'name' => self::NAME_ONE],
        );
        $this->insertSeedData(
            'INSERT INTO ' . $studios . '.Broadcasters SET id=:id, name=:name, prospect_id=0, status=1',
            ['id' => self::BROADCASTER_ID_TWO, 'name' => self::NAME_TWO],
        );
        $this->insertSeedData(
            'INSERT INTO ' . $studios . '.Broadcasters SET id=:id, name=:name, prospect_id=0, status=0',
            ['id' => self::BROADCASTER_ID_THREE, 'name' => self::NAME_THREE],
        );
    }

    /**
     * Remove only the seeded rows.
     *
     * @return void
     */
    public function tearDown(): void
    {
        $studios = UbixDatabase::STUDIOS->databaseName();

        $this->insertSeedData(
            'DELETE FROM ' . $studios . '.Broadcasters WHERE id IN (:idOne, :idTwo, :idThree)',
            [
                'idOne'   => self::BROADCASTER_ID_ONE,
                'idThree' => self::BROADCASTER_ID_THREE,
                'idTwo'   => self::BROADCASTER_ID_TWO,
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
        $this->testClassFollowingUbixStandards(MigrationPdoSqlService::class);
    }

    /**
     * Reads a single scalar column through the lazily-initialised migration
     * connection, proving ensureInitialized() resolves a usable connection.
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testGetColumnReadsThroughLazyInitialisedConnection(): void
    {
        $studios = UbixDatabase::STUDIOS->databaseName();

        $name = $this->buildMigrationSqlService()->getColumn(
            'SELECT name FROM ' . $studios . '.Broadcasters WHERE id=:id',
            ['id' => self::BROADCASTER_ID_ONE],
        );

        $this->assertSame(self::NAME_ONE, $name);
    }

    /**
     * Returns false from getColumn when the query matches no rows, confirming
     * the migration connection routes reads at the writer cluster.
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testGetColumnReturnsFalseWhenNoMatch(): void
    {
        $studios = UbixDatabase::STUDIOS->databaseName();

        $name = $this->buildMigrationSqlService()->getColumn(
            'SELECT name FROM ' . $studios . '.Broadcasters WHERE id=:id',
            ['id' => self::BROADCASTER_ID_THREE + 1000],
        );

        $this->assertFalse($name);
    }

    /**
     * Fetches a single associative row for the matching id.
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testGetRowReturnsAssociativeRow(): void
    {
        $studios = UbixDatabase::STUDIOS->databaseName();

        $row = $this->buildMigrationSqlService()->getRow(
            'SELECT id, name FROM ' . $studios . '.Broadcasters WHERE id=:id',
            ['id' => self::BROADCASTER_ID_TWO],
        );

        $this->assertIsArray($row);
        $this->assertSame(self::NAME_TWO, $row['name']);
        $this->assertSame(self::BROADCASTER_ID_TWO, (int) $row['id']);
    }

    /**
     * Yields each matching row in turn from the generator.
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testGetRowsYieldsEachMatchingRow(): void
    {
        $studios = UbixDatabase::STUDIOS->databaseName();

        $names = [];
        $rows  = $this->buildMigrationSqlService()->getRows(
            'SELECT name FROM ' . $studios . '.Broadcasters WHERE id IN (:idOne, :idTwo, :idThree) ORDER BY id ASC',
            [
                'idOne'   => self::BROADCASTER_ID_ONE,
                'idThree' => self::BROADCASTER_ID_THREE,
                'idTwo'   => self::BROADCASTER_ID_TWO,
            ],
        );
        foreach ($rows as $row) {
            $names[] = $row['name'];
        }

        $this->assertSame([self::NAME_ONE, self::NAME_TWO, self::NAME_THREE], $names);
    }

    /**
     * Executes an UPDATE through the writer pool and returns the affected-row
     * count, confirming write and read paths share the master connection.
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testQueryReturnsAffectedRowCount(): void
    {
        $studios    = UbixDatabase::STUDIOS->databaseName();
        $sqlService = $this->buildMigrationSqlService();

        // Seeded statuses are TWO=1 and THREE=0; updating both to 5 changes
        // both rows so MySQL's affected-row count is a clean 2.
        $affected = $sqlService->query(
            'UPDATE ' . $studios . '.Broadcasters SET status=5 WHERE id IN (:idTwo, :idThree)',
            ['idThree' => self::BROADCASTER_ID_THREE, 'idTwo' => self::BROADCASTER_ID_TWO],
        );

        $this->assertSame(2, $affected);
    }

    /**
     * Reads a migration write back immediately within the same connection,
     * proving the collapsed single-pool design tolerates no replica lag.
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testWriteIsImmediatelyReadableWithoutReplicaLag(): void
    {
        $studios    = UbixDatabase::STUDIOS->databaseName();
        $sqlService = $this->buildMigrationSqlService();

        $sqlService->query(
            'UPDATE ' . $studios . '.Broadcasters SET status=8 WHERE id=:id',
            ['id' => self::BROADCASTER_ID_ONE],
        );

        $status = $sqlService->getColumn(
            'SELECT status FROM ' . $studios . '.Broadcasters WHERE id=:id',
            ['id' => self::BROADCASTER_ID_ONE],
        );

        $this->assertSame(8, (int) $status);
    }

    /**
     * Commits a write performed inside a transaction so the change persists.
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testCommitPersistsTransactionalWrite(): void
    {
        $studios    = UbixDatabase::STUDIOS->databaseName();
        $sqlService = $this->buildMigrationSqlService();

        $sqlService->beginTransaction();
        $this->assertTrue($sqlService->inTransaction());

        $sqlService->query(
            'UPDATE ' . $studios . '.Broadcasters SET status=7 WHERE id=:id',
            ['id' => self::BROADCASTER_ID_TWO],
        );
        $sqlService->commit();

        $this->assertFalse($sqlService->inTransaction());

        $status = $sqlService->getColumn(
            'SELECT status FROM ' . $studios . '.Broadcasters WHERE id=:id',
            ['id' => self::BROADCASTER_ID_TWO],
        );
        $this->assertSame(7, (int) $status);
    }

    /**
     * Build a migration-tier service whose lazy ensureInitialized() resolves
     * the TEST_MYSQL_WRITE_* credentials under PHPUnit.
     *
     * @return MigrationPdoSqlService
     */
    private function buildMigrationSqlService(): MigrationPdoSqlService
    {
        return new MigrationPdoSqlService($this->createStub(Logger::class));
    }
}
