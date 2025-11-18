<?php

declare(strict_types=1);

namespace Ubix\Repository\Prospect;

use Ubix\Model\Prospect;

/**
 * Interface for writing prospect data
 */
interface ProspectWriterInterface
{
    /**
     * Save the prospect
     *
     * @param Prospect $prospect The prospect to be saved
     *
     * @return void
     */
    public function save(Prospect $prospect): void;
}
