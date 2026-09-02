<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Tier;

use Ubix\Enum\Tier\TierStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Tier\TierStatus
 *
 * @coversDefaultClass \Ubix\Enum\Tier\TierStatus
 */
final class TierStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TierStatus::class);
    }
}
