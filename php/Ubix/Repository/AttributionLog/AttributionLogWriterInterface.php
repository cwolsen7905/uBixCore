<?php

declare(strict_types=1);

namespace Ubix\Repository\AttributionLog;

use Ubix\Model\AttributionLog;

/**
 * Interface for writing attribution log data
 */
interface AttributionLogWriterInterface
{
    /**
     * Save a new attribution log entry
     *
     * @param AttributionLog $logData The attribution log data
     *
     * @return void
     */
    public function save(AttributionLog $logData): void;
}
