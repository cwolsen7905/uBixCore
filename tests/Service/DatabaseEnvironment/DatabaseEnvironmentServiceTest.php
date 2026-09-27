<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\DatabaseEnvironment;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\DatabaseEnvironment\DatabaseEnvironment;
use Ubix\Enum\Env;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentReaderInterface as DatabaseEnvironmentReader;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentWriterInterface as DatabaseEnvironmentWriter;
use Ubix\Service\DatabaseEnvironment\DatabaseEnvironmentService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\DatabaseEnvironment\DatabaseEnvironmentService
 *
 * @coversDefaultClass \Ubix\Service\DatabaseEnvironment\DatabaseEnvironmentService
 */
final class DatabaseEnvironmentServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DatabaseEnvironmentService::class);
    }

    /**
     * A staging run against a database labelled prod is a conflict; a matching or missing label is not
     *
     * @return void
     */
    public function testConflictIsTheOtherLabel(): void
    {
        $this->assertSame(Env::PROD, $this->service(Env::PROD)->conflictWith(Env::STAGING));
        $this->assertNull($this->service(Env::STAGING)->conflictWith(Env::STAGING));
        $this->assertNull($this->service(null)->conflictWith(Env::PROD));
    }

    /**
     * Test and sandbox are shared scratch databases: never refused, never labelled -- but they get the table, so a replay's schema matches a tier's
     *
     * @return void
     */
    public function testScratchTargetsTakeNoPart(): void
    {
        $writer = $this->createMock(DatabaseEnvironmentWriter::class);
        $writer->expects($this->never())->method('label');
        $writer->expects($this->once())->method('ensureTable');

        $service = $this->service(Env::DEV, $writer);

        $this->assertNull($service->conflictWith(Env::TEST));
        $this->assertFalse($service->labelIfUnlabelled(Env::SANDBOX, 'ci'));
    }

    /**
     * An unlabelled database is labelled; a labelled one is left alone
     *
     * @return void
     */
    public function testLabelsOnlyWhenUnlabelled(): void
    {
        $writer = $this->createMock(DatabaseEnvironmentWriter::class);
        $writer->expects($this->exactly(2))->method('ensureTable');
        $writer->expects($this->once())->method('label')->with(Env::STAGING, 'ci:cwolsen');

        $this->assertTrue($this->service(null, $writer)->labelIfUnlabelled(Env::STAGING, 'ci:cwolsen'));
        $this->assertFalse($this->service(Env::PROD, $writer)->labelIfUnlabelled(Env::STAGING, 'ci:cwolsen'));
    }

    /**
     * Relabelling requires naming the current label
     *
     * @return void
     */
    public function testRelabelRequiresTheCurrentLabel(): void
    {
        $writer = $this->createMock(DatabaseEnvironmentWriter::class);
        $writer->expects($this->never())->method('label');

        $this->expectException(InvalidArgumentException::class);
        $this->service(Env::PROD, $writer)->relabel(Env::STAGING, Env::DEV, 'someone');
    }

    /**
     * A production-labelled database can never be stamped sanitised
     *
     * @return void
     */
    public function testProductionIsNeverMarkedSanitised(): void
    {
        $writer = $this->createMock(DatabaseEnvironmentWriter::class);
        $writer->expects($this->never())->method('markSanitised');

        $this->expectException(InvalidArgumentException::class);
        $this->service(Env::PROD, $writer)->markSanitised(Env::PROD, 'someone');
    }

    /**
     * The service over a reader returning the given label
     *
     * @param ?Env                       $label  The current label, or null for none
     * @param ?DatabaseEnvironmentWriter $writer The writer
     *
     * @return DatabaseEnvironmentService The service
     */
    private function service(?Env $label, ?DatabaseEnvironmentWriter $writer = null): DatabaseEnvironmentService
    {
        $reader = $this->createStub(DatabaseEnvironmentReader::class);
        $reader->method('current')->willReturn($label === null ? null : new DatabaseEnvironment($label));

        return new DatabaseEnvironmentService($this->createStub(Logger::class), $reader, $writer ?? $this->createStub(DatabaseEnvironmentWriter::class));
    }
}
