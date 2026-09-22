<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\RateLimit;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the outcome of one rate-limited attempt
 *
 * Returned by {@see \Ubix\Service\RateLimit\RateLimiterService::hit()}. When
 * `allowed` is false a host answers 429 with `Retry-After: retryAfterSeconds`.
 *
 * @see \Ubix\Tests\DataTransferObject\RateLimit\RateLimitResultTest PHPUnit test case
 */
final readonly class RateLimitResult implements Dto
{
    /**
     * Constructor
     *
     * @param bool $allowed           Whether this attempt is within the limit
     * @param int  $limit             The limit for the window
     * @param int  $remaining         Attempts left in the current window after this one
     * @param int  $retryAfterSeconds Seconds until the window resets (0 when allowed)
     */
    public function __construct(
        public readonly bool $allowed = true,
        public readonly int $limit = 0,
        public readonly int $remaining = 0,
        public readonly int $retryAfterSeconds = 0,
    ) {
    }
}
