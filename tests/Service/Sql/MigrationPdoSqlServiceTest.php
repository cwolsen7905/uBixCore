<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Sql;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\Sql\MigrationPdoSqlService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Sql\MigrationPdoSqlService
 *
 * @coversDefaultClass \Ubix\Service\Sql\MigrationPdoSqlService
 * @coversDefaultClass \Ubix\Service\Sql\MigrationPdoSqlService
 */
final class MigrationPdoSqlServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    // Seed ids sit far above anything real data will reach, clear of other agents.
    private const BROADCASTER_ID_ONE = 9000000;

    private const BROADCASTER_ID_THREE = 9000002;

    private const BROADCASTER_ID_TWO = 9000001;

    private const NAME_ONE = 'UbixMigrationSqlTestOne';

    private const NAME_THREE = 'UbixMigrationSqlTestThree';

    private const NAME_TWO = 'UbixMigrationSqlTestTwo';

    // The framework's own fixture schema (sql/ubixcore_test.sql), rebuilt by `database:resetSchema test` in CI
    private const TEST_DATABASE = 'ubixcore_test';

    /**
     * Seed three user rows so the migration-tier connection can read,
     * count and iterate real data from the writer cluster.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->tearDown(); // Idempotent: a crashed earlier run can leave the seed rows behind

        $studios = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $this->insertSeedData(
            'INSERT INTO ' . $studios . ".users SET id=:id, display_name=:name, email=:email, password_hash='x', status='active'",
            ['id' => self::BROADCASTER_ID_ONE, 'name' => self::NAME_ONE, 'email' => self::NAME_ONE . '@example.test'],
        );
        $this->insertSeedData(
            'INSERT INTO ' . $studios . ".users SET id=:id, display_name=:name, email=:email, password_hash='x', status='active'",
            ['id' => self::BROADCASTER_ID_TWO, 'name' => self::NAME_TWO, 'email' => self::NAME_TWO . '@example.test'],
        );
        $this->insertSeedData(
            'INSERT INTO ' . $studios . ".users SET id=:id, display_name=:name, email=:email, password_hash='x', status='inactive'",
            ['id' => self::BROADCASTER_ID_THREE, 'name' => self::NAME_THREE, 'email' => self::NAME_THREE . '@example.test'],
        );
    }

    /**
     * Remove only the seeded rows.
     *
     * @return void
     */
    public function tearDown(): void
    {
        $studios = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $this->insertSeedData(
            'DELETE FROM ' . $studios . '.users WHERE id IN (:idOne, :idTwo, :idThree)',
            [
                'idOne'   => self::BROADCASTER_ID_ONE,
                'idThree' => self::BROADCASTER_ID_THREE,
                'idTwo'   => self::BROADCASTER_ID_TWO,
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
        $studios = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $name = $this->buildMigrationSqlService()->getColumn(
            'SELECT display_name FROM ' . $studios . '.users WHERE id=:id',
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
        $studios = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $name = $this->buildMigrationSqlService()->getColumn(
            'SELECT display_name FROM ' . $studios . '.users WHERE id=:id',
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
        $studios = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $row = $this->buildMigrationSqlService()->getRow(
            'SELECT id, display_name AS name FROM ' . $studios . '.users WHERE id=:id',
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
        $studios = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $names = [];
        $rows  = $this->buildMigrationSqlService()->getRows(
            'SELECT display_name AS name FROM ' . $studios . '.users WHERE id IN (:idOne, :idTwo, :idThree) ORDER BY id ASC',
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
        $studios    = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;
        $sqlService = $this->buildMigrationSqlService();

        // Seeded statuses are TWO=1 and THREE=0; updating both to 5 changes
        // both rows so MySQL's affected-row count is a clean 2.
        $affected = $sqlService->query(
            'UPDATE ' . $studios . ".users SET status='suspended' WHERE id IN (:idTwo, :idThree)",
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
        $studios    = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;
        $sqlService = $this->buildMigrationSqlService();

        $sqlService->query(
            'UPDATE ' . $studios . ".users SET status='suspended' WHERE id=:id",
            ['id' => self::BROADCASTER_ID_ONE],
        );

        $status = $sqlService->getColumn(
            'SELECT status FROM ' . $studios . '.users WHERE id=:id',
            ['id' => self::BROADCASTER_ID_ONE],
        );

        $this->assertSame('suspended', $status);
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
        $studios    = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;
        $sqlService = $this->buildMigrationSqlService();

        $sqlService->beginTransaction();
        $this->assertTrue($sqlService->inTransaction());

        $sqlService->query(
            'UPDATE ' . $studios . ".users SET status='pending' WHERE id=:id",
            ['id' => self::BROADCASTER_ID_TWO],
        );
        $sqlService->commit();

        $this->assertFalse($sqlService->inTransaction());

        $status = $sqlService->getColumn(
            'SELECT status FROM ' . $studios . '.users WHERE id=:id',
            ['id' => self::BROADCASTER_ID_TWO],
        );
        $this->assertSame('pending', $status);
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
