<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a fan club comment
 *
 * @see \Ubix\Tests\Model\FanClubCommentTest PHPUnit test case
 */
final class FanClubComment extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id                 The comment's ID
     * @param ?int               $postId             The comment's post ID
     * @param ?FanClubPost       $post               The comment's post
     * @param ?int               $userId             The commenter's platform user ID
     * @param ?PlatformUser      $user               The commenter
     * @param ?string            $screenName         The screen name of the commenter
     * @param ?string            $commentText        The comment's text
     * @param ?DateTimeInterface $createdAt          Timestamp of the comment date/time
     * @param ?string            $status             The comment status
     * @param ?int               $adminId            The admin's ID who deactivated
     * @param ?DateTimeInterface $deactivatedAt      Timestamp of a deactivation if deactivated
     * @param ?string            $deactivationReason Reason for the deactivation if deactivated
     */
    public function __construct(
        private ?int $id = null,
        private ?int $postId = null,
        private ?FanClubPost $post = null,
        private ?int $userId = null,
        private ?PlatformUser $user = null,
        private ?string $screenName = null,
        private ?string $commentText = null,
        private ?DateTimeInterface $createdAt = null,
        private ?string $status = null,
        private ?int $adminId = null,
        private ?DateTimeInterface $deactivatedAt = null,
        private ?string $deactivationReason = null,
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
     * Get the value of commentText
     *
     * @return ?string The value of commentText
     */
    public function getCommentText(): ?string
    {
        return $this->commentText;
    }

    /**
     * Set the value of commentText
     *
     * @param ?string $commentText The value for commentText
     *
     * @return void
     */
    public function setCommentText(?string $commentText): void
    {
        $this->commentText = $commentText;
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
     * Get the value of adminId
     *
     * @return ?int The value of adminId
     */
    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    /**
     * Set the value of adminId
     *
     * @param ?int $adminId The value for adminId
     *
     * @return void
     */
    public function setAdminId(?int $adminId): void
    {
        $this->adminId = $adminId;
    }

    /**
     * Get the value of deactivatedAt
     *
     * @return ?DateTimeInterface The value of deactivatedAt
     */
    public function getDeactivatedAt(): ?DateTimeInterface
    {
        return $this->deactivatedAt;
    }

    /**
     * Set the value of deactivatedAt
     *
     * @param ?DateTimeInterface $deactivatedAt The value for deactivatedAt
     *
     * @return void
     */
    public function setDeactivatedAt(?DateTimeInterface $deactivatedAt): void
    {
        $this->deactivatedAt = $deactivatedAt;
    }

    /**
     * Get the value of deactivationReason
     *
     * @return ?string The value of deactivationReason
     */
    public function getDeactivationReason(): ?string
    {
        return $this->deactivationReason;
    }

    /**
     * Set the value of deactivationReason
     *
     * @param ?string $deactivationReason The value for deactivationReason
     *
     * @return void
     */
    public function setDeactivationReason(?string $deactivationReason): void
    {
        $this->deactivationReason = $deactivationReason;
    }
}
