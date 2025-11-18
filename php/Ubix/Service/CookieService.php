<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Cookie\CookieSameSite;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to interact with cookies
 *
 * Set a cookie example:
 * ```
 * $response = $cookieService->set(
 *     $response,
 *     'ExampleName',
 *     'Example value',
 *     (int)(new \DateTime())->modify('+1 days')->format('U'),
 *     $cookieService->getDefaultPath(),
 *     $cookieService->getDefaultDomain($request),
 *     $cookieService->getDefaultSecure(),
 *     $cookieService->getDefaultHttpOnly($request),
 * );
 * ```
 *
 * Delete a cookie example:
 * ```
 * $response = $cookieService->delete(
 *     $response,
 *     'ExampleCookieName',
 *     $cookieService->getDefaultPath(),
 *     $cookieService->getDefaultDomain($request),
 *     $cookieService->getDefaultSecure(),
 *     $cookieService->getDefaultHttpOnly($request),
 * );
 * ```
 *
 * @see \Ubix\Tests\Service\CookieServiceTest PHPUnit test case
 */
final class CookieService
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
     * Add a Set-Cookie header to a PSR response
     *
     * Example:
     * ```
     * $response = $cookieService->set(
     *     $response,
     *     'ExampleName',
     *     'Example value',
     *     (int)(new \DateTime())->modify('+1 days')->format('U'),
     *     $cookieService->getDefaultPath(),
     *     $cookieService->getDefaultDomain($request),
     *     $cookieService->getDefaultSecure(),
     *     $cookieService->getDefaultHttpOnly($request),
     * );
     * ```
     *
     * @param Response        $response PSR response
     * @param string          $name     The cookie's name
     * @param string          $value    The cookie's value
     * @param ?int            $expire   Unix timestamp when the cookie should expire (null = session cookie) (default: null)
     * @param string          $path     The cookie's path attribute (default: '/')
     * @param string          $domain   The cookie's domain attribute (default: '')
     * @param bool            $secure   The cookie's Secure flag (default: false)
     * @param bool            $httpOnly The cookie's HttpOnly flag (default: false)
     * @param ?CookieSameSite $sameSite The cookie's SameSite flag (default: null)
     *
     * @return Response PSR response
     */
    public function set(
        Response $response,
        string $name,
        string $value,
        ?int $expire = null,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = false,
        ?CookieSameSite $sameSite = null,
    ): Response {
        //
        //  Start with name=value (URL-encoded)
        //
        $cookie = urlencode($name) . '=' . urlencode($value);

        //
        //  Expiration
        //
        if ($expire !== null) {
            //
            //  PHP-date RFC 7231 format
            //
            $cookie .= '; Expires=' . gmdate(DateTime::RFC7231, $expire);

            //
            //  Max-Age is optional but recommended
            //
            $cookie .= '; Max-Age=' . max(0, $expire - (int)(new DateTime())->format('U'));
        }

        //
        //  Path and domain
        //
        $cookie .= '; Path=' . $path;
        if ($domain !== '') {
            $cookie .= '; Domain=' . $domain;
        }

        //
        //  Secure flag
        //
        if ($secure) {
            $cookie .= '; Secure';
        }

        //
        //  HttpOnly flag
        //
        if ($httpOnly) {
            $cookie .= '; HttpOnly';
        }

        //
        //  SameSite attribute
        //
        if ($sameSite !== null) {
            $cookie .= '; SameSite=' . $sameSite->value;
        }

        //
        //  Add the new Set-Cookie header to the PSR response
        //
        return $response->withAddedHeader('Set-Cookie', $cookie);
    }

    /**
     * Delete a cookie from a PSR response
     *
     * Example:
     * ```
     * $response = $cookieService->delete(
     *     $response,
     *     'ExampleCookieName',
     *     $cookieService->getDefaultPath(),
     *     $cookieService->getDefaultDomain($request),
     *     $cookieService->getDefaultSecure(),
     *     $cookieService->getDefaultHttpOnly($request),
     * );
     * ```
     *
     * @param Response        $response PSR response
     * @param string          $name     The cookie's name
     * @param string          $path     The cookie's path attribute (default: '/')
     * @param string          $domain   The cookie's domain attribute (default: '')
     * @param bool            $secure   The cookie's Secure flag (default: false)
     * @param bool            $httpOnly The cookie's HttpOnly flag (default: false)
     * @param ?CookieSameSite $sameSite The cookie's SameSite flag (default: null)
     *
     * @return Response PSR response
     */
    public function delete(
        Response $response,
        string $name,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = false,
        ?CookieSameSite $sameSite = null,
    ): Response {
        //
        //  A cookie is deleted by setting its expiration time to the past
        //
        return $this->set(
            response: $response,
            name:     $name,
            value:    'deleted',
            expire:   0,
            path:     $path,
            domain:   $domain,
            secure:   $secure,
            httpOnly: $httpOnly,
            sameSite: $sameSite,
        );
    }

    /**
     * Method to generate the domain for cookies
     *
     * @param Request $request PSR request
     *
     * @throws Exception If the domain cannot be determined
     *
     * @return string The domain for cookies
     */
    public function getDefaultDomain(Request $request): string
    {
        //
        //  First, check the X-Forwarded-Host header
        //
        $xForwardedHost = $request->getHeaderLine('X-Forwarded-Host');
        if ($xForwardedHost !== '') {
            return $xForwardedHost;
        }

        //
        //  Next, check the URI host
        //
        $host = $request->getUri()->getHost();
        if ($host !== '') {
            return $host;
        }

        //
        //  Finally, give up and throw an exception
        //
        throw new Exception('Could not determine cookie domain.', ExceptionCode::NO_COOKIE_DOMAIN_DETERMINED->value);
    }

    /**
     * Method to generate the HttpOnly flag for cookies
     *
     * @param Request $request PSR request
     *
     * @return bool Whether or not to use the HttpOnly flag for cookies
     */
    public function getDefaultHttpOnly(Request $request): bool
    {
        //
        //  First, check the X-Forwarded-Proto header
        //
        $xForwardedProto = $request->getHeaderLine('X-Forwarded-Proto');
        if ($xForwardedProto !== '') {
            return strtolower($xForwardedProto) !== 'https';
        }

        //
        //  Next, check the URI scheme
        //
        return $request->getUri()->getScheme() !== 'https';
    }

    /**
     * Method to generate the Secure flag for cookies
     *
     * @return bool Whether or not to use the Secure flag for cookies
     */
    public function getDefaultSecure(): bool // @phpstan-ignore return.tooWideBool (We are currently returning false every time as an application/framework decision but that amy change in the future so allow this method to return either true or false)
    {
        return false;
    }

    /**
     * Method to generate the path for cookies
     *
     * @return string The path for cookies
     */
    public function getDefaultPath(): string
    {
        return '/';
    }
}
