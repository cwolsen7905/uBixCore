<?php

declare(strict_types=1);

namespace Ubix\Tests\Middleware;

use Ubix\Middleware\SessionAuthenticationMiddleware;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Middleware\SessionAuthenticationMiddleware
 *
 * @coversDefaultClass \Ubix\Middleware\SessionAuthenticationMiddleware
 */
final class SessionAuthenticationMiddlewareTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SessionAuthenticationMiddleware::class);
    }
}
