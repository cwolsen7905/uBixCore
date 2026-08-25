<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\SchemaMigration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\PdoError;
use Ubix\Exception\DtoException;
use Ubix\Repository\SchemaMigration\SchemaMigrationSqlRepository;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\Repository\SchemaMigration\SchemaMigrationSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\SchemaMigration\SchemaMigrationSqlRepository
 * @coversDefaultClass \Ubis\Repository\SchemaMigration\SchemaMigrationSqlRepository
 */
final class SchemaMigrationSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SchemaMigrationSqlRepository::class);
    }

    /**
     * `trackerTableExists()` MUST swallow the specific MySQL "table doesn't
     * exist" (1146) error so the runner can detect a fresh cluster and
     * bootstrap the tracker.
     *
     * @return void
     * @covers ::trackerTableExists
     */
    public function testTrackerTableExistsReturnsFalseOnMysqlErrno1146(): void
    {
        $repository = new SchemaMigrationSqlRepository(
            logger:     $this->createStub(Logger::class),
            sqlService: $this->makeSqlServiceThrowing($this->makeDtoException(1146)),
        );

        $this->assertFalse($repository->trackerTableExists());
    }

    /**
     * `trackerTableExists()` MUST swallow the specific MySQL "unknown
     * database" (1049) error — same reason: fresh cluster where SYSTEMS
     * doesn't exist yet, the bootstrap step will create it.
     *
     * @return void
     * @covers ::trackerTableExists
     */
    public function testTrackerTableExistsReturnsFalseOnMysqlErrno1049(): void
    {
        $repository = new SchemaMigrationSqlRepository(
            logger:     $this->createStub(Logger::class),
            sqlService: $this->makeSqlServiceThrowing($this->makeDtoException(1049)),
        );

        $this->assertFalse($repository->trackerTableExists());
    }

    /**
     * Regression for the silent-auth-failure bug: when the migration user
     * credentials are wrong, PDO throws SQLSTATE 28000 / errno 1045
     * ("Access denied"). The repository MUST propagate that exception so
     * the operator sees the auth error immediately — otherwise
     * `migrate:status` reports every migration as pending, which looks
     * like a fresh cluster and hides the real cause.
     *
     * @return void
     * @covers ::trackerTableExists
     */
    public function testTrackerTableExistsPropagatesAuthFailure1045(): void
    {
        $repository = new SchemaMigrationSqlRepository(
            logger:     $this->createStub(Logger::class),
            sqlService: $this->makeSqlServiceThrowing($this->makeDtoException(1045, '28000', 'Access denied for user')),
        );

        $this->expectException(DtoException::class);
        $repository->trackerTableExists();
    }

    /**
     * Connection-refused (errno 2002) must also propagate — the operator
     * needs to see "couldn't reach the host" rather than "every migration
     * pending".
     *
     * @return void
     * @covers ::trackerTableExists
     */
    public function testTrackerTableExistsPropagatesConnectionRefused2002(): void
    {
        $repository = new SchemaMigrationSqlRepository(
            logger:     $this->createStub(Logger::class),
            sqlService: $this->makeSqlServiceThrowing($this->makeDtoException(2002, 'HY000', "Can't connect to MySQL server")),
        );

        $this->expectException(DtoException::class);
        $repository->trackerTableExists();
    }

    /**
     * Table-level permission denied (errno 1142) — the migration user has
     * a valid login but lacks SELECT on `SYSTEMS.Schema_Migrations`. This
     * is a real misconfiguration the operator needs to fix; propagate.
     *
     * @return void
     * @covers ::trackerTableExists
     */
    public function testTrackerTableExistsPropagatesTablePermissionDenied1142(): void
    {
        $repository = new SchemaMigrationSqlRepository(
            logger:     $this->createStub(Logger::class),
            sqlService: $this->makeSqlServiceThrowing($this->makeDtoException(1142, '42000', 'SELECT command denied')),
        );

        $this->expectException(DtoException::class);
        $repository->trackerTableExists();
    }

    /**
     * Build a `SqlService` stub whose `getColumn()` calls always throw the
     * supplied exception. Used to simulate driver-level failure paths the
     * production wiring would surface but unit tests can't reach without
     * a live DB.
     *
     * @param DtoException $exception The exception every getColumn call should throw
     *
     * @return SqlService
     */
    private function makeSqlServiceThrowing(DtoException $exception): SqlService
    {
        $sqlService = $this->createStub(SqlService::class);
        $sqlService->method('getColumn')->willThrowException($exception);
        return $sqlService;
    }

    /**
     * Construct a `DtoException` carrying a `PdoError` DTO with the
     * given MySQL errno — matching the wrapping
     * `AbstractPdoSqlService::getPdoStatement()` does on PDO failure
     * (the repository under test reads the errno from the wrapped DTO,
     * not from a raw `PDOException`).
     *
     * @param int    $mysqlErrno MySQL native error code
     * @param string $sqlState   ANSI SQLSTATE (default: 'HY000')
     * @param string $message    Driver-supplied message
     *
     * @return DtoException
     */
    private function makeDtoException(int $mysqlErrno, string $sqlState = 'HY000', string $message = 'Simulated driver failure'): DtoException
    {
        return new DtoException(
            'The query execution failed.',
            13001,
            new PdoError(
                sqlState:      $sqlState,
                driverCode:    (string) $mysqlErrno,
                driverMessage: $message,
            ),
        );
    }
}
