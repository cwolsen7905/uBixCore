<?php

declare(strict_types=1);

namespace Ubix\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;

/**
 * Middleware to normalize the request's IP Address
 *
 * @see \Ubix\Tests\Middleware\NormalizedIpAddressMiddlewareTest PHPUnit test case
 */
final class NormalizedIpAddressMiddleware implements Middleware
{
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
     * Process the request and add a `normalizedIpAddress` attribute
     *
     * @param Request $request PSR request
     * @param Handler $handler PSR handler
     *
     * @return Response PSR response
     */
    public function process(Request $request, Handler $handler): Response
    {
        $request = $request->withAttribute('normalizedIpAddress', $this->getRequestIpAddress($request));

        //
        //  Continue processing the request
        //
        return $handler->handle($request);
    }

    /**
     * Get a PSR request's IP address
     *
     * @param Request $request PSR request
     *
     * @return string The request's IP address or null if one can't be found
     */
    private function getRequestIpAddress(Request $request): ?string
    {
        //
        //  First look for an IP address in the X-Forwarded-For header
        //
        if ($request->hasHeader('X-Forwarded-For')) {
            $xForwardedFor = $request->getHeaderLine('X-Forwarded-For');
            $xForwardedFor = array_map('trim', explode(',', $xForwardedFor));
            foreach ($xForwardedFor as $potentialIpAddress) {
                if (filter_var($potentialIpAddress, FILTER_VALIDATE_IP)) {
                    return $potentialIpAddress;
                }
            }
        }

        //
        //  Then fall back to the REMOTE_ADDR value and return null if it's not found or not a valid IP address
        //
        /**
         * @var array{
         *     REMOTE_ADDR?: ?string
         * } $serverParams
         */
        $serverParams = $request->getServerParams();

        return filter_var($serverParams['REMOTE_ADDR'] ?? null, FILTER_VALIDATE_IP) ? $serverParams['REMOTE_ADDR'] : null; // NOT_IMPLEMENTED: should we throw an exception rather than setting a null value if no IP address is found? (question for Chris)
    }
}
