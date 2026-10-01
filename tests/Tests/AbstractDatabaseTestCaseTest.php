<?php

declare(strict_types=1);

namespace Ubix\Tests\Tests;

use PHPUnit\Framework\Attributes\Depends;
use Ubix\Tests\AbstractDatabaseTestCase as DatabaseTestCase;

/**
 * PHPUnit test case for \Ubix\Tests\AbstractDatabaseTestCase
 *
 * Proves the one promise the base makes that a reader cannot check by eye: a
 * row written by one test is not visible to the next.
 *
 * Two details here are load-bearing, and the first version of this file got
 * both wrong — it passed just as happily with the rollback swapped for a
 * commit, which is the definition of a test that proves nothing.
 *
 * **The order is pinned with `#[Depends]`.** `phpunit.xml` sets
 * `executionOrder="depends,defects"`, so without it the reader can run before
 * the writer and find an empty table for the wrong reason.
 *
 * **`setUp()` must not clear the table.** Clearing it destroys the very
 * evidence the second test exists to look for. The table is TEMPORARY, so it
 * belongs to this connection and starts empty anyway; rows written into it are
 * transactional in InnoDB and go back with the rollback, while the table itself
 * survives, which is exactly the arrangement this needs.
 *
 * Skips itself when there is no reachable test database, which is the base's
 * other promise and is exercised simply by this file running at all on a
 * machine without credentials.
 */
final class AbstractDatabaseTestCaseTest extends DatabaseTestCase
{
    /**
     * The scratch table, created inside the transaction and rolled back with it
     */
    private const string TABLE = 'ubix_database_test_case_probe';

    /**
     * Write a row, and prove it is there while the test still owns it
     *
     * @return void
     */
    public function testARowIsVisibleInsideTheTestThatWroteIt(): void
    {
        $this->getSqlService()->query('INSERT INTO ' . self::TABLE . ' (note) VALUES (:note)', ['note' => 'written']);

        $this->assertSame(1, (int) $this->getSqlService()->getColumn('SELECT COUNT(*) FROM ' . self::TABLE));
    }

    /**
     * The previous test's row is gone, because its transaction was rolled back
     *
     * If this ever fails with 1, the rollback is not happening and every
     * database-backed test in the suite has started leaking into its neighbours.
     *
     * @return void
     */
    #[Depends('testARowIsVisibleInsideTheTestThatWroteIt')]
    public function testARowIsNotVisibleToTheNextTest(): void
    {
        $this->assertSame(0, (int) $this->getSqlService()->getColumn('SELECT COUNT(*) FROM ' . self::TABLE));
    }

    /**
     * Create the scratch table inside the transaction
     *
     * A TEMPORARY table, so it belongs to the connection rather than the schema
     * and cannot survive to collide with anything — a normal CREATE TABLE
     * commits implicitly in MySQL, which would defeat the very rollback under
     * test. It is deliberately not emptied here: see the class docblock.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->getSqlService()->query(
            'CREATE TEMPORARY TABLE IF NOT EXISTS ' . self::TABLE . ' (note VARCHAR(32) NOT NULL)',
        );
    }
}
