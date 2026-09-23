<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PiiAccessAudit;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Audit\PiiAccess;
use Ubix\Repository\PiiAccessAudit\PiiAccessAuditSqlRepository;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PiiAccessAudit\PiiAccessAuditSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PiiAccessAudit\PiiAccessAuditSqlRepository
 */
final class PiiAccessAuditSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PiiAccessAuditSqlRepository::class);
    }

    /**
     * One statement, one row per subject, shared values bound once
     *
     * @return void
     * @covers ::recordAccess
     */
    public function testRecordAccessWritesOneRowPerSubjectInOneStatement(): void
    {
        $sqlService = $this->createMock(SqlService::class);
        $sqlService->expects($this->once())
            ->method('query')
            ->with(
                $this->callback(static function (string $sql): bool {
                    return str_contains($sql, '.Pii_Access_Audits')
                        && substr_count($sql, '(:admin_id, :entity_type, :subject') === 3;
                }),
                [
                    'admin_id'    => 7,
                    'entity_type' => 'member',
                    'reason'      => 'ledger_view',
                    'search_term' => 'status=paid',
                    'subject0'    => 11,
                    'subject1'    => 12,
                    'subject2'    => 13,
                ],
                true,
            )
            ->willReturn(3);

        $repository = new PiiAccessAuditSqlRepository(logger: $this->createStub(Logger::class), sqlService: $sqlService);
        $repository->recordAccess(new PiiAccess(
            actorId:    7,
            entityType: 'member',
            subjectIds: [11, 12, 13],
            reason:     'ledger_view',
            searchTerm: 'status=paid',
        ));
    }

    /**
     * An empty result touches no one, so nothing is written
     *
     * @return void
     * @covers ::recordAccess
     */
    public function testRecordAccessWritesNothingForNoSubjects(): void
    {
        $sqlService = $this->createMock(SqlService::class);
        $sqlService->expects($this->never())->method('query');

        $repository = new PiiAccessAuditSqlRepository(logger: $this->createStub(Logger::class), sqlService: $sqlService);
        $repository->recordAccess(new PiiAccess(actorId: 7, entityType: 'member', subjectIds: [], reason: 'ledger_view'));
    }

    /**
     * A purge deletes only what is older than the cutoff, and is bounded
     *
     * Keeping "who looked at this person" forever is itself a privacy problem,
     * so hosts expire the trail on their own schedule. The bound matters: the
     * first run after a retention policy is introduced can face years of rows.
     *
     * @return void
     */
    public function testAPurgeIsBoundedAndCutoffScoped(): void
    {
        $sqlService = $this->createMock(SqlService::class);
        $sqlService->expects($this->once())
            ->method('query')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('DELETE FROM'),
                    $this->stringContains('Pii_Access_Audits'),
                    $this->stringContains('date_created < :cutoff'),
                    $this->stringContains('LIMIT 500'),
                ),
                ['cutoff' => '2025-09-22 00:00:00'],
            )
            ->willReturn(500);

        (new PiiAccessAuditSqlRepository($this->createStub(Logger::class), $sqlService))
            ->purgeAccessesBefore('2025-09-22 00:00:00', 500);
    }

    /**
     * A nonsense limit still bounds the statement
     *
     * @return void
     */
    public function testALimitIsAlwaysAtLeastOne(): void
    {
        $sqlService = $this->createMock(SqlService::class);
        $sqlService->expects($this->once())
            ->method('query')
            ->with($this->stringContains('LIMIT 1'), $this->anything())
            ->willReturn(0);

        (new PiiAccessAuditSqlRepository($this->createStub(Logger::class), $sqlService))
            ->purgeAccessesBefore('2025-09-22 00:00:00', 0);
    }
}
