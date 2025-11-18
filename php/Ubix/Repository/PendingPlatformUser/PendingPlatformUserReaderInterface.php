<?php

declare(strict_types=1);

namespace Ubix\Repository\PendingPlatformUser;

use Ubix\Model\PendingPlatformUser;

/**
 * Interface for reading pending platform user data
 */
interface PendingPlatformUserReaderInterface
{
    /**
     * Get all pending platform users by login
     *
     * @param string $username The username
     *
     * @return PendingPlatformUser[] An array of pending platform user objects
     */
    public function getByUsername(string $username): array;
}
