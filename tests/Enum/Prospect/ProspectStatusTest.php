<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Prospect;

use Ubix\Enum\Prospect\ProspectStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Prospect\ProspectStatus
 *
 * @coversDefaultClass \Ubix\Enum\Prospect\ProspectStatus
 */
final class ProspectStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectStatus::class);
    }
}
