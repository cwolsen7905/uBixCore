<?php

declare(strict_types=1);

namespace Ubix\Repository\EmailConfirmationToken;

use Exception;
use Ubix\Model\EmailConfirmationToken;

/**
 * Interface EmailConfirmationTokenWriterInterface
 *
 * Provides methods to write email confirmation token related data.
 */
interface EmailConfirmationTokenWriterInterface
{
    /**
     * Create a new email confirmation token
     *
     * @param EmailConfirmationToken $token The token to create
     *
     * @return int The created token ID
     *
     * @throws Exception If the token creation fails
     */
    public function createToken(EmailConfirmationToken $token): int;

    /**
     * Mark a token as used
     *
     * @param int $tokenId The token ID to mark as used
     *
     * @return bool True if the update was successful
     *
     * @throws Exception If the token update fails
     */
    public function markTokenAsUsed(int $tokenId): bool;

    /**
     * Delete expired tokens
     *
     * @return int The number of deleted tokens
     *
     * @throws Exception If the deletion fails
     */
    public function deleteExpiredTokens(): int;
}
