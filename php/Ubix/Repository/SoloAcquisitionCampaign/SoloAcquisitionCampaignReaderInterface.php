<?php

declare(strict_types=1);

namespace Ubix\Repository\SoloAcquisitionCampaign;

use Ubix\Model\SoloAcquisitionCampaign;

/**
 * Interface for reading solo acquisition campaign data
 */
interface SoloAcquisitionCampaignReaderInterface
{
    /**
     * Get solo acquisition campaigns by ID
     *
     * @param int $id The campaign's ID
     *
     * @return SoloAcquisitionCampaign[] An array of campaign objects
     */
    public function getById(int $id): array;

    /**
     * Get a count of solo acquisition campaigns by ID
     *
     * @param int $id The campaign's ID
     *
     * @return int The count of solo acquisition campaigns
     */
    public function getCountById(int $id): int;
}
