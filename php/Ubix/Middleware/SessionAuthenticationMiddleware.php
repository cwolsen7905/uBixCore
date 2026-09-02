<?php

declare(strict_types=1);

namespace Ubix\Middleware;

use Exception;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataType\Int\UserId;
use Ubix\Service\JsonService;
use Ubix\Service\UserService;

/**
 * Middleware to authenticate requests using PHP session user data
 *
 * @see \Ubix\Tests\Middleware\SessionAuthenticationMiddlewareTest PHPUnit test case
 */
final class SessionAuthenticationMiddleware implements Middleware
{
    /**
     * Constructor
     *
     * @param Logger                                     $logger          Logger
     * @param ResponseFactory                            $responseFactory Response factory for creating responses
     * @param JsonService                                $jsonService     JSON encoder for error bodies
     * @param UserService                                $userService     Re-checks account status per request (FR-52)
     * @param array<array{method: string, path: string}> $excludedRoutes  Routes to exclude from authentication (a trailing '*' on a path matches by prefix)
     */
    public function __construct(
        private Logger $logger,
        private ResponseFactory $responseFactory,
        private JsonService $jsonService,
        private UserService $userService,
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
        $path   = $request->getUri()->getPath();

        foreach ($this->excludedRoutes as $route) {
            $excludedPath = $route['path'];
            $pathMatches  = str_ends_with($excludedPath, '*') ? str_starts_with($path, substr($excludedPath, 0, -1)) : $excludedPath === $path;
            if ($route['method'] === $method && $pathMatches) {
                return $handler->handle($request);
            }
        }

        $sessionUser = $_SESSION['user'] ?? null;
        if (!is_array($sessionUser) || !isset($sessionUser['id'])) {
            return $this->unauthenticated();
        }

        // Status re-check (FR-52): a suspended/deactivated account loses its
        // session on the next request, not only at the next login.
        $sessionUserId = $sessionUser['id'];
        assert(is_int($sessionUserId) || is_string($sessionUserId));

        try {
            $user = $this->userService->getUserById(new UserId((int) $sessionUserId));
        } catch (Exception $e) {
            $this->logger->info('Session status re-check could not load the user', ['error' => $e->getMessage()]);
            $user = null;
        }

        if ($user === null || $user->getStatus()?->value !== 'active') {
            $_SESSION = [];
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }
            $this->logger->info('Session terminated: account no longer active', [
                'user_id' => $sessionUser['id'],
            ]);
            return $this->unauthenticated();
        }

        return $handler->handle($request);
    }

    /**
     * Build the standard 401 response
     *
     * @return Response The 401 JSON response
     */
    private function unauthenticated(): Response
    {
        $response = $this->responseFactory->createResponse(401);
        $response->getBody()->write($this->jsonService->encode(['message' => 'Not Authenticated']));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
