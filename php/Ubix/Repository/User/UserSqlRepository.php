<?php

declare(strict_types=1);

namespace Ubix\Repository\User;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataType\Int\UserId;
use Ubix\DataType\String\Username;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\User\UserStatus;
use Ubix\Model\User;
use Ubix\Repository\User\UserReaderInterface as UserReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Class UserSqlRepository
 *
 * Implements methods to read user-related data from the database.
 */
final class UserSqlRepository implements UserReader
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
                    username,
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
    public function getUserByUsername(Username $username): User
    {
        $sql = 'SELECT
                    id,
                    username,
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
                WHERE username = :username
                LIMIT 1';

        $result = $this->sqlService->getRow($sql, ['username' => $username->value]);

        if ($result === null || empty($result)) {
            throw new Exception('User not found', ExceptionCode::USER_NOT_FOUND->value);
        }

        return $this->hydrateUser($result);
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
            username: $result['username'],
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
