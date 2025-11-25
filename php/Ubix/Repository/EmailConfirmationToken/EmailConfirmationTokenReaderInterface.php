<?php

declare(strict_types=1);

namespace Ubix\Repository\EmailConfirmationToken;

use Exception;
use Ubix\Model\EmailConfirmationToken;

/**
 * Interface EmailConfirmationTokenReaderInterface
 *
 * Provides methods to read email confirmation token related data.
 */
interface EmailConfirmationTokenReaderInterface
{
    /**
     * Get EmailConfirmationToken by token string
     *
     * @param string $token The confirmation token
     *
     * @return EmailConfirmationToken|null The token or null if not found
     *
     * @throws Exception If the query fails
     */
    public function getTokenByString(string $token): ?EmailConfirmationToken;

    /**
     * Get active EmailConfirmationToken for a user
     *
     * @param int $userId The user ID
     *
     * @return EmailConfirmationToken|null The token or null if not found
     *
     * @throws Exception If the query fails
     */
    public function getActiveTokenForUser(int $userId): ?EmailConfirmationToken;
}
