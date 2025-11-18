<?php

declare(strict_types=1);

namespace Ubix\Repository\PerformerStatistics;

use Ubix\Model\PerformerStatistics;

/**
 * Interface for reading model statistics data
 */
interface PerformerStatisticsReaderInterface
{
    /**
     * Get performer statistics by performer ID
     *
     * @param int $performerId The performer ID
     *
     * @return PerformerStatistics[] An array of matching performer statistics
     */
    public function getByPerformerId(int $performerId): array;
}
