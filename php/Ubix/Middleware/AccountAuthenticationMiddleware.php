<?php

declare(strict_types=1);

namespace Ubix\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\AccountAuthenticationService;

/**
 * Middleware to authenticate requests with accounts stored in the database using cookies/session
 *
 * @see \Ubix\Tests\Middleware\AccountAuthenticationMiddlewareTest PHPUnit test case
 */
final class AccountAuthenticationMiddleware implements Middleware
{
    /**
     * Constructor
     *
     * @param Logger                       $logger                       Logger
     * @param AccountAuthenticationService $accountAuthenticationService Account authentication service
     * @param bool                         $performersEnabled            Whether or not to enable performer account authentication (default: false)
     * @param bool                         $platformUsersEnabled         Whether or not to enable platform user account authentication (default: false)
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private AccountAuthenticationService $accountAuthenticationService,
        private bool $performersEnabled = false,
        private bool $platformUsersEnabled = false,
    ) {
    }

    /**
     * Process the request and handle slugs when routing fails
     *
     * @param Request $request PSR request
     * @param Handler $handler PSR handler
     *
     * @return Response PSR response
     */
    public function process(Request $request, Handler $handler): Response
    {
        //
        //  Use the account authentication service to add the logged in account to the request
        //
        $request = $request->withAttribute(
            'logged_in_account',
            $this->accountAuthenticationService->getLoggedInAccount($request, $this->performersEnabled, $this->platformUsersEnabled),
        );

        return $handler->handle($request);
    }
}
