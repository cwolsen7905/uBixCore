<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Creator;

use Ubix\Enum\Creator\CreatorStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Creator\CreatorStatus
 *
 * @coversDefaultClass \Ubix\Enum\Creator\CreatorStatus
 */
final class CreatorStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CreatorStatus::class);
    }
}
