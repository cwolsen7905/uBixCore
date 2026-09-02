<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\PasswordResetToken;
use Ubix\Repository\PasswordResetToken\PasswordResetTokenReaderInterface as PasswordResetTokenReader;
use Ubix\Repository\PasswordResetToken\PasswordResetTokenWriterInterface as PasswordResetTokenWriter;

/**
 * Password reset tokens: issue, look up, consume, supersede
 *
 * The raw token only ever exists in the emailed URL; storage and lookup use
 * its SHA-256 hash (authentication TDS §2.2 / §8).
 *
 * @see \Ubix\Tests\Service\PasswordResetTokenServiceTest PHPUnit test case
 */
final class PasswordResetTokenService
{
    private const TOKEN_TTL = '+1 hour';

    /**
     * Constructor
     *
     * @param Logger                   $logger      Logger
     * @param PasswordResetTokenReader $tokenReader Token reader
     * @param PasswordResetTokenWriter $tokenWriter Token writer
     */
    public function __construct(
        private Logger $logger,
        private PasswordResetTokenReader $tokenReader,
        private PasswordResetTokenWriter $tokenWriter,
    ) {
    }

    /**
     * Issue a fresh token for a user, superseding any outstanding ones
     *
     * @param int $userId The user id
     *
     * @return string The RAW token for the emailed URL (never stored)
     */
    public function issueToken(int $userId): string
    {
        $this->tokenWriter->invalidateOutstandingTokensForUser($userId);

        $rawToken = bin2hex(random_bytes(32));
        $token    = new PasswordResetToken(
            userId:    $userId,
            tokenHash: hash('sha256', $rawToken),
            expiresAt: new DateTime(self::TOKEN_TTL),
        );

        $this->tokenWriter->createToken($token);
        $this->logger->info('Password reset token issued', [
            'token_id' => $token->getId(),
            'user_id'  => $userId,
        ]);

        return $rawToken;
    }

    /**
     * Get the stored token matching a raw token, when valid (unused, unexpired)
     *
     * @param string $rawToken The raw token from the reset URL
     *
     * @return ?PasswordResetToken The valid token, or null
     */
    public function getValidToken(string $rawToken): ?PasswordResetToken
    {
        $token = $this->tokenReader->getTokenByHash(hash('sha256', $rawToken));

        if ($token === null || $token->getUsedAt() !== null) {
            return null;
        }

        if ($token->getExpiresAt() === null || $token->getExpiresAt() < new DateTime()) {
            return null;
        }

        return $token;
    }

    /**
     * Consume a token and supersede every other outstanding token for the user
     *
     * @param PasswordResetToken $token The token being consumed
     *
     * @return void
     */
    public function consumeToken(PasswordResetToken $token): void
    {
        $tokenId = $token->getId();
        $userId  = $token->getUserId();
        assert($tokenId !== null && $userId !== null);

        $this->tokenWriter->markTokenAsUsed($tokenId);
        $this->tokenWriter->invalidateOutstandingTokensForUser($userId);
        $this->logger->info('Password reset token consumed', [
            'token_id' => $tokenId,
            'user_id'  => $userId,
        ]);
    }
}
