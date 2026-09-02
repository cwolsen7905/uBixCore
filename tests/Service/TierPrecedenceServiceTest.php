<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\Subscription;
use Ubix\Model\Tier;
use Ubix\Repository\Subscription\SubscriptionReaderInterface as SubscriptionReader;
use Ubix\Repository\Tier\TierReaderInterface as TierReader;
use Ubix\Service\TierPrecedenceService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\TierPrecedenceService
 *
 * @coversDefaultClass \Ubix\Service\TierPrecedenceService
 */
final class TierPrecedenceServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TierPrecedenceService::class);
    }

    /**
     * No subscription resolves to the implicit free tier (FR-502)
     *
     * @return void
     */
    public function testNoSubscriptionResolvesToZero(): void
    {
        $subscriptions = $this->createStub(SubscriptionReader::class);
        $subscriptions->method('getActiveSubscription')->willReturn(null);

        $resolver = new TierPrecedenceService($this->createStub(Logger::class), $subscriptions, $this->createStub(TierReader::class));

        $this->assertSame(0, $resolver->resolve(1, 2));
    }

    /**
     * An active subscription resolves to its tier's position
     *
     * @return void
     */
    public function testActiveSubscriptionResolvesToTierPosition(): void
    {
        $subscriptions = $this->createStub(SubscriptionReader::class);
        $subscriptions->method('getActiveSubscription')->willReturn(new Subscription(id: 5, userId: 1, creatorId: 2, tierId: 9));
        $tiers = $this->createStub(TierReader::class);
        $tiers->method('getTierById')->willReturn(new Tier(id: 9, creatorId: 2, position: 3));

        $resolver = new TierPrecedenceService($this->createStub(Logger::class), $subscriptions, $tiers);

        $this->assertSame(3, $resolver->resolve(1, 2));
    }

    /**
     * A dangling tier reference degrades safely to the free tier
     *
     * @return void
     */
    public function testMissingTierResolvesToZero(): void
    {
        $subscriptions = $this->createStub(SubscriptionReader::class);
        $subscriptions->method('getActiveSubscription')->willReturn(new Subscription(id: 5, userId: 1, creatorId: 2, tierId: 9));
        $tiers = $this->createStub(TierReader::class);
        $tiers->method('getTierById')->willReturn(null);

        $resolver = new TierPrecedenceService($this->createStub(Logger::class), $subscriptions, $tiers);

        $this->assertSame(0, $resolver->resolve(1, 2));
    }
}
