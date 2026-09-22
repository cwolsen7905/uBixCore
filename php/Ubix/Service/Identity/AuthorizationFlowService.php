<?php

declare(strict_types=1);

namespace Ubix\Service\Identity;

use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Ubix\DataTransferObject\Identity\CallbackParameters;
use Ubix\DataTransferObject\Identity\PendingAuthorization;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Identity\IdentityProvider;
use Ubix\Exception\DtoException;

/**
 * Holds in-flight sign-ins server-side and binds each one to the browser that started it
 *
 * A flow is stored in cache under a random handle for ten minutes and handed out
 * **once**. The handle travels in its own cookie, not in the host's PHP session,
 * on purpose: Sign in with Apple returns with a cross-site POST, and browsers do
 * not send a `SameSite=Lax` cookie on a cross-site POST. A flow kept in a Lax
 * session is invisible on Apple's callback, and relaxing the session cookie to
 * `None` to "fix" that weakens every other request. This cookie carries nothing
 * but a handle, lives ten minutes, and is `SameSite=None; Secure; HttpOnly`
 * with the `__Host-` prefix, so it can be neither set by a subdomain nor read
 * by script.
 *
 * {@see self::takeForCallback()} is the whole security check of the round trip:
 * without the cookie from the starting browser, and a `state` equal to the one
 * issued, there is no flow -- never a fallback.
 *
 * @see \Ubix\Tests\Service\Identity\AuthorizationFlowServiceTest PHPUnit test case
 */
final class AuthorizationFlowService
{
    public const COOKIE_NAME = '__Host-ubix_authflow';

    private const CACHE_KEY_PREFIX = 'UBIX_IDENTITY_FLOW_';
    private const HANDLE_PATTERN   = '/^[a-f0-9]{64}$/';
    private const MAX_HOST_CONTEXT = 1024;
    private const TTL_SECONDS      = 600;

    /**
     * Constructor
     *
     * @param Logger      $logger The Monolog logger
     * @param SimpleCache $cache  A PSR-16 cache shared by every pod that can receive a callback
     */
    public function __construct(
        private Logger $logger,
        private SimpleCache $cache,
    ) {
    }

    /**
     * Store a flow and return the handle to put in the flow cookie
     *
     * @param PendingAuthorization $pending The flow returned by `beginAuthorization()`
     *
     * @return string The handle (64 hex characters)
     *
     * @throws DtoException When the host context is too large or the cache refuses the write
     */
    public function store(PendingAuthorization $pending): string
    {
        if (strlen($pending->hostContext) > self::MAX_HOST_CONTEXT) {
            throw new DtoException('Sign-in could not be started', ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        $handle = bin2hex(random_bytes(32));

        $stored = $this->cache->set(self::CACHE_KEY_PREFIX . $handle, [
            'authorizationUrl'       => $pending->authorizationUrl,
            'codeVerifier'           => $pending->codeVerifier,
            'createdAtUnixTimestamp' => $pending->createdAtUnixTimestamp,
            'hostContext'            => $pending->hostContext,
            'nonce'                  => $pending->nonce,
            'provider'               => $pending->provider->value,
            'redirectUri'            => $pending->redirectUri,
            'returnTo'               => $pending->returnTo,
            'state'                  => $pending->state,
        ], self::TTL_SECONDS);

        if (!$stored) {
            throw new DtoException('Sign-in could not be started', ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        return $handle;
    }

    /**
     * Take the flow a callback belongs to, exactly once
     *
     * @param string             $cookieHandle The value of the {@see self::COOKIE_NAME} cookie, or empty when absent
     * @param CallbackParameters $parameters   What the provider sent back
     *
     * @return PendingAuthorization The flow, now removed from the store
     *
     * @throws DtoException When there is no cookie, the flow is unknown, expired or already used, or `state` does not match
     */
    public function takeForCallback(string $cookieHandle, CallbackParameters $parameters): PendingAuthorization
    {
        if (preg_match(self::HANDLE_PATTERN, $cookieHandle) !== 1) {
            $this->logger->info('Sign-in callback without a valid flow cookie');

            throw new DtoException('That sign-in didn\'t complete. Please try again.', ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        $key    = self::CACHE_KEY_PREFIX . $cookieHandle;
        $stored = $this->cache->get($key);

        // Single use: gone whatever happens next, so a replayed callback finds nothing.
        $this->cache->delete($key);

        if (!is_array($stored)) {
            $this->logger->info('Sign-in callback for an unknown, expired or used flow', ['handle' => substr($cookieHandle, 0, 8)]);

            throw new DtoException('That sign-in didn\'t complete. Please try again.', ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        $pending = new PendingAuthorization(
            provider:               IdentityProvider::from($this->getString($stored, 'provider')),
            authorizationUrl:       $this->getString($stored, 'authorizationUrl'),
            state:                  $this->getString($stored, 'state'),
            nonce:                  $this->getString($stored, 'nonce'),
            codeVerifier:           $this->getString($stored, 'codeVerifier'),
            redirectUri:            $this->getString($stored, 'redirectUri'),
            returnTo:               $this->getString($stored, 'returnTo'),
            hostContext:            $this->getString($stored, 'hostContext'),
            createdAtUnixTimestamp: is_int($stored['createdAtUnixTimestamp'] ?? null) ? $stored['createdAtUnixTimestamp'] : 0,
        );

        if ($pending->state === '' || !hash_equals($pending->state, $parameters->state)) {
            $this->logger->warning('Sign-in callback state mismatch', ['provider' => $pending->provider->value]);

            throw new DtoException('That sign-in didn\'t complete. Please try again.', ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        return $pending;
    }

    /**
     * Get the `Set-Cookie` header value that hands the flow handle to the browser
     *
     * @param string $handle The handle returned by {@see self::store()}
     *
     * @return string The header value
     */
    public function getCookieHeader(string $handle): string
    {
        return self::COOKIE_NAME . '=' . $handle . '; Path=/; Max-Age=' . self::TTL_SECONDS . '; Secure; HttpOnly; SameSite=None';
    }

    /**
     * Get the `Set-Cookie` header value that removes the flow cookie once the callback is done
     *
     * @return string The header value
     */
    public function getExpiredCookieHeader(): string
    {
        return self::COOKIE_NAME . '=; Path=/; Max-Age=0; Secure; HttpOnly; SameSite=None';
    }

    /**
     * Get a string field from a stored flow
     *
     * @param array<mixed> $stored The stored flow
     * @param string       $field  The field
     *
     * @return string The value, or empty when absent or not a string
     */
    private function getString(array $stored, string $field): string
    {
        return is_string($stored[$field] ?? null) ? $stored[$field] : '';
    }
}
