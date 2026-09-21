<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Audit;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Audit\PiiAccess;
use Ubix\Repository\PiiAccessAudit\PiiAccessAuditWriterInterface as PiiAccessAuditWriter;
use Ubix\Service\Audit\PiiAccessAuditService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Audit\PiiAccessAuditService
 *
 * @coversDefaultClass \Ubix\Service\Audit\PiiAccessAuditService
 */
final class PiiAccessAuditServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PiiAccessAuditService::class);
    }

    /**
     * Duplicates and non-ids are dropped before the write
     *
     * @return void
     * @covers ::record
     */
    public function testRecordDeduplicatesSubjects(): void
    {
        $writer = $this->createMock(PiiAccessAuditWriter::class);
        $writer->expects($this->once())
            ->method('recordAccess')
            ->with($this->callback(static function (PiiAccess $access): bool {
                return $access->subjectIds === [4, 5] && $access->actorId === 1 && $access->reason === 'support';
            }));

        $service = new PiiAccessAuditService(logger: $this->createStub(Logger::class), writer: $writer);
        $service->record(new PiiAccess(actorId: 1, entityType: 'member', subjectIds: [4, 5, 4, 0, -2], reason: 'support'));
    }

    /**
     * No subjects means nothing to record
     *
     * @return void
     * @covers ::record
     */
    public function testRecordSkipsAnEmptyResult(): void
    {
        $writer = $this->createMock(PiiAccessAuditWriter::class);
        $writer->expects($this->never())->method('recordAccess');

        $service = new PiiAccessAuditService(logger: $this->createStub(Logger::class), writer: $writer);
        $service->record(new PiiAccess(actorId: 1, entityType: 'member', subjectIds: [], reason: 'support'));
    }

    /**
     * An incomplete record is refused rather than half-written
     *
     * @return void
     * @covers ::record
     */
    public function testRecordRefusesAnIncompleteAccess(): void
    {
        $writer = $this->createMock(PiiAccessAuditWriter::class);
        $writer->expects($this->never())->method('recordAccess');

        $service = new PiiAccessAuditService(logger: $this->createStub(Logger::class), writer: $writer);

        $incomplete = [
            'entity type too long' => new PiiAccess(actorId: 1, entityType: str_repeat('x', 33), subjectIds: [1], reason: 'support'),
            'no actor'             => new PiiAccess(actorId: 0, entityType: 'member', subjectIds: [1], reason: 'support'),
            'no entity type'       => new PiiAccess(actorId: 1, entityType: '', subjectIds: [1], reason: 'support'),
            'no reason'            => new PiiAccess(actorId: 1, entityType: 'member', subjectIds: [1], reason: ''),
        ];

        foreach ($incomplete as $case => $access) {
            try {
                $service->record($access);
                $this->fail('Recorded an incomplete access: ' . $case);
            } catch (InvalidArgumentException $exception) {
                $this->assertNotSame('', $exception->getMessage(), $case);
            }
        }
    }
}
