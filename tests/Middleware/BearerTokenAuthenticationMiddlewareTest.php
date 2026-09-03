<?php

declare(strict_types=1);

namespace Ubix\Tests\Middleware;

use Ubix\Middleware\BearerTokenAuthenticationMiddleware;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Middleware\BearerTokenAuthenticationMiddleware
 *
 * @coversDefaultClass \Ubix\Middleware\BearerTokenAuthenticationMiddleware
 */
final class BearerTokenAuthenticationMiddlewareTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(BearerTokenAuthenticationMiddleware::class);
    }
}
