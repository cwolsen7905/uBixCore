<?php

declare(strict_types=1);

namespace Ubix\Tests;

use Throwable;
use Ubix\Tests\AbstractTestCase as TestCase;

/**
 * Abstract class for a PHPUnit test case that runs against the test database
 *
 * {@see AbstractTestCase} already points the SQL service at the **test**
 * database and resolves its credentials from uBix Vault. What it does not do is
 * make a test that writes rows safe to run, and that is the whole of what this
 * adds:
 *
 * - **Every test runs inside a transaction that is always rolled back.** The
 *   test database is shared between a suite, a CI job and whoever is running
 *   phpunit on a laptop. A test that leaves rows behind does not fail; it makes
 *   the *next* test fail, somewhere else, intermittently, which is the most
 *   expensive kind of failure to track down.
 * - **A database it cannot reach skips the test rather than failing it.** A
 *   developer without the credentials should still be able to run the suite and
 *   trust the result. A hard failure there teaches people to ignore red.
 *
 * ## When a test belongs here rather than on a mock
 *
 * Most tests should not extend this. A mocked `SqlService` is faster, needs no
 * infrastructure, and is the right tool for logic that happens to read a row.
 *
 * This is for the cases where **the database itself is the thing under test**:
 * a query whose correctness lives in its SQL rather than in the PHP around it,
 * a generated column, a constraint, an index-dependent ordering, a predicate
 * whose operands are all the same type so that getting the wrong one still
 * compiles and still passes a string assertion. Asserting the *text* of such a
 * query proves it was written as intended; only rows prove it was intended
 * correctly.
 *
 * ## Writing one
 *
 * Seed in `setUp()` **after** calling `parent::setUp()`, so the fixture is
 * inside the transaction and goes away with it. Use `insertSeedData()` from the
 * parent, or the SQL service directly. Do not commit: a test that commits has
 * opted out of the only thing protecting the next one.
 *
 * Build the fixture to distinguish the mistake you are afraid of. A fixture
 * where the right answer and the wrong answer coincide is a fixture that will
 * pass either way.
 */
abstract class AbstractDatabaseTestCase extends TestCase
{
    /**
     * Whether this case opened the transaction it must roll back
     */
    private bool $inOwnTransaction = false;

    /**
     * Connect, or skip, and open the transaction the test runs inside
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        try {
            $sqlService = $this->getSqlService();
            $sqlService->getColumn('SELECT 1');
        } catch (Throwable $e) {
            // Skipped, never failed. The suite has to stay runnable without the
            // credentials, or people learn to ignore a red bar that is only ever
            // telling them they are not on the VPN.
            $this->markTestSkipped('Test database unavailable, skipping: ' . $e->getMessage());
        }

        if (!$sqlService->inTransaction()) {
            $sqlService->beginTransaction();
            $this->inOwnTransaction = true;
        }
    }

    /**
     * Throw the fixture away
     *
     * Runs whether the test passed, failed or threw. A rollback that only
     * happens on success leaves the database dirtiest exactly when something has
     * already gone wrong.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        if ($this->inOwnTransaction) {
            $sqlService = $this->getSqlService();

            if ($sqlService->inTransaction()) {
                $sqlService->rollBack();
            }

            $this->inOwnTransaction = false;
        }

        parent::tearDown();
    }
}
