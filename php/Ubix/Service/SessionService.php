<?php

declare(strict_types=1);

namespace Ubix\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;

/**
 * Session operations that are easy to get subtly wrong
 *
 * {@see self::startAuthenticatedSession()} exists because every app reintroduces
 * session fixation the same way: it writes the signed-in user into
 * `$_SESSION` and keeps the session id the request arrived with. If an attacker
 * planted that id before sign-in (a link, a subdomain cookie, a shared
 * machine), they now hold an authenticated session. Rotating the id first, and
 * only then writing the user, closes it -- and putting both behind one call
 * makes the safe order the easy one.
 *
 * What goes in the payload is the host's business; this class only guarantees
 * the order.
 *
 * @see \Ubix\Tests\Service\SessionServiceTest PHPUnit test case
 */
final class SessionService
{
    /**
     * Constructor
     *
     * @param Logger $logger The Monolog logger
     */
    public function __construct(
        private Logger $logger,
    ) {
    }

    /**
     * Mark the current session as signed in: rotate the session id, then store the payload
     *
     * @param array<string, mixed> $payload What the host keeps about the signed-in user
     * @param string               $key     The `$_SESSION` key to store it under
     *
     * @throws DtoException When no session is active (the session middleware has not run)
     *
     * @return void
     */
    public function startAuthenticatedSession(array $payload, string $key = 'user'): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new DtoException('No active session to sign in to.', ExceptionCode::SESSION_NOT_STARTED->value);
        }

        // Rotate first; `true` deletes the old session's data server-side, so the
        // pre-sign-in id is worthless afterwards rather than a second way in.
        if (!session_regenerate_id(true)) {
            $this->logger->error('Session id could not be rotated at sign-in');

            throw new DtoException('Sign-in could not be completed. Please try again.', ExceptionCode::SESSION_NOT_STARTED->value);
        }

        $_SESSION[$key] = $payload;
    }
}
