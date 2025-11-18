<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubLike;

use Ubix\Model\FanClubLike;

/**
 * Interface for writing fan club like data
 */
interface FanClubLikeWriterInterface
{
    /**
     * Save a fan club like
     *
     * @param FanClubLike $like The like to save
     *
     * @return void
     */
    public function save(FanClubLike $like): void;

    /**
     * Delete a fan club like
     *
     * @param FanClubLike $like The like to delete
     *
     * @return void
     */
    public function delete(FanClubLike $like): void;
}
