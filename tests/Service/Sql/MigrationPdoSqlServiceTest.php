<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Sql;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\Sql\MigrationPdoSqlService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Sql\MigrationPdoSqlService
 *
 * @coversDefaultClass \Ubix\Service\Sql\MigrationPdoSqlService
 * @coversDefaultClass \Ubix\Service\Sql\MigrationPdoSqlService
 * @see                \Ubix\Tests\Tests\Service\Sql\MigrationPdoSqlServiceTestTest PHPUnit test case
 */
final class MigrationPdoSqlServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    // Seed ids sit far above anything real data will reach, clear of other agents.
    private const USER_ID_ONE = 9000000;

    private const USER_ID_THREE = 9000002;

    private const USER_ID_TWO = 9000001;

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

        $schema = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $this->insertSeedData(
            'INSERT INTO ' . $schema . ".users SET id=:id, display_name=:name, email=:email, password_hash='x', status='active'",
            ['id' => self::USER_ID_ONE, 'name' => self::NAME_ONE, 'email' => self::NAME_ONE . '@example.test'],
        );
        $this->insertSeedData(
            'INSERT INTO ' . $schema . ".users SET id=:id, display_name=:name, email=:email, password_hash='x', status='active'",
            ['id' => self::USER_ID_TWO, 'name' => self::NAME_TWO, 'email' => self::NAME_TWO . '@example.test'],
        );
        $this->insertSeedData(
            'INSERT INTO ' . $schema . ".users SET id=:id, display_name=:name, email=:email, password_hash='x', status='inactive'",
            ['id' => self::USER_ID_THREE, 'name' => self::NAME_THREE, 'email' => self::NAME_THREE . '@example.test'],
        );
    }

    /**
     * Remove only the seeded rows.
     *
     * @return void
     */
    public function tearDown(): void
    {
        $schema = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $this->insertSeedData(
            'DELETE FROM ' . $schema . '.users WHERE id IN (:idOne, :idTwo, :idThree)',
            [
                'idOne'   => self::USER_ID_ONE,
                'idThree' => self::USER_ID_THREE,
                'idTwo'   => self::USER_ID_TWO,
            ],
        );
    }

    /**
     * The connection speaks utf8mb4 unless MYSQL_CHARSET says otherwise
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testTheConnectionSpeaksUtf8mb4ByDefault(): void
    {
        putenv('MYSQL_CHARSET');

        $this->assertSame('utf8mb4', $this->buildMigrationSqlService()->getColumn('SELECT @@character_set_client'));
    }

    /**
     * Non-ASCII text is stored as the characters written, not double-encoded
     *
     * Under the old hardcoded latin1 connection an em dash was stored as the
     * three characters `â€”`; it read back intact through the same connection,
     * which is why nobody saw it.
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testNonAsciiIsStoredAsTheCharactersWritten(): void
    {
        putenv('MYSQL_CHARSET');
        $schema     = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;
        $sqlService = $this->buildMigrationSqlService();
        $text       = 'Café — Grace 🙏';

        $sqlService->query('UPDATE ' . $schema . '.users SET display_name=:name WHERE id=:id', ['id' => self::USER_ID_ONE, 'name' => $text]);
        $row = $sqlService->getRow('SELECT display_name, CHAR_LENGTH(display_name) chars, HEX(display_name) bytes FROM ' . $schema . '.users WHERE id=:id', ['id' => self::USER_ID_ONE]);

        $this->assertIsArray($row);
        $this->assertSame($text, $row['display_name']);
        $this->assertSame(mb_strlen($text), (int) $row['chars']);
        $this->assertSame(strtoupper(bin2hex($text)), $row['bytes']);
    }

    /**
     * MYSQL_CHARSET still selects another charset, for a host that needs one
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testMysqlCharsetOverridesTheDefault(): void
    {
        putenv('MYSQL_CHARSET=latin1');

        try {
            $this->assertSame('latin1', $this->buildMigrationSqlService()->getColumn('SELECT @@character_set_client'));
        } finally {
            putenv('MYSQL_CHARSET');
        }
    }

    /**
     * A MYSQL_CHARSET that is not a bare name never reaches the DSN
     *
     * @return void
     *
     * @covers ::ensureInitialized
     */
    public function testAMalformedCharsetIsRefused(): void
    {
        putenv('MYSQL_CHARSET=utf8mb4;host=elsewhere');

        try {
            $this->expectException(InvalidArgumentException::class);
            $this->buildMigrationSqlService()->getColumn('SELECT 1');
        } finally {
            putenv('MYSQL_CHARSET');
        }
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
        $schema = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $name = $this->buildMigrationSqlService()->getColumn(
            'SELECT display_name FROM ' . $schema . '.users WHERE id=:id',
            ['id' => self::USER_ID_ONE],
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
        $schema = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $name = $this->buildMigrationSqlService()->getColumn(
            'SELECT display_name FROM ' . $schema . '.users WHERE id=:id',
            ['id' => self::USER_ID_THREE + 1000],
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
        $schema = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $row = $this->buildMigrationSqlService()->getRow(
            'SELECT id, display_name AS name FROM ' . $schema . '.users WHERE id=:id',
            ['id' => self::USER_ID_TWO],
        );

        $this->assertIsArray($row);
        $this->assertSame(self::NAME_TWO, $row['name']);
        $this->assertSame(self::USER_ID_TWO, (int) $row['id']);
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
        $schema = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;

        $names = [];
        $rows  = $this->buildMigrationSqlService()->getRows(
            'SELECT display_name AS name FROM ' . $schema . '.users WHERE id IN (:idOne, :idTwo, :idThree) ORDER BY id ASC',
            [
                'idOne'   => self::USER_ID_ONE,
                'idThree' => self::USER_ID_THREE,
                'idTwo'   => self::USER_ID_TWO,
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
        $schema     = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;
        $sqlService = $this->buildMigrationSqlService();

        // Seeded statuses are TWO=1 and THREE=0; updating both to 5 changes
        // both rows so MySQL's affected-row count is a clean 2.
        $affected = $sqlService->query(
            'UPDATE ' . $schema . ".users SET status='suspended' WHERE id IN (:idTwo, :idThree)",
            ['idThree' => self::USER_ID_THREE, 'idTwo' => self::USER_ID_TWO],
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
        $schema     = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;
        $sqlService = $this->buildMigrationSqlService();

        $sqlService->query(
            'UPDATE ' . $schema . ".users SET status='suspended' WHERE id=:id",
            ['id' => self::USER_ID_ONE],
        );

        $status = $sqlService->getColumn(
            'SELECT status FROM ' . $schema . '.users WHERE id=:id',
            ['id' => self::USER_ID_ONE],
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
        $schema     = (string) getenv('DATABASE_PREFIX') . self::TEST_DATABASE;
        $sqlService = $this->buildMigrationSqlService();

        $sqlService->beginTransaction();
        $this->assertTrue($sqlService->inTransaction());

        $sqlService->query(
            'UPDATE ' . $schema . ".users SET status='pending' WHERE id=:id",
            ['id' => self::USER_ID_TWO],
        );
        $sqlService->commit();

        $this->assertFalse($sqlService->inTransaction());

        $status = $sqlService->getColumn(
            'SELECT status FROM ' . $schema . '.users WHERE id=:id',
            ['id' => self::USER_ID_TWO],
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
