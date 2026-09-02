<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\Creator\CreatorCategory;
use Ubix\Enum\Creator\CreatorStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * A creator: the public-facing identity a user operates (creator-profile SRS FR-101)
 *
 * @see \Ubix\Tests\Model\CreatorTest PHPUnit test case
 */
final class Creator extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id                The creator id
     * @param ?int               $userId            The owning user id (1:1)
     * @param ?string            $slug              The unique URL slug
     * @param ?string            $displayName       The public display name
     * @param ?string            $bio               The long-form bio
     * @param ?string            $avatarUrl         The avatar image URL
     * @param ?string            $bannerUrl         The banner image URL
     * @param ?CreatorCategory   $category          The creator category
     * @param ?string            $faithTopic        The faith topic / denomination
     * @param ?string            $externalLinksJson The external links as raw JSON ([{label, url}])
     * @param ?int               $organizationId    The reserved organization id (ADR-007, unset at M1)
     * @param ?int               $payoutAccountId   The reserved payout account id (unset until payouts, M2)
     * @param ?CreatorStatus     $status            The lifecycle status
     * @param ?DateTimeInterface $publishedAt       When the page first went active
     * @param ?DateTimeInterface $createdAt         When the row was created
     * @param ?DateTimeInterface $updatedAt         When the row was last updated
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?string $slug = null,
        private ?string $displayName = null,
        private ?string $bio = null,
        private ?string $avatarUrl = null,
        private ?string $bannerUrl = null,
        private ?CreatorCategory $category = null,
        private ?string $faithTopic = null,
        private ?string $externalLinksJson = null,
        private ?int $organizationId = null,
        private ?int $payoutAccountId = null,
        private ?CreatorStatus $status = null,
        private ?DateTimeInterface $publishedAt = null,
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
     * Get the value of slug
     *
     * @return ?string The value of slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @param ?string $slug The value of slug
     *
     * @return void
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Get the value of displayName
     *
     * @return ?string The value of displayName
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * Set the value of displayName
     *
     * @param ?string $displayName The value of displayName
     *
     * @return void
     */
    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * Get the value of bio
     *
     * @return ?string The value of bio
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * Set the value of bio
     *
     * @param ?string $bio The value of bio
     *
     * @return void
     */
    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }

    /**
     * Get the value of avatarUrl
     *
     * @return ?string The value of avatarUrl
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    /**
     * Set the value of avatarUrl
     *
     * @param ?string $avatarUrl The value of avatarUrl
     *
     * @return void
     */
    public function setAvatarUrl(?string $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * Get the value of bannerUrl
     *
     * @return ?string The value of bannerUrl
     */
    public function getBannerUrl(): ?string
    {
        return $this->bannerUrl;
    }

    /**
     * Set the value of bannerUrl
     *
     * @param ?string $bannerUrl The value of bannerUrl
     *
     * @return void
     */
    public function setBannerUrl(?string $bannerUrl): void
    {
        $this->bannerUrl = $bannerUrl;
    }

    /**
     * Get the value of category
     *
     * @return ?CreatorCategory The value of category
     */
    public function getCategory(): ?CreatorCategory
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @param ?CreatorCategory $category The value of category
     *
     * @return void
     */
    public function setCategory(?CreatorCategory $category): void
    {
        $this->category = $category;
    }

    /**
     * Get the value of faithTopic
     *
     * @return ?string The value of faithTopic
     */
    public function getFaithTopic(): ?string
    {
        return $this->faithTopic;
    }

    /**
     * Set the value of faithTopic
     *
     * @param ?string $faithTopic The value of faithTopic
     *
     * @return void
     */
    public function setFaithTopic(?string $faithTopic): void
    {
        $this->faithTopic = $faithTopic;
    }

    /**
     * Get the value of externalLinksJson
     *
     * @return ?string The value of externalLinksJson
     */
    public function getExternalLinksJson(): ?string
    {
        return $this->externalLinksJson;
    }

    /**
     * Set the value of externalLinksJson
     *
     * @param ?string $externalLinksJson The value of externalLinksJson
     *
     * @return void
     */
    public function setExternalLinksJson(?string $externalLinksJson): void
    {
        $this->externalLinksJson = $externalLinksJson;
    }

    /**
     * Get the value of organizationId
     *
     * @return ?int The value of organizationId
     */
    public function getOrganizationId(): ?int
    {
        return $this->organizationId;
    }

    /**
     * Set the value of organizationId
     *
     * @param ?int $organizationId The value of organizationId
     *
     * @return void
     */
    public function setOrganizationId(?int $organizationId): void
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Get the value of payoutAccountId
     *
     * @return ?int The value of payoutAccountId
     */
    public function getPayoutAccountId(): ?int
    {
        return $this->payoutAccountId;
    }

    /**
     * Set the value of payoutAccountId
     *
     * @param ?int $payoutAccountId The value of payoutAccountId
     *
     * @return void
     */
    public function setPayoutAccountId(?int $payoutAccountId): void
    {
        $this->payoutAccountId = $payoutAccountId;
    }

    /**
     * Get the value of status
     *
     * @return ?CreatorStatus The value of status
     */
    public function getStatus(): ?CreatorStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?CreatorStatus $status The value of status
     *
     * @return void
     */
    public function setStatus(?CreatorStatus $status): void
    {
        $this->status = $status;
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
     * @param ?DateTimeInterface $publishedAt The value of publishedAt
     *
     * @return void
     */
    public function setPublishedAt(?DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
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
