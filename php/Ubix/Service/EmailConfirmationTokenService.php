<?php

declare(strict_types=1);

namespace Ubix\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\EmailConfirmationToken;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenReaderInterface as EmailConfirmationTokenReader;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenWriterInterface as EmailConfirmationTokenWriter;

/**
 * Service to read and write email confirmation tokens
 *
 * @see \Ubix\Tests\Service\EmailConfirmationTokenServiceTest PHPUnit test case
 */
final class EmailConfirmationTokenService
{
    /**
     * Constructor
     *
     * @param Logger                       $logger      Logger
     * @param EmailConfirmationTokenReader $tokenReader Email confirmation token reader
     * @param EmailConfirmationTokenWriter $tokenWriter Email confirmation token writer
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private EmailConfirmationTokenReader $tokenReader,
        private EmailConfirmationTokenWriter $tokenWriter,
    ) {
    }

    /**
     * Get a token by its string value
     *
     * @param string $token The confirmation token string
     *
     * @return ?EmailConfirmationToken The token, or null if not found
     */
    public function getTokenByString(string $token): ?EmailConfirmationToken
    {
        return $this->tokenReader->getTokenByString($token);
    }

    /**
     * Get the active (unused, unexpired) token for a user
     *
     * @param int $userId The user ID
     *
     * @return ?EmailConfirmationToken The token, or null if not found
     */
    public function getActiveTokenForUser(int $userId): ?EmailConfirmationToken
    {
        return $this->tokenReader->getActiveTokenForUser($userId);
    }

    /**
     * Create a new token
     *
     * @param EmailConfirmationToken $token The token to create
     *
     * @return int The created token ID
     */
    public function createToken(EmailConfirmationToken $token): int
    {
        $this->tokenWriter->createToken($token);

        return (int) $token->getId();
    }

    /**
     * Mark a token as used
     *
     * @param int $tokenId The token ID
     *
     * @return void
     */
    public function markTokenAsUsed(int $tokenId): void
    {
        $this->tokenWriter->markTokenAsUsed($tokenId);
    }

    /**
     * Delete expired and used tokens
     *
     * @return void
     */
    public function deleteExpiredTokens(): void
    {
        $this->tokenWriter->deleteExpiredTokens();
    }
}
