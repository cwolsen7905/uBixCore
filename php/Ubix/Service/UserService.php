<?php

declare(strict_types=1);

namespace Ubix\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataType\Int\UserId;
use Ubix\DataType\String\DisplayName;
use Ubix\DataType\String\Email;
use Ubix\Model\User;
use Ubix\Repository\User\UserReaderInterface as UserReader;
use Ubix\Repository\User\UserWriterInterface as UserWriter;

/**
 * Service to read and write Sowing.me platform users
 *
 * @see \Ubix\Tests\Service\UserServiceTest PHPUnit test case
 */
final class UserService
{
    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param UserReader $userReader User reader
     * @param UserWriter $userWriter User writer
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private UserReader $userReader,
        private UserWriter $userWriter,
    ) {
    }

    /**
     * Get a user by ID
     *
     * @param UserId $userId The user ID
     *
     * @return User The user
     */
    public function getUserById(UserId $userId): User
    {
        return $this->userReader->getUserById($userId);
    }

    /**
     * Get a user by email address
     *
     * @param Email $email The email address
     *
     * @return User The user
     */
    public function getUserByEmail(Email $email): User
    {
        return $this->userReader->getUserByEmail($email);
    }

    /**
     * Check whether an email address is already registered
     *
     * @param Email $email The email address to check
     *
     * @return bool True if the email address exists
     */
    public function emailExists(Email $email): bool
    {
        return $this->userReader->emailExists($email);
    }

    /**
     * Check whether a display name is already taken
     *
     * @param DisplayName $displayName The display name to check
     *
     * @return bool True if the display name exists
     */
    public function displayNameExists(DisplayName $displayName): bool
    {
        return $this->userReader->displayNameExists($displayName);
    }

    /**
     * Create a new user
     *
     * @param User $user The user to create
     *
     * @return int The created user ID
     */
    public function createUser(User $user): int
    {
        $this->userWriter->createUser($user);

        return (int) $user->getId();
    }

    /**
     * Update an existing user
     *
     * @param User $user The user to update
     *
     * @return void
     */
    public function updateUser(User $user): void
    {
        $this->userWriter->updateUser($user);
    }
}
