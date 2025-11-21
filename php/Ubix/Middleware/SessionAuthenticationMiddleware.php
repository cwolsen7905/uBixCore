<?php

declare(strict_types=1);

namespace Ubix\Middleware;

use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Middleware to authenticate requests using PHP session user data
 */
final class SessionAuthenticationMiddleware implements Middleware
{
    /**
     * Constructor
     *
     * @param ResponseFactory      $responseFactory Response factory for creating responses
     * @param array<array{method: string, path: string}> $excludedRoutes  Routes to exclude from authentication
     */
    public function __construct(
        private ResponseFactory $responseFactory,
        private array $excludedRoutes = [],
    ) {
    }

    /**
     * Process the request and check for session authentication
     *
     * @param Request $request PSR request
     * @param Handler $handler PSR handler
     *
     * @return Response PSR response
     */
    public function process(Request $request, Handler $handler): Response
    {
        // Check if route is excluded from authentication
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        foreach ($this->excludedRoutes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                return $handler->handle($request);
            }
        }

        if (!isset($_SESSION['user']['id'])) {
            $response = $this->responseFactory->createResponse(401);
            $response->getBody()->write(json_encode(['message' => 'Not Authenticated']));

            return $response->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}
