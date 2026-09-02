<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Tier;

use Ubix\Enum\Tier\TierBillingInterval;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Tier\TierBillingInterval
 *
 * @coversDefaultClass \Ubix\Enum\Tier\TierBillingInterval
 */
final class TierBillingIntervalTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TierBillingInterval::class);
    }
}
