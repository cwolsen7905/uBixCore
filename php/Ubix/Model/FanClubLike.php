<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a fan club like
 *
 * @see \Ubix\Tests\Model\FanClubLikeTest PHPUnit test case
 */
final class FanClubLike extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id         The like ID
     * @param ?int               $postId     The post ID of the liked
     * @param ?int               $userId     The user ID of the liker
     * @param ?string            $screenName The screen name of the liker
     * @param ?DateTimeInterface $createdAt  When the like occured
     */
    public function __construct(
        private ?int $id = null,
        private ?int $postId = null,
        private ?int $userId = null,
        private ?string $screenName = null,
        private ?DateTimeInterface $createdAt = null,
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
