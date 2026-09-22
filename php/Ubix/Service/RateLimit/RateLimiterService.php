<?php

declare(strict_types=1);

namespace Ubix\Service\RateLimit;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Ubix\DataTransferObject\RateLimit\RateLimitResult;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;

/**
 * Fixed-window rate limiter over a shared PSR-16 cache
 *
 * `hit('identify-ip', $ip, 30, 600)` counts one attempt by `$ip` in the
 * `identify-ip` scope and says whether it is within 30 per 10 minutes. The
 * scope and numbers are the host's; the framework ships no defaults that mean
 * anything.
 *
 * Keys follow `docs/standards/memcache-keys.md`:
 * `UBIX_RATE_LIMIT_<SCOPE>_<sha256(subject)>_<window>`. The subject -- an email,
 * an IP -- is hashed, so no address sits in cache in the clear and key length is
 * bounded whatever the subject is.
 *
 * **Approximate under concurrency, deliberately.** PSR-16 has no atomic
 * increment, so two simultaneous attempts can both read N and both write N+1.
 * An abuse limit needs "about 30 per 10 minutes", not "exactly 30"; a burst of
 * genuinely simultaneous requests gains at most a few extra attempts per
 * window, and the next read is right again. Do not use this where an exact
 * count is the requirement (a spend limit, a quota someone pays for).
 *
 * If the cache is unavailable the attempt is **allowed** and logged: a limiter
 * that failed closed would turn a memcached outage into nobody being able to
 * sign in. Hosts that need fail-closed behaviour check the result themselves.
 *
 * @see \Ubix\Tests\Service\RateLimit\RateLimiterServiceTest PHPUnit test case
 */
final class RateLimiterService
{
    private const KEY_PREFIX = 'UBIX_RATE_LIMIT_';

    /**
     * Constructor
     *
     * @param Logger      $logger The Monolog logger
     * @param SimpleCache $cache  A PSR-16 cache shared by every pod that serves the limited route
     */
    public function __construct(
        private Logger $logger,
        private SimpleCache $cache,
    ) {
    }

    /**
     * Count one attempt and say whether it is within the limit
     *
     * @param string $scope           What is being limited, e.g. `identify-ip` (letters, digits, `-`, `_`)
     * @param string $subject         Who is attempting, e.g. an IP address or a lower-cased email
     * @param int    $limit           Attempts allowed per window (at least 1)
     * @param int    $windowSeconds   Window length in seconds (at least 1)
     * @param ?int   $atUnixTimestamp The time of the attempt; null for now (tests pass a fixed time)
     *
     * @throws DtoException When the scope, limit or window is invalid -- a programming error, not a user one
     *
     * @return RateLimitResult Whether the attempt is allowed, and when to retry if not
     */
    public function hit(string $scope, string $subject, int $limit, int $windowSeconds, ?int $atUnixTimestamp = null): RateLimitResult
    {
        if (preg_match('/^[A-Za-z0-9_-]{1,40}$/', $scope) !== 1 || $limit < 1 || $windowSeconds < 1) {
            throw new DtoException('Invalid rate-limit definition.', ExceptionCode::RATE_LIMIT_SCOPE_INVALID->value);
        }

        $now        = $atUnixTimestamp ?? (new DateTime())->getTimestamp();
        $window     = intdiv($now, $windowSeconds);
        $retryAfter = (($window + 1) * $windowSeconds) - $now;
        $key        = self::KEY_PREFIX . strtoupper(str_replace('-', '_', $scope)) . '_' . hash('sha256', $subject) . '_' . $window;
        $countSoFar = $this->cache->get($key, 0);
        $count      = (is_int($countSoFar) ? $countSoFar : 0) + 1;

        if (!$this->cache->set($key, $count, $retryAfter)) {
            $this->logger->warning('Rate limiter could not record an attempt; allowing it', ['scope' => $scope]);

            return new RateLimitResult(allowed: true, limit: $limit, remaining: 0, retryAfterSeconds: 0);
        }

        if ($count > $limit) {
            return new RateLimitResult(allowed: false, limit: $limit, remaining: 0, retryAfterSeconds: $retryAfter);
        }

        return new RateLimitResult(allowed: true, limit: $limit, remaining: $limit - $count, retryAfterSeconds: 0);
    }
}
