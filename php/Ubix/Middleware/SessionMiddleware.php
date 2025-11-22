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

        //
        //  Determine session settings, call PHP's session handling functions then start the session
        //
        /**
         * @var non-empty-string $requestHost
         */
        $requestHost = $request->getServerParams()['HTTP_HOST'] ?? 'localhost';

        $domain = getenv('IS_SANDBOX') !== 'true' && getenv('IS_DEV') !== 'true' && getenv('IS_STAGING') !== 'true' && preg_match('/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/', $requestHost, $regex) ? $regex[0] : '';
        $path   = '/';// getenv('IS_SANDBOX') === 'true' ? '/' : '/; SameSite=None';
        $secure = true;// getenv('IS_SANDBOX') !== 'true';


		$isSecure = (getenv('HTTPS') !== false && getenv('HTTPS') !== 'off') || (getenv('HTTP_X_FORWARDED_PROTO') === 'https');


		$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
		$domain = '.ubixsys.com';

		$this->logger->debug('Session cookie parameters', 			[
				'lifetime' => 0,
				'path'     => $path,
				'domain'   => $domain,
				'secure'   => $isSecure,
				'httponly' => false,
				'samesite' => 'None', // TEMPORARY: because of the way we're serving the app in development
				'HTTPS'	 => $_SERVER['HTTPS'] ?? 'not set',
			]);

		// Invoke session_set_cookie_params() before session_set_save_handler() because the latter will invoke session_get_cookie_params() to get the $domain value
        session_set_cookie_params(
			[
				'lifetime' => 0,
				'path'     => $path,
				'domain'   => $domain,
				'secure'   => $isSecure,
				'httponly' => false,
				'samesite' => 'None', // TEMPORARY: because of the way we're serving the app in development
			]
		);
        session_set_save_handler($this->sessionHandler, true);
        session_start();

        //
        //  Continue processing the request
        //
        return $handler->handle($request);
    }
}
