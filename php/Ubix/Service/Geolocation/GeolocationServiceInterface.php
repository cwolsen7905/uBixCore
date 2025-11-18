<?php

declare(strict_types=1);

namespace Ubix\Service\Geolocation;

use Ubix\DataTransferObject\GeolocationLookup;

/**
 * Interface for a geolocation service
 */
interface GeolocationServiceInterface
{
    /**
     * Get a geolocation lookup by IP address
     *
     * @param string $ipAddress The IP address
     *
     * @return GeolocationLookup A geolocation lookup
     */
    public function getLookupByIpAddress(string $ipAddress): GeolocationLookup;
}
