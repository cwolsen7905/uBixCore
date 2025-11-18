<?php

declare(strict_types=1);

namespace Ubix\Tests\SimpleCache;

use Ubix\SimpleCache\MemcachedLegacySimpleCache;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\SimpleCache\MemcachedLegacySimpleCache
 *
 * @coversDefaultClass \Ubix\SimpleCache\MemcachedLegacySimpleCache
 */
final class MemcachedLegacySimpleCacheTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MemcachedLegacySimpleCache::class);
    }
}
