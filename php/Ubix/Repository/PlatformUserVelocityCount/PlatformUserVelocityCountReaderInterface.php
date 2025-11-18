<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUserVelocityCount;

use Ubix\Model\PlatformUserVelocityCount;

/**
 * Interface for reading platform user velocity count data
 */
interface PlatformUserVelocityCountReaderInterface
{
    /**
     * Get platform user velocity count
     *
     * @param string $ipAddress               The IP address
     * @param string $username                The username
     * @param int    $secondsInThePast        The number of seconds to look back when doing the count
     * @param int    $ipAddressCacheThreshold The threshold that must be hit to cache the IP address results (optional) (default: PHP_INT_MAX)
     * @param int    $usernameCacheThreshold  The threshold that must be hit to cache the username results (optional) (default: PHP_INT_MAX)
     *
     * @return PlatformUserVelocityCount[] An array of matching platform user velocity counts
     */
    public function get(
        string $ipAddress,
        string $username,
        int $secondsInThePast,
        int $ipAddressCacheThreshold = PHP_INT_MAX,
        int $usernameCacheThreshold = PHP_INT_MAX,
    ): array;
}
