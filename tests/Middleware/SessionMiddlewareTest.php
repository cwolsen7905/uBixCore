<?php

declare(strict_types=1);

namespace Ubix\Tests\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\NullLogger;
use SessionHandler;
use Ubix\Middleware\SessionMiddleware;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Middleware\SessionMiddleware
 *
 * @coversDefaultClass \Ubix\Middleware\SessionMiddleware
 */
final class SessionMiddlewareTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SessionMiddleware::class);
    }

    /**
     * Test the session cookie's attributes: always HttpOnly, SameSite Lax by default
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function testCookieIsHttpOnlyAndLaxByDefault(): void
    {
        $params = $this->cookieParamsAfterProcessing(new SessionMiddleware(new NullLogger(), new SessionHandler()), 'https://api.example.com/auth');

        $this->assertTrue($params['httponly']);
        $this->assertSame('Lax', $params['samesite']);
        $this->assertTrue($params['secure']);
        $this->assertSame('.example.com', $params['domain']);
    }

    /**
     * Test that SameSite=None is honoured over HTTPS and refused over plain HTTP
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function testSameSiteNoneOnlyOverHttps(): void
    {
        $params = $this->cookieParamsAfterProcessing(new SessionMiddleware(new NullLogger(), new SessionHandler(), 'None'), 'http://api.example.com/auth');

        $this->assertSame('Lax', $params['samesite']);
    }

    /**
     * Test that a host can narrow the cookie domain
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function testCookieDomainCanBeNarrowed(): void
    {
        $params = $this->cookieParamsAfterProcessing(new SessionMiddleware(new NullLogger(), new SessionHandler(), 'Lax', '.dev.example.com'), 'https://api.dev.example.com/auth');

        $this->assertSame('.dev.example.com', $params['domain']);
    }

    /**
     * Run the middleware once and return the session cookie parameters it set
     *
     * @param SessionMiddleware $middleware The middleware
     * @param string            $url        The request URL
     *
     * @return array<string, mixed> The cookie parameters
     */
    private function cookieParamsAfterProcessing(SessionMiddleware $middleware, string $url): array
    {
        ini_set('session.cache_limiter', '');
        ini_set('session.save_path', sys_get_temp_dir());

        $factory = new Psr17Factory();
        $handler = $this->createStub(Handler::class);
        $handler->method('handle')->willReturn($factory->createResponse(200));

        $middleware->process($factory->createServerRequest('GET', $url), $handler);

        return session_get_cookie_params();
    }
}
