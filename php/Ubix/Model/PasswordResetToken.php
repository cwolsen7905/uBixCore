<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * A single-use, time-limited password reset token (stored hashed)
 *
 * @see \Ubix\Tests\Model\PasswordResetTokenTest PHPUnit test case
 */
final class PasswordResetToken extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id        The token id
     * @param ?int               $userId    The user the token was issued to
     * @param ?string            $tokenHash SHA-256 hash of the raw token
     * @param ?DateTimeInterface $expiresAt When the token stops being valid
     * @param ?DateTimeInterface $createdAt When the token was issued
     * @param ?DateTimeInterface $usedAt    When the token was consumed
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?string $tokenHash = null,
        private ?DateTimeInterface $expiresAt = null,
        private ?DateTimeInterface $createdAt = null,
        private ?DateTimeInterface $usedAt = null,
    ) {
    }

    /**
     * Get the value of id
     *
     * @return ?int The value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param ?int $id The value of id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of userId
     *
     * @return ?int The value of userId
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param ?int $userId The value of userId
     *
     * @return void
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Get the value of tokenHash
     *
     * @return ?string The value of tokenHash
     */
    public function getTokenHash(): ?string
    {
        return $this->tokenHash;
    }

    /**
     * Set the value of tokenHash
     *
     * @param ?string $tokenHash The value of tokenHash
     *
     * @return void
     */
    public function setTokenHash(?string $tokenHash): void
    {
        $this->tokenHash = $tokenHash;
    }

    /**
     * Get the value of expiresAt
     *
     * @return ?DateTimeInterface The value of expiresAt
     */
    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * Set the value of expiresAt
     *
     * @param ?DateTimeInterface $expiresAt The value of expiresAt
     *
     * @return void
     */
    public function setExpiresAt(?DateTimeInterface $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get the value of createdAt
     *
     * @return ?DateTimeInterface The value of createdAt
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param ?DateTimeInterface $createdAt The value of createdAt
     *
     * @return void
     */
    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get the value of usedAt
     *
     * @return ?DateTimeInterface The value of usedAt
     */
    public function getUsedAt(): ?DateTimeInterface
    {
        return $this->usedAt;
    }

    /**
     * Set the value of usedAt
     *
     * @param ?DateTimeInterface $usedAt The value of usedAt
     *
     * @return void
     */
    public function setUsedAt(?DateTimeInterface $usedAt): void
    {
        $this->usedAt = $usedAt;
    }
}
