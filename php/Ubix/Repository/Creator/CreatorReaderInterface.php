<?php

declare(strict_types=1);

namespace Ubix\Repository\Creator;

use Ubix\Model\Creator;

/**
 * Reads creator rows and slug history
 */
interface CreatorReaderInterface
{
    /**
     * Get a creator by slug (any status)
     *
     * @param string $slug The slug to look up
     *
     * @return ?Creator The creator, or null if no row matches
     */
    public function getCreatorBySlug(string $slug): ?Creator;

    /**
     * Get a creator by owning user id
     *
     * @param int $userId The owning user id
     *
     * @return ?Creator The creator, or null if the user has none
     */
    public function getCreatorByUserId(int $userId): ?Creator;

    /**
     * Resolve a retired slug to the owning creator's current slug (FR-202)
     *
     * @param string $slug The retired slug
     *
     * @return ?string The current slug, or null if the slug was never retired
     */
    public function getCurrentSlugForRetiredSlug(string $slug): ?string;

    /**
     * Whether a slug is taken, across live creators and retired slugs (FR-203)
     *
     * @param string $slug The slug to check
     *
     * @return bool True when the slug is unavailable
     */
    public function slugExists(string $slug): bool;
}
