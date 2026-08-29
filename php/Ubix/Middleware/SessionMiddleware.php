<?php

declare(strict_types=1);

namespace Ubix\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;
use SessionHandlerInterface as SessionHandler;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Middleware to enable sessions
 *
 * @see \Ubix\Tests\Middleware\SessionMiddlewareTest PHPUnit test case
 */
final class SessionMiddleware implements Middleware
{
    /**
     * Constructor
     *
     * @param Logger         $logger         Logger
     * @param SessionHandler $sessionHandler Session handler
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SessionHandler $sessionHandler,
    ) {
    }

    /**
     * Process the request and enable sessions
     *
     * @param Request $request PSR request
     * @param Handler $handler PSR handler
     *
     * @throws Exception If a session has already been started
     *
     * @return Response PSR response
     */
    public function process(Request $request, Handler $handler): Response
    {
        //
        //  Ensure no session has been started yet
        //
        if (session_status() !== PHP_SESSION_NONE) {
            throw new Exception('A session has already started.', ExceptionCode::SESSION_ALREADY_STARTED->value);
        }

        // Get the root domain as wildcard for cookie sharing across subdomains
        $uri              = $request->getUri();
        $host             = $uri->getHost();
        $domainParts      = explode('.', $host);
        $domainPartsCount = count($domainParts);
        $domain           = $domainPartsCount >= 2 && !str_contains($host, '127.0.0.1') ? '.' . $domainParts[$domainPartsCount - 2] . '.' . $domainParts[$domainPartsCount - 1] : '';

        $serverParams = $request->getServerParams();
        $isSecure     = $uri->getScheme() === 'https'
        || ($serverParams['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        || in_array($serverParams['SERVER_PORT'] ?? null, [443, '443'], true);
        $sameSite     = $isSecure ? 'None' : 'Lax';
        // Invoke session_set_cookie_params() before session_set_save_handler() because the latter will invoke session_get_cookie_params() to get the $domain value
        session_set_cookie_params(
            [
                'domain'   => $domain,
                'httponly' => false,
                'lifetime' => 0,
                'path'     => '/',
                'samesite' => $sameSite, // TEMPORARY: because of the way we're serving the app in development
                'secure'   => $isSecure,
            ],
        );

        session_set_save_handler($this->sessionHandler, true);
        session_start();

        //
        //  Continue processing the request
        //
        return $handler->handle($request);
    }
}
