<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Migration;

use Ubix\Enum\Migration\SchemaDiffMode;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Migration\SchemaDiffMode
 *
 * @coversDefaultClass \Ubix\Enum\Migration\SchemaDiffMode
 */
final class SchemaDiffModeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SchemaDiffMode::class);
    }

    /**
     * Only replay mode may gate a build
     *
     * The distinction the migrations standard section 9 turns on: reference-dump
     * reports the whole migration history as drift under the frozen-baseline policy,
     * so failing a pipeline on it would teach everyone to ignore the job.
     *
     * @return void
     */
    public function testOnlyReplayIsGateable(): void
    {
        $this->assertTrue(SchemaDiffMode::REPLAY->isGateable());
        $this->assertFalse(SchemaDiffMode::REFERENCE_DUMP->isGateable());
    }

    /**
     * The values are the CLI spellings the standard documents
     *
     * @return void
     */
    public function testValuesAreTheDocumentedCliSpellings(): void
    {
        $this->assertSame('replay', SchemaDiffMode::REPLAY->value);
        $this->assertSame('reference-dump', SchemaDiffMode::REFERENCE_DUMP->value);
    }
}
