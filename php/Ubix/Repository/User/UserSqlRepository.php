<?php

declare(strict_types=1);

namespace Ubix\Repository\User;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataType\Int\UserId;
use Ubix\DataType\String\DisplayName;
use Ubix\DataType\String\Email;
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
 *
 * @see \Ubix\Tests\Repository\User\UserSqlRepositoryTest PHPUnit test case
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
     *
     * @throws Exception When no user has the given id
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
                    creator_name,
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

        if ($result === false) {
            throw new Exception('User not found', ExceptionCode::USER_NOT_FOUND->value);
        }

        return $this->hydrateUser($result);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception When no user has the given email
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
                    creator_name,
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

        if ($result === false || empty($result)) {
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
            'email'         => $user->getEmail(),
            'first_name'    => $user->getFirstName(),
            'last_name'     => $user->getLastName(),
            'password_hash' => $user->getPasswordHash(),
            'roles'         => $user->getRoles() ?? 'user',
            'status'        => $user->getStatus()->value ?? 'pending',
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
            'display_name'          => $user->getDisplayName(),
            'email'                 => $user->getEmail(),
            'failed_login_attempts' => $user->getFailedLoginAttempts(),
            'first_name'            => $user->getFirstName(),
            'id'                    => $user->getId(),
            'last_failed_login'     => $user->getLastFailedLogin()?->format('Y-m-d H:i:s'),
            'last_login'            => $user->getLastLogin()?->format('Y-m-d H:i:s'),
            'last_name'             => $user->getLastName(),
            'password_hash'         => $user->getPasswordHash(),
            'roles'                 => $user->getRoles(),
            'status'                => $user->getStatus()?->value,
        ];

        return $this->sqlService->query($sql, $params) > 0;
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

        return $result !== false && (int) $result['count'] > 0;
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

        return $result !== false && (int) $result['count'] > 0;
    }

    /**
     * Hydrate a User model from database result
     *
     * @param array<string, bool|int|float|string|null> $result The database result row
     *
     * @return User The hydrated user model
     */
    private function hydrateUser(array $result): User
    {
        return new User(
            id:                  (int) $result['id'],
            displayName:         is_string($result['display_name']) ? $result['display_name'] : null,
            passwordHash:        is_string($result['password_hash']) ? $result['password_hash'] : null,
            email:               is_string($result['email']) ? $result['email'] : null,
            firstName:           is_string($result['first_name']) ? $result['first_name'] : null,
            lastName:            is_string($result['last_name']) ? $result['last_name'] : null,
            creatorName:         is_string($result['creator_name']) ? $result['creator_name'] : null,
            status:              is_string($result['status']) ? UserStatus::from($result['status']) : null,
            roles:               is_string($result['roles']) ? $result['roles'] : null,
            failedLoginAttempts: (int) $result['failed_login_attempts'],
            lastFailedLogin:     is_string($result['last_failed_login']) ? new DateTime($result['last_failed_login']) : null,
            lastLogin:           is_string($result['last_login']) ? new DateTime($result['last_login']) : null,
            createdAt:           is_string($result['created_at']) ? new DateTime($result['created_at']) : null,
            updatedAt:           is_string($result['updated_at']) ? new DateTime($result['updated_at']) : null,
        );
    }
}
