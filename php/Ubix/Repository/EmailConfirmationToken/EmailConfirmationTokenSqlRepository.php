<?php

declare(strict_types=1);

namespace Ubix\Repository\EmailConfirmationToken;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\EmailConfirmationTokenOptions;
use Ubix\Model\EmailConfirmationToken;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenReaderInterface as EmailConfirmationTokenReader;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenWriterInterface as EmailConfirmationTokenWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Class EmailConfirmationTokenSqlRepository
 *
 * Implements methods to read and write email confirmation token data from the database.
 *
 * @see \Ubix\Tests\Repository\EmailConfirmationToken\EmailConfirmationTokenSqlRepositoryTest PHPUnit test case
 */
final class EmailConfirmationTokenSqlRepository implements EmailConfirmationTokenReader, EmailConfirmationTokenWriter
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
        $tokens = $this->query(new EmailConfirmationTokenOptions(
            token: $token,
            limit: 1,
        ));

        return $tokens[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveTokenForUser(int $userId): ?EmailConfirmationToken
    {
        $tokens = $this->query(new EmailConfirmationTokenOptions(
            userId:     $userId,
            activeOnly: true,
            limit:      1,
        ));

        return $tokens[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function createToken(EmailConfirmationToken $token): void
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
            'expires_at' => $token->getExpiresAt()?->format('Y-m-d H:i:s'),
            'token'      => $token->getToken(),
            'user_id'    => $token->getUserId(),
        ];

        $this->sqlService->query($sql, $params);

        $token->setId((int) $this->sqlService->lastInsertId());
    }

    /**
     * {@inheritDoc}
     */
    public function markTokenAsUsed(int $tokenId): void
    {
        $sql = 'UPDATE sowingme.email_confirmation_tokens
                SET used_at = NOW()
                WHERE id = :id';

        $this->sqlService->query($sql, ['id' => $tokenId]);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteExpiredTokens(): void
    {
        $sql = 'DELETE FROM sowingme.email_confirmation_tokens
                WHERE expires_at < NOW()
                OR used_at IS NOT NULL';

        $this->sqlService->query($sql);
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param EmailConfirmationTokenOptions $options DTO of options to generate the query
     *
     * @return EmailConfirmationToken[] An array of objects
     */
    private function query(EmailConfirmationTokenOptions $options): array
    {
        $sqlQuery = $this->getSqlQuery($options);

        $objects = [];

        /**
 * @var array<string, bool|float|int|string|null> $row
*/
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters, true) as $row) {
            $objects[] = $this->hydrateToken($row);
        }

        return $objects;
    }

    /**
     * Get a SQL query DTO ready to be executed
     *
     * @param EmailConfirmationTokenOptions $options DTO of options to generate the query
     *
     * @return SqlQuery A SQL query DTO
     */
    private function getSqlQuery(EmailConfirmationTokenOptions $options): SqlQuery
    {
        $parameters = [];

        $sql = 'SELECT
                    id,
                    user_id,
                    token,
                    expires_at,
                    created_at,
                    used_at
                FROM sowingme.email_confirmation_tokens
                WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->userId !== null) {
            $sql .= ' AND user_id = :userId';

            $parameters['userId'] = $options->userId;
        }

        if ($options->token !== null) {
            $sql .= ' AND token = :token';

            $parameters['token'] = $options->token;
        }

        if ($options->activeOnly) {
            $sql .= ' AND used_at IS NULL AND expires_at > NOW()';
        }

        $sql .= ' ORDER BY created_at DESC';

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        return new SqlQuery(
            sql:        $sql,
            parameters: $parameters,
        );
    }

    /**
     * Hydrate an EmailConfirmationToken model from database result
     *
     * @param array<string, bool|int|float|string|null> $result The database result row
     *
     * @return EmailConfirmationToken The hydrated token model
     */
    private function hydrateToken(array $result): EmailConfirmationToken
    {
        return new EmailConfirmationToken(
            id:        (int) $result['id'],
            userId:    (int) $result['user_id'],
            token:     is_string($result['token']) ? $result['token'] : null,
            expiresAt: is_string($result['expires_at']) ? new DateTime($result['expires_at']) : null,
            createdAt: is_string($result['created_at']) ? new DateTime($result['created_at']) : null,
            usedAt:    is_string($result['used_at']) ? new DateTime($result['used_at']) : null,
        );
    }
}
