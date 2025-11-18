<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubPost;

use Ubix\Model\FanClubPost;

/**
 * Interface for reading fan club post data
 */
interface FanClubPostReaderInterface
{
    /**
     * Get the most liked fan club posts for the trending widget
     *
     * @return FanClubPost[] An array of fan club post objects
     */
    public function getByMostLiked(): array;

    /**
     * Get the most commented fan club posts for the trending widget
     *
     * @return FanClubPost[] An array of fan club post objects
     */
    public function getByMostCommented(): array;

    /**
     * Get fan club posts using the top hashtag for the trending widget
     *
     * @return FanClubPost[] An array of fan club post objects
     */
    public function getByTopHashtag(): array;

    /**
     * Get newest fan club posts for the trending widget
     *
     * @return FanClubPost[] An array of fan club post objects
     */
    public function getByDateCreated(): array;

    /**
     * Get all public fan club posts for the trending widget
     *
     * @return FanClubPost[] An array of fan club post objects
     */
    public function getPublicPosts(): array;

    /**
     * Get all fan club posts by ID
     *
     * @param int $id The post's ID
     *
     * @return FanClubPost[] An array of fan club post objects
     */
    public function getById(int $id): array;

    /**
     * Get all fan club posts by performer ID
     *
     * @param int     $performerId   The performer's ID
     * @param ?string $status        The post's status (optional)
     * @param ?string $visibility    The post's visibility (optional)
     * @param ?bool   $purchasedOnly The post's purchased status (true = purchased only, false = unpurchased only, null = both) (optional)
     * @param ?int    $userId        The user's ID (optional)
     *
     * @return FanClubPost[] An array of fan club post objects
     */
    public function getByModelId(int $performerId, ?string $status = 'published', ?string $visibility = null, ?bool $purchasedOnly = false, ?int $userId = null): array;

    /**
     * Get all unlocked fan club posts by user ID
     *
     * @param int  $userId The user's ID
     * @param ?int $postId Filter further by post ID
     *
     * @return FanClubPost[] An array of fan club post objects
     */
    public function getUnlockedByUserId(int $userId, ?int $postId = null): array;
}
