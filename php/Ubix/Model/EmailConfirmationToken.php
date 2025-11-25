<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of an email confirmation token
 */
final class EmailConfirmationToken extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id        Token ID
     * @param ?int               $userId    User ID
     * @param ?string            $token     Confirmation token
     * @param ?DateTimeInterface $expiresAt Expiration datetime
     * @param ?DateTimeInterface $createdAt Created datetime
     * @param ?DateTimeInterface $usedAt    Used datetime (null if unused)
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?string $token = null,
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
     * @param ?int $id The value for id
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
     * @param ?int $userId The value for userId
     *
     * @return void
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Get the value of token
     *
     * @return ?string The value of token
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @param ?string $token The value for token
     *
     * @return void
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
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
     * @param ?DateTimeInterface $expiresAt The value for expiresAt
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
     * @param ?DateTimeInterface $createdAt The value for createdAt
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
     * @param ?DateTimeInterface $usedAt The value for usedAt
     *
     * @return void
     */
    public function setUsedAt(?DateTimeInterface $usedAt): void
    {
        $this->usedAt = $usedAt;
    }
}
