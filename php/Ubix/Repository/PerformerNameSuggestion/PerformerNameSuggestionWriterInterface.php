<?php

declare(strict_types=1);

namespace Ubix\Repository\PerformerNameSuggestion;

use Ubix\Model\PerformerNameSuggestion;

/**
 * Interface for writing performer name suggestion data
 */
interface PerformerNameSuggestionWriterInterface
{
    /**
     * Save the performer name suggestion
     *
     * @param PerformerNameSuggestion $performerNameSuggestion The performer name suggestion to be saved
     *
     * @return void
     */
    public function save(PerformerNameSuggestion $performerNameSuggestion): void;
}
