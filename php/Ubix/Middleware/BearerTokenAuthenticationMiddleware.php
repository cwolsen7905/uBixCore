<?php

declare(strict_types=1);

namespace Ubix\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpForbiddenException;

/**
 * Middleware to authenticate requests with bearer tokens
 *
 * @see \Ubix\Tests\Middleware\BearerTokenAuthenticationMiddlewareTest PHPUnit test case
 */
final class BearerTokenAuthenticationMiddleware implements Middleware
{
    private const BEARER_TOKENS = [ // TEMPORARY: Bearer tokens should be moved into MySQL and out of the source code
        'a8f3c9d7e2b6f1a4c5e8d9b0f7a3c6e1', // The flirt.fans Python website managed by Miro
    ];

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Process the request
     *
     * @param Request $request PSR request
     * @param Handler $handler PSR handler
     *
     * @throws HttpForbiddenException If the bearer token is missing or invalid
     *
     * @return Response PSR response
     */
    public function process(Request $request, Handler $handler): Response
    {
        //
        //  Get the bearer token from the request's Authorization header
        //
        $bearerToken = preg_match('/^Bearer\s+(.+)$/i', $request->getHeaderLine('Authorization'), $matches) ? $matches[1] : null;

        //
        //  If the bearer token is missing or is invalid throw an HTTP forbidden exception
        //
        if (!in_array($bearerToken, self::BEARER_TOKENS, true)) {
            throw new HttpForbiddenException($request, 'You must include a valid bearer token');
        }

        //
        //  The bearer token is valid so we can continue processing the request
        //
        return $handler->handle($request);
    }
}
