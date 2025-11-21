<?php

declare(strict_types=1);

namespace Ubix\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Middleware to handle CORS headers for approved origins
 */
final class CorsMiddleware implements Middleware
{
    /**
     * Constructor
     *
     * @param array<string> $allowedOrigins  List of allowed origins
     * @param array<string> $allowedMethods  List of allowed HTTP methods
     * @param array<string> $allowedHeaders  List of allowed headers
     * @param bool          $allowCredentials Whether to allow credentials
     */
    public function __construct(
        private array $allowedOrigins = [],
        private array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        private array $allowedHeaders = ['Content-Type', 'Authorization', 'X-Requested-With'],
        private bool $allowCredentials = true,
    ) {
    }

    /**
     * Process the request and add CORS headers if origin is allowed
     *
     * @param Request $request PSR request
     * @param Handler $handler PSR handler
     *
     * @return Response PSR response
     */
    public function process(Request $request, Handler $handler): Response
    {
        $origin = $request->getHeaderLine('Origin');
        $response = $handler->handle($request);

        if ($origin === '' || !$this->isOriginAllowed($origin)) {
            return $response;
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods))
            ->withHeader('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders))
            ->withHeader('Access-Control-Allow-Credentials', $this->allowCredentials ? 'true' : 'false');
    }

    /**
     * Check if the origin is in the allowed list
     *
     * @param string $origin The origin to check
     *
     * @return bool True if origin is allowped
     */
    private function isOriginAllowed(string $origin): bool
    {
        // Parse host from origin URL
        $parsedUrl = parse_url($origin);
        $host = $parsedUrl['host'] ?? '';

        if ($host === '') {
            return false;
        }

        foreach ($this->allowedOrigins as $allowed) {
            // Wildcard match (e.g., .ubixsys.com matches any subdomain)
            if (str_starts_with($allowed, '.')) {
                if ($host === substr($allowed, 1) || str_ends_with($host, $allowed)) {
                    return true;
                }
            } elseif ($host === $allowed) {
                // Exact host match
                return true;
            }
        }

        return false;
    }
}
