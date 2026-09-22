<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\RateLimit;

use Ubix\DataTransferObject\RateLimit\RateLimitResult;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\RateLimit\RateLimitResult
 *
 * @coversDefaultClass \Ubix\DataTransferObject\RateLimit\RateLimitResult
 */
final class RateLimitResultTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RateLimitResult::class);
    }
}
