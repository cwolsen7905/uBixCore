<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubLike;

use Ubix\Model\FanClubLike;

/**
 * Interface for reading fan club like data
 */
interface FanClubLikeReaderInterface
{
    /**
     * Get likes by user ID and post ID
     *
     * @param int $userId The user ID we want likes for
     * @param int $postId The post ID we want likes for
     *
     * @return FanClubLike[] An array of like objects
     */
    public function get(int $userId, int $postId): array;
}
