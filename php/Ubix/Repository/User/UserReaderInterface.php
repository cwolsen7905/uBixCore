<?php

declare(strict_types=1);

namespace Ubix\Repository\User;

use Exception;
use Ubix\DataType\Int\UserId;
use Ubix\DataType\String\Username;
use Ubix\Model\User;

/**
 * Interface UserReaderInterface
 *
 * Provides methods to read user related data.
 */
interface UserReaderInterface
{
    /**
     * Get User by ID
     *
     * @param UserId $userId The user ID
     *
     * @return User The user
     *
     * @throws Exception If the user is not found
     */
    public function getUserById(UserId $userId): User;

    /**
     * Get User by Username
     *
     * @param Username $username The username
     *
     * @return User The user
     *
     * @throws Exception If the user is not found
     */
    public function getUserByUsername(Username $username): User;
}
