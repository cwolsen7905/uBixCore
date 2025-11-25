<?php

declare(strict_types=1);

namespace Ubix\Repository\User;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataType\Int\UserId;
use Ubix\DataType\String\Email;
use Ubix\DataType\String\DisplayName;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\User\UserStatus;
use Ubix\Model\User;
use Ubix\Repository\User\UserReaderInterface as UserReader;
use Ubix\Repository\User\UserWriterInterface as UserWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Class UserSqlRepository
 *
 * Implements methods to read and write user-related data from the database.
 */
final class UserSqlRepository implements UserReader, UserWriter
{
    /**
     * UserSqlRepository constructor.
     *
     * @param Logger     $logger     The logger interface
     * @param SqlService $sqlService The SQL service for database interactions
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getUserById(UserId $userId): User
    {
        $sql = 'SELECT
                    id,
                    display_name,
                    password_hash,
                    email,
                    first_name,
                    last_name,
                    status,
                    roles,
                    failed_login_attempts,
                    last_failed_login,
                    last_login,
                    created_at,
                    updated_at
                FROM sowingme.users
                WHERE id = :id
                LIMIT 1';

        $result = $this->sqlService->getRow($sql, ['id' => $userId->value]);

        if ($result === null) {
            throw new Exception('User not found', ExceptionCode::USER_NOT_FOUND->value);
        }

        return $this->hydrateUser($result);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserByEmail(Email $email): User
    {
        $sql = 'SELECT
                    id,
                    display_name,
                    password_hash,
                    email,
                    first_name,
                    last_name,
                    status,
                    roles,
                    failed_login_attempts,
                    last_failed_login,
                    last_login,
                    created_at,
                    updated_at
                FROM sowingme.users
                WHERE email = :email
                LIMIT 1';

        $result = $this->sqlService->getRow($sql, ['email' => $email->value]);

        if ($result === null || empty($result)) {
            throw new Exception('User not found', ExceptionCode::USER_NOT_FOUND->value);
        }

        return $this->hydrateUser($result);
    }

    /**
     * {@inheritDoc}
     */
    public function createUser(User $user): int
    {
        $sql = 'INSERT INTO sowingme.users (
                    display_name,
                    password_hash,
                    email,
                    first_name,
                    last_name,
                    status,
                    roles,
                    created_at,
                    updated_at
                ) VALUES (
                    :display_name,
                    :password_hash,
                    :email,
                    :first_name,
                    :last_name,
                    :status,
                    :roles,
                    NOW(),
                    NOW()
                )';

        $params = [
            'display_name'  => $user->getDisplayName(),
            'password_hash' => $user->getPasswordHash(),
            'email'         => $user->getEmail(),
            'first_name'    => $user->getFirstName(),
            'last_name'     => $user->getLastName(),
            'status'        => $user->getStatus()?->value ?? 'pending',
            'roles'         => $user->getRoles() ?? 'user',
        ];

        $this->sqlService->query($sql, $params);

        return (int) $this->sqlService->lastInsertId();
    }

    /**
     * {@inheritDoc}
     */
    public function updateUser(User $user): bool
    {
        $sql = 'UPDATE sowingme.users
                SET display_name = :display_name,
                    password_hash = :password_hash,
                    email = :email,
                    first_name = :first_name,
                    last_name = :last_name,
                    status = :status,
                    roles = :roles,
                    failed_login_attempts = :failed_login_attempts,
                    last_failed_login = :last_failed_login,
                    last_login = :last_login,
                    updated_at = NOW()
                WHERE id = :id';

        $params = [
            'id'                    => $user->getId(),
            'display_name'          => $user->getDisplayName(),
            'password_hash'         => $user->getPasswordHash(),
            'email'                 => $user->getEmail(),
            'first_name'            => $user->getFirstName(),
            'last_name'             => $user->getLastName(),
            'status'                => $user->getStatus()?->value,
            'roles'                 => $user->getRoles(),
            'failed_login_attempts' => $user->getFailedLoginAttempts(),
            'last_failed_login'     => $user->getLastFailedLogin()?->format('Y-m-d H:i:s'),
            'last_login'            => $user->getLastLogin()?->format('Y-m-d H:i:s'),
        ];

        return $this->sqlService->query($sql, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function emailExists(Email $email): bool
    {
        $sql = 'SELECT COUNT(*) as count
                FROM sowingme.users
                WHERE email = :email';

        $result = $this->sqlService->getRow($sql, ['email' => $email->value]);

        return $result !== null && (int) $result['count'] > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function displayNameExists(DisplayName $displayName): bool
    {
        $sql = 'SELECT COUNT(*) as count
                FROM sowingme.users
                WHERE display_name = :display_name';

        $result = $this->sqlService->getRow($sql, ['display_name' => $displayName->value]);

        return $result !== null && (int) $result['count'] > 0;
    }

    /**
     * Hydrate a User model from database result
     *
     * @param array<string, mixed> $result The database result row
     *
     * @return User The hydrated user model
     */
    private function hydrateUser(array $result): User
    {
        return new User(
            id: (int) $result['id'],
            displayName: $result['display_name'],
            passwordHash: $result['password_hash'],
            email: $result['email'],
            firstName: $result['first_name'],
            lastName: $result['last_name'],
            status: $result['status'] !== null ? UserStatus::from($result['status']) : null,
            roles: $result['roles'],
            failedLoginAttempts: (int) $result['failed_login_attempts'],
            lastFailedLogin: $result['last_failed_login'] !== null ? new DateTime($result['last_failed_login']) : null,
            lastLogin: $result['last_login'] !== null ? new DateTime($result['last_login']) : null,
            createdAt: $result['created_at'] !== null ? new DateTime($result['created_at']) : null,
            updatedAt: $result['updated_at'] !== null ? new DateTime($result['updated_at']) : null,
        );
    }
}
