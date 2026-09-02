<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\LockoutPolicy;
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
     * @param Logger        $logger        Logger
     * @param UserReader    $userReader    User reader
     * @param UserWriter    $userWriter    User writer
     * @param LockoutPolicy $lockoutPolicy Lockout thresholds (env-driven, FR-23)
     */
    public function __construct(
        private Logger $logger,
        private UserReader $userReader,
        private UserWriter $userWriter,
        private LockoutPolicy $lockoutPolicy = new LockoutPolicy(),
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
     * Whether the account is inside its lockout window (FR-21)
     *
     * @param User $user The user being authenticated
     *
     * @return bool True while locked out
     */
    public function isLockedOut(User $user): bool
    {
        if (($user->getFailedLoginAttempts() ?? 0) < $this->lockoutPolicy->threshold) {
            return false;
        }

        $lastFailed = $user->getLastFailedLogin();
        if ($lastFailed === null) {
            return false;
        }

        $windowEnd = (new DateTime($lastFailed->format('Y-m-d H:i:s')))
            ->modify('+' . $this->lockoutPolicy->minutes . ' minutes');

        return $windowEnd > new DateTime();
    }

    /**
     * Record a failed login attempt (FR-20)
     *
     * @param User $user The user whose attempt failed
     *
     * @return void
     */
    public function recordFailedLogin(User $user): void
    {
        $userId = $user->getId();
        assert($userId !== null);

        $this->userWriter->recordFailedLogin($userId);
        $this->logger->warning('Failed login recorded', [
            'attempts' => ($user->getFailedLoginAttempts() ?? 0) + 1,
            'user_id'  => $userId,
        ]);
    }

    /**
     * Record a successful login: reset the counter, stamp last_login (FR-20)
     *
     * @param User $user The user who logged in
     *
     * @return void
     */
    public function recordSuccessfulLogin(User $user): void
    {
        $userId = $user->getId();
        assert($userId !== null);

        $this->userWriter->recordSuccessfulLogin($userId);
    }

    /**
     * Replace a user's password and clear the lockout counter (FR-32)
     *
     * @param int    $userId      The user id
     * @param string $newPassword The new plaintext password (hashed here)
     *
     * @return void
     */
    public function changePassword(int $userId, string $newPassword): void
    {
        $this->userWriter->updatePasswordHash($userId, password_hash($newPassword, PASSWORD_DEFAULT));
        $this->userWriter->recordSuccessfulLogin($userId);
        $this->logger->info('Password changed', ['user_id' => $userId]);
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
