<?php

declare(strict_types=1);

namespace Ubix\Repository\PasswordResetToken;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\PasswordResetTokenOptions;
use Ubix\Model\PasswordResetToken;
use Ubix\Repository\PasswordResetToken\PasswordResetTokenReaderInterface as PasswordResetTokenReader;
use Ubix\Repository\PasswordResetToken\PasswordResetTokenWriterInterface as PasswordResetTokenWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * SQL-backed password reset token repository
 *
 * @see \Ubix\Tests\Repository\PasswordResetToken\PasswordResetTokenSqlRepositoryTest PHPUnit test case
 */
final class PasswordResetTokenSqlRepository implements PasswordResetTokenReader, PasswordResetTokenWriter
{
    /**
     * Constructor
     *
     * @param Logger     $logger     The Monolog logger
     * @param SqlService $sqlService The SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (required first dependency of every repository)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenByHash(string $tokenHash): ?PasswordResetToken
    {
        return $this->query(new PasswordResetTokenOptions(tokenHash: $tokenHash, limit: 1))[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function createToken(PasswordResetToken $token): void
    {
        $sql = 'INSERT INTO sowingme.password_reset_tokens (user_id, token_hash, expires_at)
                VALUES (:user_id, :token_hash, :expires_at)';

        $this->sqlService->query($sql, [
            'expires_at' => $token->getExpiresAt()?->format('Y-m-d H:i:s'),
            'token_hash' => $token->getTokenHash(),
            'user_id'    => $token->getUserId(),
        ]);

        $token->setId((int) $this->sqlService->lastInsertId());
    }

    /**
     * {@inheritDoc}
     */
    public function markTokenAsUsed(int $tokenId): void
    {
        $sql = 'UPDATE sowingme.password_reset_tokens SET used_at = NOW() WHERE id = :id';

        $this->sqlService->query($sql, ['id' => $tokenId]);
    }

    /**
     * {@inheritDoc}
     */
    public function invalidateOutstandingTokensForUser(int $userId): void
    {
        $sql = 'UPDATE sowingme.password_reset_tokens
                SET used_at = NOW()
                WHERE user_id = :user_id
                AND used_at IS NULL';

        $this->sqlService->query($sql, ['user_id' => $userId]);
    }

    /**
     * Query password reset tokens
     *
     * @param PasswordResetTokenOptions $options DTO of options to generate the query
     *
     * @return array<int, PasswordResetToken> The matching tokens, newest first
     */
    private function query(PasswordResetTokenOptions $options): array
    {
        $sql        = 'SELECT id, user_id, token_hash, expires_at, created_at, used_at FROM sowingme.password_reset_tokens';
        $where      = [];
        $parameters = [];

        if ($options->id !== null) {
            $where[]          = 'id = :id';
            $parameters['id'] = $options->id;
        }

        if ($options->tokenHash !== null) {
            $where[]                 = 'token_hash = :tokenHash';
            $parameters['tokenHash'] = $options->tokenHash;
        }

        if ($options->userId !== null) {
            $where[]              = 'user_id = :userId';
            $parameters['userId'] = $options->userId;
        }

        if ($options->activeOnly) {
            $where[] = 'used_at IS NULL';
            $where[] = 'expires_at > NOW()';
        }

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY created_at DESC';

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        $tokens = [];
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $tokens[] = $this->hydrateToken($row);
        }

        return $tokens;
    }

    /**
     * Hydrate a PasswordResetToken model from a database row
     *
     * @param array<string, bool|int|float|string|null> $row The database result row
     *
     * @return PasswordResetToken The hydrated token model
     */
    private function hydrateToken(array $row): PasswordResetToken
    {
        return new PasswordResetToken(
            id:        (int) $row['id'],
            userId:    (int) $row['user_id'],
            tokenHash: is_string($row['token_hash']) ? $row['token_hash'] : null,
            expiresAt: is_string($row['expires_at']) ? new DateTime($row['expires_at']) : null,
            createdAt: is_string($row['created_at']) ? new DateTime($row['created_at']) : null,
            usedAt:    is_string($row['used_at']) ? new DateTime($row['used_at']) : null,
        );
    }
}
