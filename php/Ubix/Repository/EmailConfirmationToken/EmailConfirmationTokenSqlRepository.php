<?php

declare(strict_types=1);

namespace Ubix\Repository\EmailConfirmationToken;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\EmailConfirmationToken;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenReaderInterface as TokenReader;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenWriterInterface as TokenWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Class EmailConfirmationTokenSqlRepository
 *
 * Implements methods to read and write email confirmation token data from the database.
 */
final class EmailConfirmationTokenSqlRepository implements TokenReader, TokenWriter
{
    /**
     * EmailConfirmationTokenSqlRepository constructor.
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
    public function getTokenByString(string $token): ?EmailConfirmationToken
    {
        $sql = 'SELECT
                    id,
                    user_id,
                    token,
                    expires_at,
                    created_at,
                    used_at
                FROM sowingme.email_confirmation_tokens
                WHERE token = :token
                LIMIT 1';

        $result = $this->sqlService->getRow($sql, ['token' => $token]);

        if ($result === null) {
            return null;
        }

        return $this->hydrateToken($result);
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveTokenForUser(int $userId): ?EmailConfirmationToken
    {
        $sql = 'SELECT
                    id,
                    user_id,
                    token,
                    expires_at,
                    created_at,
                    used_at
                FROM sowingme.email_confirmation_tokens
                WHERE user_id = :user_id
                AND used_at IS NULL
                AND expires_at > NOW()
                ORDER BY created_at DESC
                LIMIT 1';

        $result = $this->sqlService->getRow($sql, ['user_id' => $userId]);

        if ($result === null) {
            return null;
        }

        return $this->hydrateToken($result);
    }

    /**
     * {@inheritDoc}
     */
    public function createToken(EmailConfirmationToken $token): int
    {
        $sql = 'INSERT INTO sowingme.email_confirmation_tokens (
                    user_id,
                    token,
                    expires_at,
                    created_at
                ) VALUES (
                    :user_id,
                    :token,
                    :expires_at,
                    NOW()
                )';

        $params = [
            'user_id'    => $token->getUserId(),
            'token'      => $token->getToken(),
            'expires_at' => $token->getExpiresAt()?->format('Y-m-d H:i:s'),
        ];

        $this->sqlService->query($sql, $params);

        return (int) $this->sqlService->lastInsertId();
    }

    /**
     * {@inheritDoc}
     */
    public function markTokenAsUsed(int $tokenId): bool
    {
        $sql = 'UPDATE sowingme.email_confirmation_tokens
                SET used_at = NOW()
                WHERE id = :id';

        return $this->sqlService->query($sql, ['id' => $tokenId]);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteExpiredTokens(): int
    {
        $sql = 'DELETE FROM sowingme.email_confirmation_tokens
                WHERE expires_at < NOW()
                OR used_at IS NOT NULL';

        $this->sqlService->query($sql);

        return $this->sqlService->rowCount();
    }

    /**
     * Hydrate an EmailConfirmationToken model from database result
     *
     * @param array<string, mixed> $result The database result row
     *
     * @return EmailConfirmationToken The hydrated token model
     */
    private function hydrateToken(array $result): EmailConfirmationToken
    {
        return new EmailConfirmationToken(
            id: (int) $result['id'],
            userId: (int) $result['user_id'],
            token: $result['token'],
            expiresAt: $result['expires_at'] !== null ? new DateTime($result['expires_at']) : null,
            createdAt: $result['created_at'] !== null ? new DateTime($result['created_at']) : null,
            usedAt: $result['used_at'] !== null ? new DateTime($result['used_at']) : null,
        );
    }
}
