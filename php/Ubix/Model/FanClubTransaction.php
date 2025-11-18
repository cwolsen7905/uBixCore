<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a fan club transaction
 *
 * @see \Ubix\Tests\Model\FanClubTransactionTest PHPUnit test case
 */
final class FanClubTransaction extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id                   The transaction's ID
     * @param ?int               $userId               The purchaser's ID
     * @param ?PlatformUser      $user                 The purchaser
     * @param ?int               $modelId              The performer connected to the transaction's ID
     * @param ?Performer         $performer            The performer connected to the transaction
     * @param ?string            $type                 The transaction's type
     * @param ?int               $amountCredits        The transaction's cost in credits
     * @param ?string            $screenName           The purchaser's screen name
     * @param ?int               $broadcasterId        The performer's broadcaster ID
     * @param ?string            $studioCode           The performer's studio code
     * @param ?int               $locationId           The location ID
     * @param ?int               $refFanclubTransactId Reference transaction ID
     * @param ?DateTimeInterface $createdAt            Timestamp of the transaction
     * @param ?FanClubPost       $post                 The post connected to the transaction
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?PlatformUser $user = null,
        private ?int $modelId = null,
        private ?Performer $performer = null,
        private ?string $type = null,
        private ?int $amountCredits = null,
        private ?string $screenName = null,
        private ?int $broadcasterId = null,
        private ?string $studioCode = null,
        private ?int $locationId = null,
        private ?int $refFanclubTransactId = null,
        private ?DateTimeInterface $createdAt = null,
        private ?FanClubPost $post = null,
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
     * Get the value of user
     *
     * @return ?PlatformUser The value of user
     */
    public function getUser(): ?PlatformUser
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @param ?PlatformUser $user The value for user
     *
     * @return void
     */
    public function setUser(?PlatformUser $user): void
    {
        $this->user = $user;
    }

    /**
     * Get the value of modelId
     *
     * @return ?int The value of modelId
     */
    public function getModelId(): ?int
    {
        return $this->modelId;
    }

    /**
     * Set the value of modelId
     *
     * @param ?int $modelId The value for modelId
     *
     * @return void
     */
    public function setModelId(?int $modelId): void
    {
        $this->modelId = $modelId;
    }

    /**
     * Get the value of performer
     *
     * @return ?Performer The value of performer
     */
    public function getPerformer(): ?Performer
    {
        return $this->performer;
    }

    /**
     * Set the value of performer
     *
     * @param ?Performer $performer The value for performer
     *
     * @return void
     */
    public function setPerformer(?Performer $performer): void
    {
        $this->performer = $performer;
    }

    /**
     * Get the value of type
     *
     * @return ?string The value of type
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param ?string $type The value for type
     *
     * @return void
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * Get the value of amountCredits
     *
     * @return ?int The value of amountCredits
     */
    public function getAmountCredits(): ?int
    {
        return $this->amountCredits;
    }

    /**
     * Set the value of amountCredits
     *
     * @param ?int $amountCredits The value for amountCredits
     *
     * @return void
     */
    public function setAmountCredits(?int $amountCredits): void
    {
        $this->amountCredits = $amountCredits;
    }

    /**
     * Get the value of screenName
     *
     * @return ?string The value of screenName
     */
    public function getScreenName(): ?string
    {
        return $this->screenName;
    }

    /**
     * Set the value of screenName
     *
     * @param ?string $screenName The value for screenName
     *
     * @return void
     */
    public function setScreenName(?string $screenName): void
    {
        $this->screenName = $screenName;
    }

    /**
     * Get the value of broadcasterId
     *
     * @return ?int The value of broadcasterId
     */
    public function getBroadcasterId(): ?int
    {
        return $this->broadcasterId;
    }

    /**
     * Set the value of broadcasterId
     *
     * @param ?int $broadcasterId The value for broadcasterId
     *
     * @return void
     */
    public function setBroadcasterId(?int $broadcasterId): void
    {
        $this->broadcasterId = $broadcasterId;
    }

    /**
     * Get the value of studioCode
     *
     * @return ?string The value of studioCode
     */
    public function getStudioCode(): ?string
    {
        return $this->studioCode;
    }

    /**
     * Set the value of studioCode
     *
     * @param ?string $studioCode The value for studioCode
     *
     * @return void
     */
    public function setStudioCode(?string $studioCode): void
    {
        $this->studioCode = $studioCode;
    }

    /**
     * Get the value of locationId
     *
     * @return ?int The value of locationId
     */
    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    /**
     * Set the value of locationId
     *
     * @param ?int $locationId The value for locationId
     *
     * @return void
     */
    public function setLocationId(?int $locationId): void
    {
        $this->locationId = $locationId;
    }

    /**
     * Get the value of refFanclubTransactId
     *
     * @return ?int The value of refFanclubTransactId
     */
    public function getRefFanclubTransactId(): ?int
    {
        return $this->refFanclubTransactId;
    }

    /**
     * Set the value of refFanclubTransactId
     *
     * @param ?int $refFanclubTransactId The value for refFanclubTransactId
     *
     * @return void
     */
    public function setRefFanclubTransactId(?int $refFanclubTransactId): void
    {
        $this->refFanclubTransactId = $refFanclubTransactId;
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
     * Get the value of post
     *
     * @return ?FanClubPost The value of post
     */
    public function getPost(): ?FanClubPost
    {
        return $this->post;
    }

    /**
     * Set the value of post
     *
     * @param ?FanClubPost $post The value for post
     *
     * @return void
     */
    public function setPost(?FanClubPost $post): void
    {
        $this->post = $post;
    }
}
