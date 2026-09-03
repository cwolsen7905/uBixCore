<?php

declare(strict_types=1);

namespace Ubix\Tests\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Middleware\RoleAuthorizationMiddleware;
use Ubix\Service\JsonService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Middleware\RoleAuthorizationMiddleware
 *
 * @coversDefaultClass \Ubix\Middleware\RoleAuthorizationMiddleware
 */
final class RoleAuthorizationMiddlewareTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RoleAuthorizationMiddleware::class);
    }

    /**
     * Allow/deny matrix over the multi-value roles field
     *
     * @return void
     */
    public function testAllowDenyMatrix(): void
    {
        $factory = new Psr17Factory();
        $request = $factory->createServerRequest('GET', '/creator/only');
        $handler = $this->createStub(Handler::class);
        $handler->method('handle')->willReturn($factory->createResponse(200));

        $middleware = new RoleAuthorizationMiddleware(
            $this->createStub(Logger::class),
            $factory,
            new JsonService($this->createStub(Logger::class)),
            'creator',
        );

        $cases = [
            ['creator', 200],
            ['supporter,creator', 200],
            [' creator , admin ', 200],
            ['supporter', 403],
            ['', 403],
            [null, 403],
        ];

        foreach ($cases as [$roles, $expected]) {
            $_SESSION['user'] = $roles === null ? [] : ['id' => 1, 'roles' => $roles];
            $this->assertSame($expected, $middleware->process($request, $handler)->getStatusCode(), 'roles=' . ($roles ?? 'null'));
        }

        unset($_SESSION['user']);
    }
}
