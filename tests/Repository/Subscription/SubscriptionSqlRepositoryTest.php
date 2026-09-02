<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Subscription;

use Ubix\Repository\Subscription\SubscriptionSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Subscription\SubscriptionSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Subscription\SubscriptionSqlRepository
 */
final class SubscriptionSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SubscriptionSqlRepository::class);
    }
}
