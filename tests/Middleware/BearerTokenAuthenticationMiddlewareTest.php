<?php

declare(strict_types=1);

namespace Ubix\Tests\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpForbiddenException;
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
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(BearerTokenAuthenticationMiddleware::class);
    }

    /**
     * A configured token is accepted; anything else is rejected
     *
     * @return void
     */
    public function testAcceptsOnlyConfiguredTokens(): void
    {
        $middleware = new BearerTokenAuthenticationMiddleware($this->createStub(Logger::class), ['token-one', 'token-two']);

        $this->assertSame(200, $middleware->process($this->request('Bearer token-two'), $this->okHandler())->getStatusCode());
        $this->assertSame(200, $middleware->process($this->request('bearer token-one'), $this->okHandler())->getStatusCode());

        foreach (['', 'Bearer', 'Bearer ', 'Bearer token-three', 'Bearer token-one extra', 'Basic token-one', 'token-one'] as $header) {
            $this->assertRejected($middleware, $header);
        }
    }

    /**
     * With no tokens configured the middleware fails closed, even for an empty presented token
     *
     * @return void
     */
    public function testFailsClosedWhenNoTokensAreConfigured(): void
    {
        foreach ([[], ['']] as $tokens) {
            $middleware = new BearerTokenAuthenticationMiddleware($this->createStub(Logger::class), $tokens);

            $this->assertRejected($middleware, 'Bearer anything');
            $this->assertRejected($middleware, 'Bearer ');
        }
    }

    /**
     * The framework ships no tokens of its own
     *
     * @return void
     */
    public function testShipsNoTokens(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../php/Ubix/Middleware/BearerTokenAuthenticationMiddleware.php');

        $this->assertDoesNotMatchRegularExpression('/[\'"][0-9a-f]{24,}[\'"]/i', $source, 'Middleware source must not contain a hard-coded token');
    }

    /**
     * Build a request with the given Authorization header
     *
     * @param string $authorization Header value; empty for no header
     *
     * @return Request
     */
    private function request(string $authorization): Request
    {
        $request = (new Psr17Factory())->createServerRequest('GET', '/protected');

        return $authorization === '' ? $request : $request->withHeader('Authorization', $authorization);
    }

    /**
     * A handler that answers 200
     *
     * @return Handler
     */
    private function okHandler(): Handler
    {
        $handler = $this->createStub(Handler::class);
        $handler->method('handle')->willReturn((new Psr17Factory())->createResponse(200));

        return $handler;
    }

    /**
     * Assert the middleware rejects the given header with a 403
     *
     * @param BearerTokenAuthenticationMiddleware $middleware Middleware under test
     * @param string                              $header     Authorization header value
     *
     * @return void
     */
    private function assertRejected(BearerTokenAuthenticationMiddleware $middleware, string $header): void
    {
        try {
            $middleware->process($this->request($header), $this->okHandler());
            $this->fail('Expected rejection for Authorization: ' . $header);
        } catch (HttpForbiddenException $exception) {
            $this->assertSame('You must include a valid bearer token', $exception->getMessage());
        }
    }
}
