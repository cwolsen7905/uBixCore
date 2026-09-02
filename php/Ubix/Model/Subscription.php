<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\Subscription\SubscriptionStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * A supporter's paid subscription to one creator tier (status written by payments)
 *
 * @see \Ubix\Tests\Model\SubscriptionTest PHPUnit test case
 */
final class Subscription extends Model
{
    /**
     * Constructor
     *
     * @param ?int                $id                     The value of id
     * @param ?int                $userId                 The value of userId
     * @param ?int                $creatorId              The value of creatorId
     * @param ?int                $tierId                 The value of tierId
     * @param ?SubscriptionStatus $status                 The value of status
     * @param ?string             $providerSubscriptionId The value of providerSubscriptionId
     * @param ?string             $providerCustomerId     The value of providerCustomerId
     * @param ?DateTimeInterface  $currentPeriodEnd       The value of currentPeriodEnd
     * @param ?DateTimeInterface  $canceledAt             The value of canceledAt
     * @param ?DateTimeInterface  $createdAt              The value of createdAt
     * @param ?DateTimeInterface  $updatedAt              The value of updatedAt
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?int $creatorId = null,
        private ?int $tierId = null,
        private ?SubscriptionStatus $status = null,
        private ?string $providerSubscriptionId = null,
        private ?string $providerCustomerId = null,
        private ?DateTimeInterface $currentPeriodEnd = null,
        private ?DateTimeInterface $canceledAt = null,
        private ?DateTimeInterface $createdAt = null,
        private ?DateTimeInterface $updatedAt = null,
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
     * Get the value of creatorId
     *
     * @return ?int The value of creatorId
     */
    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }

    /**
     * Set the value of creatorId
     *
     * @param ?int $creatorId The value of creatorId
     *
     * @return void
     */
    public function setCreatorId(?int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    /**
     * Get the value of tierId
     *
     * @return ?int The value of tierId
     */
    public function getTierId(): ?int
    {
        return $this->tierId;
    }

    /**
     * Set the value of tierId
     *
     * @param ?int $tierId The value of tierId
     *
     * @return void
     */
    public function setTierId(?int $tierId): void
    {
        $this->tierId = $tierId;
    }

    /**
     * Get the value of status
     *
     * @return ?SubscriptionStatus The value of status
     */
    public function getStatus(): ?SubscriptionStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?SubscriptionStatus $status The value of status
     *
     * @return void
     */
    public function setStatus(?SubscriptionStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of providerSubscriptionId
     *
     * @return ?string The value of providerSubscriptionId
     */
    public function getProviderSubscriptionId(): ?string
    {
        return $this->providerSubscriptionId;
    }

    /**
     * Set the value of providerSubscriptionId
     *
     * @param ?string $providerSubscriptionId The value of providerSubscriptionId
     *
     * @return void
     */
    public function setProviderSubscriptionId(?string $providerSubscriptionId): void
    {
        $this->providerSubscriptionId = $providerSubscriptionId;
    }

    /**
     * Get the value of providerCustomerId
     *
     * @return ?string The value of providerCustomerId
     */
    public function getProviderCustomerId(): ?string
    {
        return $this->providerCustomerId;
    }

    /**
     * Set the value of providerCustomerId
     *
     * @param ?string $providerCustomerId The value of providerCustomerId
     *
     * @return void
     */
    public function setProviderCustomerId(?string $providerCustomerId): void
    {
        $this->providerCustomerId = $providerCustomerId;
    }

    /**
     * Get the value of currentPeriodEnd
     *
     * @return ?DateTimeInterface The value of currentPeriodEnd
     */
    public function getCurrentPeriodEnd(): ?DateTimeInterface
    {
        return $this->currentPeriodEnd;
    }

    /**
     * Set the value of currentPeriodEnd
     *
     * @param ?DateTimeInterface $currentPeriodEnd The value of currentPeriodEnd
     *
     * @return void
     */
    public function setCurrentPeriodEnd(?DateTimeInterface $currentPeriodEnd): void
    {
        $this->currentPeriodEnd = $currentPeriodEnd;
    }

    /**
     * Get the value of canceledAt
     *
     * @return ?DateTimeInterface The value of canceledAt
     */
    public function getCanceledAt(): ?DateTimeInterface
    {
        return $this->canceledAt;
    }

    /**
     * Set the value of canceledAt
     *
     * @param ?DateTimeInterface $canceledAt The value of canceledAt
     *
     * @return void
     */
    public function setCanceledAt(?DateTimeInterface $canceledAt): void
    {
        $this->canceledAt = $canceledAt;
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
     * Get the value of updatedAt
     *
     * @return ?DateTimeInterface The value of updatedAt
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param ?DateTimeInterface $updatedAt The value of updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
