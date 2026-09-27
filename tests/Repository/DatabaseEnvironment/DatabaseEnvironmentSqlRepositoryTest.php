<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\DatabaseEnvironment;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\PdoError;
use Ubix\Enum\Env;
use Ubix\Exception\DtoException;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentSqlRepository;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentSqlRepository
 */
final class DatabaseEnvironmentSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DatabaseEnvironmentSqlRepository::class);
    }

    /**
     * A missing table or SYSTEMS database reads as "unlabelled", not an error
     *
     * @return void
     */
    public function testAMissingTableIsUnlabelled(): void
    {
        $sqlService = $this->createStub(SqlService::class);
        $sqlService->method('getRow')->willThrowException(new DtoException('missing', 0, new PdoError(driverCode: '1146')));

        $this->assertNull((new DatabaseEnvironmentSqlRepository($this->createStub(Logger::class), $sqlService))->current());
    }

    /**
     * Any other failure is surfaced: a guard must not read "could not read" as "no label"
     *
     * @return void
     */
    public function testOtherFailuresAreSurfaced(): void
    {
        $sqlService = $this->createStub(SqlService::class);
        $sqlService->method('getRow')->willThrowException(new DtoException('denied', 0, new PdoError(driverCode: '1142')));

        $this->expectException(DtoException::class);
        (new DatabaseEnvironmentSqlRepository($this->createStub(Logger::class), $sqlService))->current();
    }

    /**
     * The row is hydrated into the label
     *
     * @return void
     */
    public function testTheRowIsHydrated(): void
    {
        $sqlService = $this->createStub(SqlService::class);
        $sqlService->method('getRow')->willReturn(['environment' => 'staging', 'labelled_at' => '2026-09-27 10:00:00', 'labelled_by' => 'ci:cwolsen', 'sanitised_at' => null, 'sanitised_by' => null]);

        $label = (new DatabaseEnvironmentSqlRepository($this->createStub(Logger::class), $sqlService))->current();

        $this->assertNotNull($label);
        $this->assertSame(Env::STAGING, $label->environment);
        $this->assertNull($label->sanitisedAt);
    }

    /**
     * Labelling replaces the single row and clears the sanitised stamp
     *
     * @return void
     */
    public function testLabellingClearsSanitised(): void
    {
        $sqlService = $this->createMock(SqlService::class);
        $sqlService->expects($this->once())
            ->method('query')
            ->with($this->logicalAnd($this->stringContains('REPLACE INTO'), $this->stringContains('NULL, NULL')), ['actor' => 'someone', 'environment' => 'staging']);

        (new DatabaseEnvironmentSqlRepository($this->createStub(Logger::class), $sqlService))->label(Env::STAGING, 'someone');
    }
}
