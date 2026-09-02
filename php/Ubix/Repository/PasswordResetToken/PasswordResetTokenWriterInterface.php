<?php

declare(strict_types=1);

namespace Ubix\Repository\PasswordResetToken;

use Ubix\Model\PasswordResetToken;

/**
 * Writes password reset tokens
 */
interface PasswordResetTokenWriterInterface
{
    /**
     * Persist a new token (the new id is set on the model)
     *
     * @param PasswordResetToken $token The token to create
     *
     * @return void
     */
    public function createToken(PasswordResetToken $token): void;

    /**
     * Mark one token as used
     *
     * @param int $tokenId The token id
     *
     * @return void
     */
    public function markTokenAsUsed(int $tokenId): void;

    /**
     * Mark every outstanding (unused, unexpired) token for a user as used
     *
     * @param int $userId The user id
     *
     * @return void
     */
    public function invalidateOutstandingTokensForUser(int $userId): void;
}
