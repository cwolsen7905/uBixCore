<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubComment;

use Ubix\Model\FanClubComment;

/**
 * Interface for writing fan club comment data
 */
interface FanClubCommentWriterInterface
{
    /**
     * Save a fan club comment
     *
     * @param FanClubComment $comment The comment to save
     *
     * @return void
     */
    public function save(FanClubComment $comment): void;
}
