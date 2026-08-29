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
     * @return void The created ID is set on the passed model
     *
     * @throws Exception If the token creation fails
     */
    public function createToken(EmailConfirmationToken $token): void;

    /**
     * Mark a token as used
     *
     * @param int $tokenId The token ID to mark as used
     *
     * @return void
     *
     * @throws Exception If the token update fails
     */
    public function markTokenAsUsed(int $tokenId): void;

    /**
     * Delete expired tokens
     *
     * @return void
     *
     * @throws Exception If the deletion fails
     */
    public function deleteExpiredTokens(): void;
}
