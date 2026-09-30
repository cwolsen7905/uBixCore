<?php

declare(strict_types=1);

namespace Ubix\Tests\SimpleCache;

use DateInterval;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Exception\SimpleCacheInvalidArgumentException;
use Ubix\SimpleCache\ApcuSimpleCache;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\SimpleCache\ApcuSimpleCache
 *
 * The behaviour tests need APCu loaded and enabled for the CLI (`apc.enable_cli=1`,
 * a system-level setting — run `php -d apc.enable_cli=1 vendor/bin/phpunit`); they
 * are skipped otherwise, and say so. The standards test always runs.
 *
 * @coversDefaultClass \Ubix\SimpleCache\ApcuSimpleCache
 */
final class ApcuSimpleCacheTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ApcuSimpleCache::class);
    }

    /**
     * Test set, get, has and delete round-trip, with the default on a miss
     *
     * @return void
     */
    public function testRoundTrip(): void
    {
        $cache = $this->cache();

        $this->assertSame('fallback', $cache->get('missing', 'fallback'));
        $this->assertTrue($cache->set('key', ['a' => 'b'], 60));
        $this->assertTrue($cache->has('key'));
        $this->assertSame(['a' => 'b'], $cache->get('key'));
        $this->assertTrue($cache->delete('key'));
        $this->assertFalse($cache->has('key'));
        $this->assertTrue($cache->delete('key'), 'deleting a missing key is not an error');
    }

    /**
     * Test that a non-positive TTL deletes, as PSR-16 requires
     *
     * @return void
     */
    public function testNonPositiveTtlDeletes(): void
    {
        $cache = $this->cache();
        $cache->set('key', 'value', 60);

        $this->assertTrue($cache->set('key', 'other', 0));
        $this->assertFalse($cache->has('key'));

        $cache->set('key', 'value', new DateInterval('PT1M'));
        $this->assertSame('value', $cache->get('key'));
    }

    /**
     * Test that clear() removes only this cache's prefix
     *
     * @return void
     */
    public function testClearIsScopedToThePrefix(): void
    {
        $mine  = $this->cache('test.mine.');
        $other = $this->cache('test.other.');
        $mine->set('a', 1);
        $other->set('a', 2);

        $this->assertTrue($mine->clear());
        $this->assertFalse($mine->has('a'));
        $this->assertSame(2, $other->get('a'));
        $other->clear();
    }

    /**
     * Test the multiple-key operations
     *
     * @return void
     */
    public function testMultiple(): void
    {
        $cache = $this->cache();

        $this->assertTrue($cache->setMultiple(['a' => 1, 'b' => 2], 60));
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 'none'], $cache->getMultiple(['a', 'b', 'c'], 'none'));
        $this->assertTrue($cache->deleteMultiple(['a', 'b']));
        $this->assertFalse($cache->has('a'));
    }

    /**
     * Test that invalid keys are refused, as in the other PSR-16 caches
     *
     * @return void
     */
    public function testInvalidKeyIsRefused(): void
    {
        $this->expectException(SimpleCacheInvalidArgumentException::class);

        $this->cache()->get('has/slash');
    }

    /**
     * A cache under a test prefix, or a skip when APCu is not available here
     *
     * @param string $prefix Key prefix
     *
     * @return ApcuSimpleCache
     */
    private function cache(string $prefix = 'test.apcu.'): ApcuSimpleCache
    {
        $cache = new ApcuSimpleCache($this->createStub(Logger::class), $prefix);

        if (!$cache->isAvailable()) {
            $this->markTestSkipped('APCu is not loaded, or not enabled for the CLI (apc.enable_cli=1)');
        }

        $cache->clear();

        return $cache;
    }
}
