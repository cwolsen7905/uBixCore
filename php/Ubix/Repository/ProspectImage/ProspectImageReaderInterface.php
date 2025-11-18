<?php

declare(strict_types=1);

namespace Ubix\Repository\ProspectImage;

use Ubix\Model\ProspectImage;

/**
 * Interface for reading prospect application data
 */
interface ProspectImageReaderInterface
{
    /**
     * Get images by prospect ID
     *
     * @param int $prospectId The prospect's ID
     *
     * @return ProspectImage[] An array of image objects
     */
    public function getByProspectId(int $prospectId): array;
}
