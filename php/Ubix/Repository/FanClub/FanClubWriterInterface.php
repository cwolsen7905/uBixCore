<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClub;

use Ubix\Model\FanClub;

/**
 * Interface for writing fan club data
 */
interface FanClubWriterInterface
{
    /**
     * Save a fan club
     *
     * @param FanClub $fanClub The fan club to save
     *
     * @return void
     */
    public function save(FanClub $fanClub): void;
}
