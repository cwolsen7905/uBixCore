<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTime;
use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a fan club membership
 *
 * @see \Ubix\Tests\Model\FanClubMembershipTest PHPUnit test case
 */
final class FanClubMembership extends Model
{
    /**
     * Constructor
     *
     * @param ?int                $id                   The membership's ID
     * @param ?int                $userId               The user connected to the membership's ID
     * @param ?PlatformUser       $user                 The user connected to the membership
     * @param ?int                $modelId              The performer connected to the membership's ID
     * @param ?Performer          $performer            The performer connected to the membership
     * @param ?int                $membershipPriceId    The membership's price ID
     * @param ?DateTimeInterface  $startDate            The membership's start date
     * @param ?DateTimeInterface  $endDate              The membership's end date
     * @param ?string             $status               The membership's status
     * @param ?string             $membershipType       The membership type
     * @param ?string             $screenName           The user connected to the membership's screen name
     * @param ?int                $transactionId        The transaction connected to the membership's ID
     * @param ?FanClubTransaction $transaction          The transaction connected to the membership
     * @param ?int                $refFanclubTransactId Reference transaction ID
     * @param ?DateTimeInterface  $createdAt            The timestamp when the membership was created
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?PlatformUser $user = null,
        private ?int $modelId = null,
        private ?Performer $performer = null,
        private ?int $membershipPriceId = null,
        private ?DateTimeInterface $startDate = null,
        private ?DateTimeInterface $endDate = null,
        private ?string $status = null,
        private ?string $membershipType = null,
        private ?string $screenName = null,
        private ?int $transactionId = null,
        private ?FanClubTransaction $transaction = null,
        private ?int $refFanclubTransactId = null,
        private ?DateTimeInterface $createdAt = null,
    ) {
    }

    /**
     * Determine if the membership is valid or not
     *
     * @return bool Whether or not the membership is valid
     */
    public function isValid(): bool
    {
        return $this->getEndDate() !== null && $this->getEndDate()->getTimestamp() - (int)(new DateTime())->format('U') > 0;
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
     * Get the value of membershipPriceId
     *
     * @return ?int The value of membershipPriceId
     */
    public function getMembershipPriceId(): ?int
    {
        return $this->membershipPriceId;
    }

    /**
     * Set the value of membershipPriceId
     *
     * @param ?int $membershipPriceId The value for membershipPriceId
     *
     * @return void
     */
    public function setMembershipPriceId(?int $membershipPriceId): void
    {
        $this->membershipPriceId = $membershipPriceId;
    }

    /**
     * Get the value of startDate
     *
     * @return ?DateTimeInterface The value of startDate
     */
    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * Set the value of startDate
     *
     * @param ?DateTimeInterface $startDate The value for startDate
     *
     * @return void
     */
    public function setStartDate(?DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * Get the value of endDate
     *
     * @return ?DateTimeInterface The value of endDate
     */
    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate
     *
     * @param ?DateTimeInterface $endDate The value for endDate
     *
     * @return void
     */
    public function setEndDate(?DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * Get the value of status
     *
     * @return ?string The value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?string $status The value for status
     *
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of membershipType
     *
     * @return ?string The value of membershipType
     */
    public function getMembershipType(): ?string
    {
        return $this->membershipType;
    }

    /**
     * Set the value of membershipType
     *
     * @param ?string $membershipType The value for membershipType
     *
     * @return void
     */
    public function setMembershipType(?string $membershipType): void
    {
        $this->membershipType = $membershipType;
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
     * Get the value of transactionId
     *
     * @return ?int The value of transactionId
     */
    public function getTransactionId(): ?int
    {
        return $this->transactionId;
    }

    /**
     * Set the value of transactionId
     *
     * @param ?int $transactionId The value for transactionId
     *
     * @return void
     */
    public function setTransactionId(?int $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Get the value of transaction
     *
     * @return ?FanClubTransaction The value of transaction
     */
    public function getTransaction(): ?FanClubTransaction
    {
        return $this->transaction;
    }

    /**
     * Set the value of transaction
     *
     * @param ?FanClubTransaction $transaction The value for transaction
     *
     * @return void
     */
    public function setTransaction(?FanClubTransaction $transaction): void
    {
        $this->transaction = $transaction;
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
}
