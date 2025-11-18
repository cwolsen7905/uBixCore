<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Studio;

use Ubix\Enum\Studio\StudioStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Studio\StudioStatus
 *
 * @coversDefaultClass \Ubix\Enum\Studio\StudioStatus
 */
final class StudioStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(StudioStatus::class);
    }
}
