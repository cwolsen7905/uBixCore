<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\SchemaDiffResult;
use Ubix\Service\Migration\MigrationCredentialResolverService;
use Ubix\Service\Migration\SchemaDiffService;
use Ubix\Service\ProcessService;
use Ubix\Service\ProjectRootService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Migration\SchemaDiffService
 *
 * @coversDefaultClass \Ubix\Service\Migration\SchemaDiffService
 * @coversDefaultClass \Ubis\Service\Migration\SchemaDiffService
 */
final class SchemaDiffServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SchemaDiffService::class);
    }

    /**
     * A filter naming a database outside the Ubix-consumed set short-circuits
     * to a single result without ever shelling out or reading the cluster.
     *
     * @return void
     *
     * @covers ::diffAll
     */
    public function testDiffAllReturnsSingleResultForUnknownDatabaseFilter(): void
    {
        $results = $this->service()->diffAll('NOT_A_REAL_DATABASE_9016000');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(SchemaDiffResult::class, $results[0]);
    }

    /**
     * The short-circuit result for an unknown database is an inconclusive
     * error report: no drift, empty line lists, and an explanatory message.
     *
     * @return void
     *
     * @covers ::diffAll
     */
    public function testDiffAllUnknownDatabaseResultIsInconclusiveError(): void
    {
        $result = $this->service()->diffAll('NOT_A_REAL_DATABASE_9016001')[0];

        $this->assertFalse($result->hasDrift);
        $this->assertSame([], $result->extraInLive);
        $this->assertSame([], $result->missingFromLive);
        $this->assertSame(
            'Database `NOT_A_REAL_DATABASE_9016001` is not in the Ubix-consumed set; nothing to diff.',
            $result->errorMessage,
        );
    }

    /**
     * The unknown-database error result echoes the offending filter back in
     * its `database` field so callers can attribute the failure.
     *
     * @return void
     *
     * @covers ::diffAll
     */
    public function testDiffAllUnknownDatabaseResultEchoesTheFilter(): void
    {
        $result = $this->service()->diffAll('flirt4free_typo_9016002')[0];

        $this->assertSame('flirt4free_typo_9016002', $result->database);
    }

    /**
     * The Ubix-consumed set is case-sensitive: a known database supplied in
     * the wrong case is treated as unknown and short-circuits to an error.
     *
     * @return void
     *
     * @covers ::diffAll
     */
    public function testDiffAllTreatsWrongCaseDatabaseAsUnknown(): void
    {
        $result = $this->service()->diffAll('systems')[0];

        $this->assertSame('systems', $result->database);
        $this->assertNotNull($result->errorMessage);
    }

    /**
     * Build the service with real collaborators; only the logger is stubbed,
     * per the no-mock-internals policy.
     *
     * @return SchemaDiffService
     */
    private function service(): SchemaDiffService
    {
        $logger = $this->createStub(Logger::class);

        return new SchemaDiffService(
            $logger,
            new ProcessService($logger),
            new MigrationCredentialResolverService($logger),
            new ProjectRootService($logger, dirname(__DIR__, 3)),
        );
    }
}
