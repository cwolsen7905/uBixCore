<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubComment;

use Ubix\Model\FanClubComment;

/**
 * Interface for reading fan club comment data
 */
interface FanClubCommentReaderInterface
{
    /**
     * Get comments by post ID
     *
     * @param int $postId The post ID we want comments for
     *
     * @return FanClubComment[] An array of comment objects
     */
    public function getByPostId(int $postId): array;
}
