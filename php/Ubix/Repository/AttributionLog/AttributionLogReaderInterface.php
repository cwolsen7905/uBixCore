<?php

declare(strict_types=1);

namespace Ubix\Repository\AttributionLog;

use Ubix\DataType\Int\PlatformUserId;
use Ubix\Model\AttributionLog;

/**
 * Interface for reading attribution log data
 */
interface AttributionLogReaderInterface
{
    /**
     * Get attribution log entries by platform user ID
     *
     * @param PlatformUserId $userId The user's ID
     *
     * @return array<AttributionLog> An array containing the attribution log entries
     */
    public function getLogByPlatformUserId(PlatformUserId $userId): array;
}
