<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClub;

use Ubix\Model\FanClub;

/**
 * Interface for reading fan club data
 */
interface FanClubReaderInterface
{
    /**
     * Get all fan clubs by model ID
     *
     * @param int $modelId The model ID
     *
     * @return FanClub[] An array of fan club objects
     */
    public function get(int $modelId): array;
}
