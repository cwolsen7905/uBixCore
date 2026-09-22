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
 * The session cookie is always `HttpOnly`: the session id is a bearer credential,
 * and a cookie script can read turns any XSS bug into account takeover. No host
 * needs it from JavaScript -- a browser sends it on `fetch(..., {credentials})`
 * and a server-rendered frontend forwards the request's `Cookie` header.
 *
 * `SameSite` defaults to `Lax`, which still carries the cookie between sibling
 * subdomains of one site (`app.example.com` -> `api.example.com`) and on
 * top-level navigations, but not on cross-site POSTs -- the CSRF vector. A host
 * that genuinely serves its frontend from another site can pass `None`; it is
 * honoured only over HTTPS, since browsers reject `None` without `Secure`.
 *
 * The cookie domain defaults to the request host's last two labels
 * (`.example.com`), so every subdomain shares the session. That is broad on a
 * shared parent domain -- on `*.dev.example.com` it would reach every sibling
 * service -- so a host can pass an explicit domain (`.dev.example.com`), or an
 * empty string for a host-only cookie.
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
     * @param string         $sameSite       One of `Lax` (default), `Strict`, or `None` (HTTPS only; otherwise `Lax`)
     * @param ?string        $cookieDomain   Explicit cookie domain, `''` for host-only, or null for the request host's registrable domain
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private SessionHandler $sessionHandler,
        private string $sameSite = 'Lax',
        private ?string $cookieDomain = null,
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
        $domain           = $this->cookieDomain ?? ($domainPartsCount >= 2 && !str_contains($host, '127.0.0.1') ? '.' . $domainParts[$domainPartsCount - 2] . '.' . $domainParts[$domainPartsCount - 1] : '');

        $serverParams = $request->getServerParams();
        $isSecure     = $uri->getScheme() === 'https'
        || ($serverParams['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        || in_array($serverParams['SERVER_PORT'] ?? null, [443, '443'], true);
        $sameSite     = in_array($this->sameSite, ['Lax', 'Strict', 'None'], true) ? $this->sameSite : 'Lax';
        if ($sameSite === 'None' && !$isSecure) {
            $sameSite = 'Lax';
        }

        // Invoke session_set_cookie_params() before session_set_save_handler() because the latter will invoke session_get_cookie_params() to get the $domain value
        session_set_cookie_params(
            [
                'domain'   => $domain,
                'httponly' => true,
                'lifetime' => 0,
                'path'     => '/',
                'samesite' => $sameSite,
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
