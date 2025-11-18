<?php

declare(strict_types=1);

namespace Ubix\Repository\PerformerNameSuggestion;

use Ubix\Enum\Performer\PerformerNameSuggestionSex;
use Ubix\Model\PerformerNameSuggestion;

/**
 * Interface for reading performer name suggestion data
 */
interface PerformerNameSuggestionReaderInterface
{
    /**
     * Get performer name suggestion(s)
     *
     * @param PerformerNameSuggestionSex $sex            The performer name suggestion sex
     * @param ?string                    $searchQuery    The search query (optional) (default: null)
     * @param int                        $maximumResults The maximum size of the results (optional) (default: PHP_INT_MAX)
     * @param int                        $resultsOffset  The offset of the results (optional) (default: 0)
     *
     * @return PerformerNameSuggestion[] An array of matching performer name suggestion(s)
     */
    public function get(
        PerformerNameSuggestionSex $sex,
        ?string $searchQuery = null,
        int $maximumResults = PHP_INT_MAX,
        int $resultsOffset = 0,
    ): array;

    /**
     * Get performer name suggestion(s) by name
     *
     * @param string $name The name
     *
     * @return PerformerNameSuggestion[] An array of matching performer name suggestion(s)
     */
    public function getByName(string $name): array;

    /**
     * Get a count of performer name suggestion(s)
     *
     * @param PerformerNameSuggestionSex $sex         The performer name suggestion sex
     * @param ?string                    $searchQuery The search query (optional) (default: null)
     *
     * @return int The count of performer name suggestion(s)
     */
    public function getCount(
        PerformerNameSuggestionSex $sex,
        ?string $searchQuery = null,
    ): int;
}
