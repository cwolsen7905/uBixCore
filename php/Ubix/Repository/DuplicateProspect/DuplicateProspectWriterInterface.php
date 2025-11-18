<?php

declare(strict_types=1);

namespace Ubix\Repository\DuplicateProspect;

use Ubix\Model\DuplicateProspect;

/**
 * Interface for writing duplicate prospect data
 */
interface DuplicateProspectWriterInterface
{
    /**
     * Save the duplicate prospect
     *
     * @param DuplicateProspect $duplicateProspect The duplicate prospect to be saved
     *
     * @return void
     */
    public function save(DuplicateProspect $duplicateProspect): void;
}
