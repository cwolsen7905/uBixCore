<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Subscription;

use Ubix\Enum\Subscription\SubscriptionStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Subscription\SubscriptionStatus
 *
 * @coversDefaultClass \Ubix\Enum\Subscription\SubscriptionStatus
 */
final class SubscriptionStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SubscriptionStatus::class);
    }
}
