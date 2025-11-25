<?php

declare(strict_types=1);

namespace Ubix\Repository\User;

use Exception;
use Ubix\DataType\Int\UserId;
use Ubix\DataType\String\Email;
use Ubix\DataType\String\DisplayName;
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
     * Get User by Email
     *
     * @param Email $email The email
     *
     * @return User The user
     *
     * @throws Exception If the user is not found
     */
    public function getUserByEmail(Email $email): User;

	/**
     * Check if email already exists
     *
     * @param Email $email The email to check
     *
     * @return bool True if email exists
     */
    public function emailExists(Email $email): bool;

    /**
     * Check if display name already exists
     *
     * @param DisplayNName $displayName The display name to check
     *
     * @return bool True if display name exists
     */
    public function displayNameExists(DisplayName $displayName): bool;
}
