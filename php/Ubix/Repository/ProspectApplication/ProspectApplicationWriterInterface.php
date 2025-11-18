<?php

declare(strict_types=1);

namespace Ubix\Repository\ProspectApplication;

use Ubix\Model\ProspectApplication;

/**
 * Interface for writing prospect application data
 */
interface ProspectApplicationWriterInterface
{
    /**
     * Save the prospect application
     *
     * @param ProspectApplication $prospectApplication The prospect application to be saved
     *
     * @return void
     */
    public function save(ProspectApplication $prospectApplication): void;
}
