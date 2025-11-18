<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubPostUnlock;

use Ubix\Model\FanClubPostUnlock;

/**
 * Interface for writing fan club post unlock data
 */
interface FanClubPostUnlockWriterInterface
{
    /**
     * Save a fan club post unlock
     *
     * @param FanClubPostUnlock $postUnlock The post unlock to save
     *
     * @return void
     */
    public function save(FanClubPostUnlock $postUnlock): void;
}
