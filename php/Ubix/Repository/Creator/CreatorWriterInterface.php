<?php

declare(strict_types=1);

namespace Ubix\Repository\Creator;

use Ubix\Model\Creator;

/**
 * Writes creator rows
 */
interface CreatorWriterInterface
{
    /**
     * Create a new creator
     *
     * @param Creator $creator The creator to create
     *
     * @return void The created ID is set on the passed model
     */
    public function createCreator(Creator $creator): void;
}
