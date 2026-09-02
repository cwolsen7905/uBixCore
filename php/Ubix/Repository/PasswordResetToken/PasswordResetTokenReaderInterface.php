<?php

declare(strict_types=1);

namespace Ubix\Repository\PasswordResetToken;

use Ubix\Model\PasswordResetToken;

/**
 * Reads password reset tokens
 */
interface PasswordResetTokenReaderInterface
{
    /**
     * Get a token by the SHA-256 hash of its raw value
     *
     * @param string $tokenHash The token hash
     *
     * @return ?PasswordResetToken The token, or null when unknown
     */
    public function getTokenByHash(string $tokenHash): ?PasswordResetToken;
}
