<?php

declare(strict_types=1);

namespace Ubix\Tests\Middleware;

use Ubix\Middleware\NormalizedIpAddressMiddleware;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Middleware\NormalizedIpAddressMiddleware
 *
 * @coversDefaultClass \Ubix\Middleware\NormalizedIpAddressMiddleware
 */
final class NormalizedIpAddressMiddlewareTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(NormalizedIpAddressMiddleware::class);
    }
}
