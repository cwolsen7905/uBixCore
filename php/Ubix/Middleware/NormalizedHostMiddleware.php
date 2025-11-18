<?php

declare(strict_types=1);

namespace Ubix\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;

/**
 * Middleware to normalize the request's hostname
 *
 * @see \Ubix\Tests\Middleware\NormalizedHostMiddlewareTest PHPUnit test case
 */
final class NormalizedHostMiddleware implements Middleware
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
     * Process the request and add a `normalizedHost` attribute
     *
     * @param Request $request PSR request
     * @param Handler $handler PSR handler
     *
     * @return Response PSR response
     */
    public function process(Request $request, Handler $handler): Response
    {
        $xForwardedHost = $request->getHeaderLine('X-Forwarded-Host');

        $request = $request->withAttribute(
            'normalizedHost',
            $this->normalizeHost($xForwardedHost !== '' ? $xForwardedHost : $request->getUri()->getHost()),
        );

        //
        //  Continue processing the request
        //
        return $handler->handle($request);
    }

    /**
     * Normalize a host
     *
     * @param string $host The host to normalize
     *
     * @return string The normalized host
     */
    private function normalizeHost(string $host): string
    {
        if (preg_match('/^(?P<subdomain>[a-zA-Z0-9-]+)\.(sandbox|dev|staging|lan)\.vsmedia\.net$/', $host, $matches)) { // NOT_IMPLEMENTED: We can probably simplify this code once projectneptune.app is rolled out and we stop using vsmedia.net
            switch ($matches['subdomain']) {
                case 'fanclub-model': // This git repo should have been named model-flirt-fans
                    return 'model.flirt.fans';

                case 'fanclub-user': // This git repo should have been named flirt-fans
                    return 'flirt.fans';

                default:
                    //
                    //  In our encoded host format periods are represented by a dash and dashes are represented by two dashes, e.g. "example-host.com" would be encoded as "example--host-com"
                    //
                    $encodedHost = $matches['subdomain'];
                    $encodedHost = str_replace('--', '#', $encodedHost);
                    $encodedHost = str_replace('-', '.', $encodedHost);
                    return str_replace('#', '-', $encodedHost);
            }
        }

        return $host;
    }
}
