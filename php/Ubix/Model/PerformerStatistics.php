<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\Model\AbstractModel as Model;

/**
 * Model of performer statistics
 *
 * @see \Ubix\Tests\Model\PerformerStatisticsTest PHPUnit test case
 */
final class PerformerStatistics extends Model
{
    /**
     * Constructor
     *
     * @param ?int   $publicPosts         Total public posts
     * @param ?int   $membersPosts        Total subscriber-only posts
     * @param ?int   $extraPosts          Total unlockable posts
     * @param ?int   $activeSubscriptions Total active subscriptions
     * @param ?int   $giftedSubscriptions Total gifted subscriptions
     * @param ?int   $images              Total images
     * @param ?int   $videos              Total videos
     * @param ?float $quotaMb             Quota for storage in megabytes
     * @param ?float $imagesMb            Total image storage in megabytes
     * @param ?float $videosMb            Total video storage in megabytes
     * @param ?float $videosDuration      Total video duration
     * @param ?int   $unconverted         Total unconverted media
     * @param ?float $preconversionMb     Preconverted media storage in megabytes
     * @param ?float $postconversionMb    Postconverted media storage in megabytes
     * @param ?int   $creditsEarned       Total credits earned
     * @param ?int   $transactions        Total transactions
     */
    public function __construct(
        private ?int $publicPosts = null,
        private ?int $membersPosts = null,
        private ?int $extraPosts = null,
        private ?int $activeSubscriptions = null,
        private ?int $giftedSubscriptions = null,
        private ?int $images = null,
        private ?int $videos = null,
        private ?float $quotaMb = null,
        private ?float $imagesMb = null,
        private ?float $videosMb = null,
        private ?float $videosDuration = null,
        private ?int $unconverted = null,
        private ?float $preconversionMb = null,
        private ?float $postconversionMb = null,
        private ?int $creditsEarned = null,
        private ?int $transactions = null,
    ) {
    }

    /**
     * Get the value of publicPosts
     *
     * @return ?int The value of publicPosts
     */
    public function getPublicPosts(): ?int
    {
        return $this->publicPosts;
    }

    /**
     * Set the value of publicPosts
     *
     * @param ?int $publicPosts The value for publicPosts
     *
     * @return void
     */
    public function setPublicPosts(?int $publicPosts): void
    {
        $this->publicPosts = $publicPosts;
    }

    /**
     * Get the value of membersPosts
     *
     * @return ?int The value of membersPosts
     */
    public function getMembersPosts(): ?int
    {
        return $this->membersPosts;
    }

    /**
     * Set the value of membersPosts
     *
     * @param ?int $membersPosts The value for membersPosts
     *
     * @return void
     */
    public function setMembersPosts(?int $membersPosts): void
    {
        $this->membersPosts = $membersPosts;
    }

    /**
     * Get the value of extraPosts
     *
     * @return ?int The value of extraPosts
     */
    public function getExtraPosts(): ?int
    {
        return $this->extraPosts;
    }

    /**
     * Set the value of extraPosts
     *
     * @param ?int $extraPosts The value for extraPosts
     *
     * @return void
     */
    public function setExtraPosts(?int $extraPosts): void
    {
        $this->extraPosts = $extraPosts;
    }

    /**
     * Get the value of activeSubscriptions
     *
     * @return ?int The value of activeSubscriptions
     */
    public function getActiveSubscriptions(): ?int
    {
        return $this->activeSubscriptions;
    }

    /**
     * Set the value of activeSubscriptions
     *
     * @param ?int $activeSubscriptions The value for activeSubscriptions
     *
     * @return void
     */
    public function setActiveSubscriptions(?int $activeSubscriptions): void
    {
        $this->activeSubscriptions = $activeSubscriptions;
    }

    /**
     * Get the value of giftedSubscriptions
     *
     * @return ?int The value of giftedSubscriptions
     */
    public function getGiftedSubscriptions(): ?int
    {
        return $this->giftedSubscriptions;
    }

    /**
     * Set the value of giftedSubscriptions
     *
     * @param ?int $giftedSubscriptions The value for giftedSubscriptions
     *
     * @return void
     */
    public function setGiftedSubscriptions(?int $giftedSubscriptions): void
    {
        $this->giftedSubscriptions = $giftedSubscriptions;
    }

    /**
     * Get the value of images
     *
     * @return ?int The value of images
     */
    public function getImages(): ?int
    {
        return $this->images;
    }

    /**
     * Set the value of images
     *
     * @param ?int $images The value for images
     *
     * @return void
     */
    public function setImages(?int $images): void
    {
        $this->images = $images;
    }

    /**
     * Get the value of videos
     *
     * @return ?int The value of videos
     */
    public function getVideos(): ?int
    {
        return $this->videos;
    }

    /**
     * Set the value of videos
     *
     * @param ?int $videos The value for videos
     *
     * @return void
     */
    public function setVideos(?int $videos): void
    {
        $this->videos = $videos;
    }

    /**
     * Get the value of quotaMb
     *
     * @return ?float The value of quotaMb
     */
    public function getQuotaMb(): ?float
    {
        return $this->quotaMb;
    }

    /**
     * Set the value of quotaMb
     *
     * @param ?float $quotaMb The value for quotaMb
     *
     * @return void
     */
    public function setQuotaMb(?float $quotaMb): void
    {
        $this->quotaMb = $quotaMb;
    }

    /**
     * Get the value of imagesMb
     *
     * @return ?float The value of imagesMb
     */
    public function getImagesMb(): ?float
    {
        return $this->imagesMb;
    }

    /**
     * Set the value of imagesMb
     *
     * @param ?float $imagesMb The value for imagesMb
     *
     * @return void
     */
    public function setImagesMb(?float $imagesMb): void
    {
        $this->imagesMb = $imagesMb;
    }

    /**
     * Get the value of videosMb
     *
     * @return ?float The value of videosMb
     */
    public function getVideosMb(): ?float
    {
        return $this->videosMb;
    }

    /**
     * Set the value of videosMb
     *
     * @param ?float $videosMb The value for videosMb
     *
     * @return void
     */
    public function setVideosMb(?float $videosMb): void
    {
        $this->videosMb = $videosMb;
    }

    /**
     * Get the value of videosDuration
     *
     * @return ?float The value of videosDuration
     */
    public function getVideosDuration(): ?float
    {
        return $this->videosDuration;
    }

    /**
     * Set the value of videosDuration
     *
     * @param ?float $videosDuration The value for videosDuration
     *
     * @return void
     */
    public function setVideosDuration(?float $videosDuration): void
    {
        $this->videosDuration = $videosDuration;
    }

    /**
     * Get the value of unconverted
     *
     * @return ?int The value of unconverted
     */
    public function getUnconverted(): ?int
    {
        return $this->unconverted;
    }

    /**
     * Set the value of unconverted
     *
     * @param ?int $unconverted The value for unconverted
     *
     * @return void
     */
    public function setUnconverted(?int $unconverted): void
    {
        $this->unconverted = $unconverted;
    }

    /**
     * Get the value of preconversionMb
     *
     * @return ?float The value of preconversionMb
     */
    public function getPreconversionMb(): ?float
    {
        return $this->preconversionMb;
    }

    /**
     * Set the value of preconversionMb
     *
     * @param ?float $preconversionMb The value for preconversionMb
     *
     * @return void
     */
    public function setPreconversionMb(?float $preconversionMb): void
    {
        $this->preconversionMb = $preconversionMb;
    }

    /**
     * Get the value of postconversionMb
     *
     * @return ?float The value of postconversionMb
     */
    public function getPostconversionMb(): ?float
    {
        return $this->postconversionMb;
    }

    /**
     * Set the value of postconversionMb
     *
     * @param ?float $postconversionMb The value for postconversionMb
     *
     * @return void
     */
    public function setPostconversionMb(?float $postconversionMb): void
    {
        $this->postconversionMb = $postconversionMb;
    }

    /**
     * Get the value of creditsEarned
     *
     * @return ?int The value of creditsEarned
     */
    public function getCreditsEarned(): ?int
    {
        return $this->creditsEarned;
    }

    /**
     * Set the value of creditsEarned
     *
     * @param ?int $creditsEarned The value for creditsEarned
     *
     * @return void
     */
    public function setCreditsEarned(?int $creditsEarned): void
    {
        $this->creditsEarned = $creditsEarned;
    }

    /**
     * Get the value of transactions
     *
     * @return ?int The value of transactions
     */
    public function getTransactions(): ?int
    {
        return $this->transactions;
    }

    /**
     * Set the value of transactions
     *
     * @param ?int $transactions The value for transactions
     *
     * @return void
     */
    public function setTransactions(?int $transactions): void
    {
        $this->transactions = $transactions;
    }
}
