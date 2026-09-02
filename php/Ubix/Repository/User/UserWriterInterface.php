<?php

declare(strict_types=1);

namespace Ubix\Repository\User;

use Exception;
use Ubix\Model\User;

/**
 * Interface UserWriterInterface
 *
 * Provides methods to write user related data.
 */
interface UserWriterInterface
{
    /**
     * Create a new user
     *
     * @param User $user The user to create
     *
     * @return void The created ID is set on the passed model
     *
     * @throws Exception If the user creation fails
     */
    public function createUser(User $user): void;

    /**
     * Update an existing user
     *
     * @param User $user The user to update
     *
     * @return void
     *
     * @throws Exception If the user update fails
     */
    public function updateUser(User $user): void;

    /**
     * Record a failed login: increment the attempt counter, stamp the time
     *
     * @param int $userId The user id
     *
     * @return void
     */
    public function recordFailedLogin(int $userId): void;

    /**
     * Record a successful login: reset the attempt counter, stamp last_login
     *
     * @param int $userId The user id
     *
     * @return void
     */
    public function recordSuccessfulLogin(int $userId): void;

    /**
     * Replace a user's password hash
     *
     * @param int    $userId       The user id
     * @param string $passwordHash The new password hash
     *
     * @return void
     */
    public function updatePasswordHash(int $userId, string $passwordHash): void;
}
