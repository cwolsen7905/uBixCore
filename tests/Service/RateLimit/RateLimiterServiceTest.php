<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\RateLimit;

use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\RateLimit\RateLimiterService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\RateLimit\RateLimiterService
 *
 * @coversDefaultClass \Ubix\Service\RateLimit\RateLimiterService
 */
final class RateLimiterServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const T0 = 1800000000; // A window boundary for 600 s windows (1800000000 % 600 === 0)

    private ArrayAdapter $store;

    private RateLimiterService $limiter;

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RateLimiterService::class);
    }

    /**
     * Test that attempts up to the limit pass, the next is refused with the time left in the window
     *
     * @return void
     */
    public function testRefusesPastTheLimitUntilTheWindowEnds(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $result = $this->limiter->hit('identify-ip', '203.0.113.9', 3, 600, self::T0 + 10);
            $this->assertTrue($result->allowed);
            $this->assertSame(3 - $i, $result->remaining);
        }

        $refused = $this->limiter->hit('identify-ip', '203.0.113.9', 3, 600, self::T0 + 10);
        $this->assertFalse($refused->allowed);
        $this->assertSame(590, $refused->retryAfterSeconds);

        $this->assertTrue($this->limiter->hit('identify-ip', '203.0.113.9', 3, 600, self::T0 + 600)->allowed, 'a new window starts fresh');
    }

    /**
     * Test that scopes and subjects are counted separately
     *
     * @return void
     */
    public function testScopesAndSubjectsAreIndependent(): void
    {
        $this->limiter->hit('identify-ip', '203.0.113.9', 1, 600, self::T0);

        $this->assertFalse($this->limiter->hit('identify-ip', '203.0.113.9', 1, 600, self::T0)->allowed);
        $this->assertTrue($this->limiter->hit('identify-ip', '198.51.100.7', 1, 600, self::T0)->allowed);
        $this->assertTrue($this->limiter->hit('identify-email', '203.0.113.9', 1, 600, self::T0)->allowed);
    }

    /**
     * Test that the subject never appears in a cache key, and keys follow the memcache-keys standard
     *
     * @return void
     */
    public function testSubjectIsHashedInTheKey(): void
    {
        $this->limiter->hit('identify-email', 'person@example.com', 5, 600, self::T0);

        $keys = array_keys($this->store->getValues());
        $this->assertCount(1, $keys);
        $this->assertStringStartsWith('UBIX_RATE_LIMIT_IDENTIFY_EMAIL_' . hash('sha256', 'person@example.com'), (string) $keys[0]);
        $this->assertStringNotContainsString('example.com', (string) $keys[0]);
    }

    /**
     * Test that a cache that cannot record attempts fails open rather than locking everyone out
     *
     * @return void
     */
    public function testUnavailableCacheAllowsTheAttempt(): void
    {
        $cache = $this->createStub(SimpleCache::class);
        $cache->method('get')->willReturn(0);
        $cache->method('set')->willReturn(false);

        $this->assertTrue((new RateLimiterService(new NullLogger(), $cache))->hit('identify-ip', 'x', 1, 60, self::T0)->allowed);
    }

    /**
     * Test that a malformed scope is rejected as a programming error
     *
     * @return void
     */
    public function testInvalidScopeIsRejected(): void
    {
        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::RATE_LIMIT_SCOPE_INVALID->value);
        $this->limiter->hit('bad scope!', 'x', 1, 60, self::T0);
    }

    /**
     * Fresh in-memory cache per test
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->store   = new ArrayAdapter();
        $this->limiter = new RateLimiterService(new NullLogger(), new Psr16Cache($this->store));
    }
}
