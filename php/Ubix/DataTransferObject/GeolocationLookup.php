<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a geolocation lookup
 *
 * @see \Ubix\Service\Geolocation\GeolocationServiceInterface This DTO is used by geolocation service
 * @see \Ubix\Tests\DataTransferObject\GeolocationLookupTest PHPUnit test case
 */
final readonly class GeolocationLookup implements Dto
{
    /**
     * Constructor
     *
     * @param ?string $city        The city name (optional) (default: null)
     * @param ?string $countryCode The country code (optional) (default: null)
     * @param ?string $ipAddress   The IP address (optional) (default: null)
     * @param ?string $postalCode  The postal code (optional) (default: null)
     * @param ?string $stateCode   The state code (optional) (default: null)
     */
    public function __construct(
        public readonly ?string $city = null,
        public readonly ?string $countryCode = null,
        public readonly ?string $ipAddress = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $stateCode = null,
    ) {
    }
}
