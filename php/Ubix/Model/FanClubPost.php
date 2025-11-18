<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a fan club post
 *
 * @see \Ubix\Tests\Model\FanClubPostTest PHPUnit test case
 */
final class FanClubPost extends Model
{
    /**
     * Constructor
     *
     * @param ?int                $id              The post's ID
     * @param ?int                $modelId         The post's performer's ID
     * @param ?Performer          $performer       The post's performer
     * @param ?int                $fileId          The post's file's ID
     * @param ?FanClubFile        $file            The post's file
     * @param ?string             $title           The post's title
     * @param ?string             $description     The post's description
     * @param ?string             $hashtags        The post's hashtag(s)
     * @param ?string             $visibility      The post's visibility
     * @param ?int                $priceCredits    The cost in credits
     * @param ?DateTimeInterface  $scheduledAt     Timestamp of scheduled publish
     * @param ?DateTimeInterface  $publishedAt     Timestamp of initial publish
     * @param ?string             $status          The post's status
     * @param ?DateTimeInterface  $createdAt       Timestamp of post creation
     * @param ?DateTimeInterface  $updatedAt       Timestamp of last update
     * @param ?int                $likesCount      Number of likes
     * @param ?int                $commentsCount   Number of comments
     * @param ?int                $lifetimeCredits The post's lifetime earnings in credits
     * @param ?FanClubLike[]      $likes           Array of fan club likes
     * @param ?FanClubComment[]   $comments        Array of fan club comments
     * @param ?string             $hasLiked        Whether or not the post is likable ('Y' or 'N')
     * @param ?string             $hasMembership   Whether or not an active membership exists ('Y' or 'N')
     * @param ?string             $hasUnlocked     Whether or not the post has been unlocked ('Y' or 'N')
     * @param ?FanClubTransaction $transaction     The transaction related to the post unlock
     */
    public function __construct(
        private ?int $id = null,
        private ?int $modelId = null,
        private ?Performer $performer = null,
        private ?int $fileId = null,
        private ?FanClubFile $file = null,
        private ?string $title = null,
        private ?string $description = null,
        private ?string $hashtags = null,
        private ?string $visibility = null,
        private ?int $priceCredits = null,
        private ?DateTimeInterface $scheduledAt = null,
        private ?DateTimeInterface $publishedAt = null,
        private ?string $status = null,
        private ?DateTimeInterface $createdAt = null,
        private ?DateTimeInterface $updatedAt = null,
        private ?int $likesCount = null,
        private ?int $commentsCount = null,
        private ?int $lifetimeCredits = null,
        private ?array $likes = null,
        private ?array $comments = null,
        private ?string $hasLiked = null,
        private ?string $hasMembership = null,
        private ?string $hasUnlocked = null,
        private ?FanClubTransaction $transaction = null,
    ) {
    }

    /**
     * Get the post's permalink
     *
     * @return string The permalink
     */
    public function getPermalink(): string
    {
        return '/' . $this->getPerformer()?->getSlug() . '#post-' . $this->getId();
    }

    /**
     * Determine if the post is viewable
     *
     * @return bool Whether or not the post is viewable
     */
    public function isViewable(): bool
    {
        //
        //  If the post has visibility set to public then everyone can view it
        //
        if ($this->getVisibility() === 'public') {
            return true;
        }

        //
        //  If the post has visibility set to public then check if the active user has a membership
        //
        if ($this->getVisibility() === 'members' && $this->getHasMembership() === 'Y') {
            return true;
        }

        //
        //  If the post has visibility set to extra then check if the active user has unlocked the post
        //
        return $this->getVisibility() === 'extra' && $this->getHasUnlocked() === 'Y';
    }

    /**
     * Determine if the post is likable
     *
     * @return bool Whether or not the post is likable
     */
    public function isLikable(): bool
    {
        //
        //  The logic for being able to like or view is the same
        //
        return $this->isViewable();
    }

    /**
     * Determine if the post is commentable
     *
     * @return bool Whether or not the post is commentable
     */
    public function isCommentable(): bool
    {
        //
        //  The logic for being able to comment or view is the same
        //
        return $this->isViewable();
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
     * Get the value of fileId
     *
     * @return ?int The value of fileId
     */
    public function getFileId(): ?int
    {
        return $this->fileId;
    }

    /**
     * Set the value of fileId
     *
     * @param ?int $fileId The value for fileId
     *
     * @return void
     */
    public function setFileId(?int $fileId): void
    {
        $this->fileId = $fileId;
    }

    /**
     * Get the value of file
     *
     * @return ?FanClubFile The value of file
     */
    public function getFile(): ?FanClubFile
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @param ?FanClubFile $file The value for file
     *
     * @return void
     */
    public function setFile(?FanClubFile $file): void
    {
        $this->file = $file;
    }

    /**
     * Get the value of title
     *
     * @return ?string The value of title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param ?string $title The value for title
     *
     * @return void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the value of description
     *
     * @return ?string The value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param ?string $description The value for description
     *
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the value of hashtags
     *
     * @return ?string The value of hashtags
     */
    public function getHashtags(): ?string
    {
        return $this->hashtags;
    }

    /**
     * Set the value of hashtags
     *
     * @param ?string $hashtags The value for hashtags
     *
     * @return void
     */
    public function setHashtags(?string $hashtags): void
    {
        $this->hashtags = $hashtags;
    }

    /**
     * Get the value of visibility
     *
     * @return ?string The value of visibility
     */
    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    /**
     * Set the value of visibility
     *
     * @param ?string $visibility The value for visibility
     *
     * @return void
     */
    public function setVisibility(?string $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * Get the value of priceCredits
     *
     * @return ?int The value of priceCredits
     */
    public function getPriceCredits(): ?int
    {
        return $this->priceCredits;
    }

    /**
     * Set the value of priceCredits
     *
     * @param ?int $priceCredits The value for priceCredits
     *
     * @return void
     */
    public function setPriceCredits(?int $priceCredits): void
    {
        $this->priceCredits = $priceCredits;
    }

    /**
     * Get the value of scheduledAt
     *
     * @return ?DateTimeInterface The value of scheduledAt
     */
    public function getScheduledAt(): ?DateTimeInterface
    {
        return $this->scheduledAt;
    }

    /**
     * Set the value of scheduledAt
     *
     * @param ?DateTimeInterface $scheduledAt The value for scheduledAt
     *
     * @return void
     */
    public function setScheduledAt(?DateTimeInterface $scheduledAt): void
    {
        $this->scheduledAt = $scheduledAt;
    }

    /**
     * Get the value of publishedAt
     *
     * @return ?DateTimeInterface The value of publishedAt
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * Set the value of publishedAt
     *
     * @param ?DateTimeInterface $publishedAt The value for publishedAt
     *
     * @return void
     */
    public function setPublishedAt(?DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
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
     * @param ?DateTimeInterface $updatedAt The value for updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get the value of likesCount
     *
     * @return ?int The value of likesCount
     */
    public function getLikesCount(): ?int
    {
        return $this->likesCount;
    }

    /**
     * Set the value of likesCount
     *
     * @param ?int $likesCount The value for likesCount
     *
     * @return void
     */
    public function setLikesCount(?int $likesCount): void
    {
        $this->likesCount = $likesCount;
    }

    /**
     * Get the value of commentsCount
     *
     * @return ?int The value of commentsCount
     */
    public function getCommentsCount(): ?int
    {
        return $this->commentsCount;
    }

    /**
     * Set the value of commentsCount
     *
     * @param ?int $commentsCount The value for commentsCount
     *
     * @return void
     */
    public function setCommentsCount(?int $commentsCount): void
    {
        $this->commentsCount = $commentsCount;
    }

    /**
     * Get the value of lifetimeCredits
     *
     * @return ?int The value of lifetimeCredits
     */
    public function getLifetimeCredits(): ?int
    {
        return $this->lifetimeCredits;
    }

    /**
     * Set the value of lifetimeCredits
     *
     * @param ?int $lifetimeCredits The value for lifetimeCredits
     *
     * @return void
     */
    public function setLifetimeCredits(?int $lifetimeCredits): void
    {
        $this->lifetimeCredits = $lifetimeCredits;
    }

    /**
     * Get the value of likes
     *
     * @return ?FanClubLike[] The value of likes
     */
    public function getLikes(): ?array
    {
        return $this->likes;
    }

    /**
     * Set the value of likes
     *
     * @param ?FanClubLike[] $likes The value for likes
     *
     * @return void
     */
    public function setLikes(?array $likes): void
    {
        $this->likes = $likes;
    }

    /**
     * Get the value of comments
     *
     * @return ?FanClubComment[] The value of comments
     */
    public function getComments(): ?array
    {
        return $this->comments;
    }

    /**
     * Set the value of comments
     *
     * @param ?FanClubComment[] $comments The value for comments
     *
     * @return void
     */
    public function setComments(?array $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * Get the value of hasLiked
     *
     * @return ?string The value of hasLiked
     */
    public function getHasLiked(): ?string
    {
        return $this->hasLiked;
    }

    /**
     * Set the value of hasLiked
     *
     * @param ?string $hasLiked The value for hasLiked
     *
     * @return void
     */
    public function setHasLiked(?string $hasLiked): void
    {
        $this->hasLiked = $hasLiked;
    }

    /**
     * Get the value of hasMembership
     *
     * @return ?string The value of hasMembership
     */
    public function getHasMembership(): ?string
    {
        return $this->hasMembership;
    }

    /**
     * Set the value of hasMembership
     *
     * @param ?string $hasMembership The value for hasMembership
     *
     * @return void
     */
    public function setHasMembership(?string $hasMembership): void
    {
        $this->hasMembership = $hasMembership;
    }

    /**
     * Get the value of hasUnlocked
     *
     * @return ?string The value of hasUnlocked
     */
    public function getHasUnlocked(): ?string
    {
        return $this->hasUnlocked;
    }

    /**
     * Set the value of hasUnlocked
     *
     * @param ?string $hasUnlocked The value for hasUnlocked
     *
     * @return void
     */
    public function setHasUnlocked(?string $hasUnlocked): void
    {
        $this->hasUnlocked = $hasUnlocked;
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
}
