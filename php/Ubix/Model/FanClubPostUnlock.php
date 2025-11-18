<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a fan club post unlock
 *
 * @see \Ubix\Tests\Model\FanClubPostUnlockTest PHPUnit test case
 */
final class FanClubPostUnlock extends Model
{
    /**
     * Constructor
     *
     * @param ?int                $id                   The post unlock's ID
     * @param ?int                $userId               The unlocker's ID
     * @param ?PlatformUser       $user                 The unlocker
     * @param ?int                $postId               The post being unlocked's ID
     * @param ?FanClubPost        $post                 The post being unlocked
     * @param ?string             $screenName           The screen name of the unlocker
     * @param ?DateTimeInterface  $purchasedAt          Timestamp of the unlock
     * @param ?int                $transactionId        The transaction connected to the unlock's ID
     * @param ?FanClubTransaction $transaction          The transaction connected to the unlock
     * @param ?string             $contentType          The content type being unlocked
     * @param ?int                $refFanclubTransactId Reference transaction ID
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?PlatformUser $user = null,
        private ?int $postId = null,
        private ?FanClubPost $post = null,
        private ?string $screenName = null,
        private ?DateTimeInterface $purchasedAt = null,
        private ?int $transactionId = null,
        private ?FanClubTransaction $transaction = null,
        private ?string $contentType = null,
        private ?int $refFanclubTransactId = null,
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
     * Get the value of postId
     *
     * @return ?int The value of postId
     */
    public function getPostId(): ?int
    {
        return $this->postId;
    }

    /**
     * Set the value of postId
     *
     * @param ?int $postId The value for postId
     *
     * @return void
     */
    public function setPostId(?int $postId): void
    {
        $this->postId = $postId;
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
     * Get the value of purchasedAt
     *
     * @return ?DateTimeInterface The value of purchasedAt
     */
    public function getPurchasedAt(): ?DateTimeInterface
    {
        return $this->purchasedAt;
    }

    /**
     * Set the value of purchasedAt
     *
     * @param ?DateTimeInterface $purchasedAt The value for purchasedAt
     *
     * @return void
     */
    public function setPurchasedAt(?DateTimeInterface $purchasedAt): void
    {
        $this->purchasedAt = $purchasedAt;
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
     * Get the value of contentType
     *
     * @return ?string The value of contentType
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * Set the value of contentType
     *
     * @param ?string $contentType The value for contentType
     *
     * @return void
     */
    public function setContentType(?string $contentType): void
    {
        $this->contentType = $contentType;
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
}
