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
 * The accepted tokens are supplied by the host application -- typically read from
 * uBixVault in the app's Dependencies.php. The framework ships NO tokens: a token
 * compiled into a package is a token published to everyone who installs it.
 *
 * Fails closed. With no tokens configured, every request is rejected rather than
 * every request being let through, so a missing secret cannot silently disable
 * authentication.
 *
 * @see \Ubix\Tests\Middleware\BearerTokenAuthenticationMiddlewareTest PHPUnit test case
 */
final class BearerTokenAuthenticationMiddleware implements Middleware
{
    /**
     * Constructor
     *
     * @param Logger             $logger       PSR-3 logger
     * @param array<int, string> $bearerTokens Accepted tokens, supplied by the host; empty rejects everything
     */
    public function __construct(
        private Logger $logger,
        private array $bearerTokens,
    ) {
    }

    /**
     * Allow the request through only when it carries one of the configured tokens
     *
     * @param Request $request PSR-7 request
     * @param Handler $handler Next handler
     *
     * @return Response
     *
     * @throws HttpForbiddenException When the token is missing, unknown, or none are configured
     */
    public function process(Request $request, Handler $handler): Response
    {
        $tokens = array_values(array_filter(
            $this->bearerTokens,
            static function (string $token): bool {
                return $token !== '';
            },
        ));

        if ($tokens === []) {
            $this->logger->error('Bearer token authentication has no tokens configured; rejecting request');
            throw new HttpForbiddenException($request, 'You must include a valid bearer token');
        }

        $presented = preg_match('/^Bearer\s+(\S+)$/i', $request->getHeaderLine('Authorization'), $matches) === 1 ? $matches[1] : null;

        if ($presented === null || !$this->isAccepted($presented, $tokens)) {
            // Never log the presented value: a near-miss token is still a secret.
            $this->logger->notice('Rejected request with a missing or invalid bearer token');
            throw new HttpForbiddenException($request, 'You must include a valid bearer token');
        }

        return $handler->handle($request);
    }

    /**
     * Constant-time membership check, so response timing does not leak token prefixes
     *
     * @param string             $presented Token from the request
     * @param array<int, string> $tokens    Configured tokens
     *
     * @return bool
     */
    private function isAccepted(string $presented, array $tokens): bool
    {
        $accepted = false;

        foreach ($tokens as $token) {
            // No early return: compare against every token either way.
            $accepted = hash_equals($token, $presented) || $accepted;
        }

        return $accepted;
    }
}
