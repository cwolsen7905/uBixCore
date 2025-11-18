<?php

declare(strict_types=1);

namespace Ubix\Service\Geolocation;

use Exception;
use InvalidArgumentException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\GeolocationLookup;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Service\Geolocation\GeolocationServiceInterface as GeolocationService;
use Ubix\Service\JsonService;

/**
 * Geolocation service powered by the VSM legacy internal API
 *
 * @see \Ubix\Tests\Service\Geolocation\UbixGeolocationServiceTest PHPUnit test case
 */
final class UbixGeolocationService implements GeolocationService
{
    /**
     * Constructor
     *
     * @param Logger         $logger                 Logger
     * @param HttpClient     $httpClient             HTTP client
     * @param RequestFactory $requestFactory         Request factory
     * @param JsonService    $jsonService            JSON service
     * @param string         $apiProtocolAndHostname API protocol and hostname
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private JsonService $jsonService,
        private string $apiProtocolAndHostname = '',
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If the API returns empty or invalid data
     * @throws InvalidArgumentException If an invalid parameter is passed in
     */
    public function getLookupByIpAddress(string $ipAddress): GeolocationLookup
    {
        //
        //  Validate parameters
        //
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('The IP address `' . $ipAddress . '` is invalid', ExceptionCode::INVALID_IP_ADDRESS_FOR_GEOLOCATION_LOOKUP->value);
        }

        //
        //  Call the VSM internal geolocation API
        //
        $apiUrl = $this->apiProtocolAndHostname . '/geolocation/resolve-ip-location.php?' . http_build_query([
            'ip' => $ipAddress,
        ]);

        $apiRequest = $this->requestFactory->createRequest('GET', $apiUrl);

        $apiResponse = $this->httpClient->sendRequest($apiRequest);

        //
        //  Decode the API response from JSON and return the data in a DTO
        //
        /**
         * @var array{
         *     COUNTRY: ?string,
         *     STATE: ?string,
         *     CITY: ?string,
         *     POSTAL: ?string,
         *     USER_IP: ?string,
         * } $apiResponseDecoded
         */
        $apiResponseDecoded = $this->jsonService->decode((string)$apiResponse->getBody());

        if (!array_key_exists('COUNTRY', $apiResponseDecoded) || trim($apiResponseDecoded['COUNTRY'] ?? '') === '') {
            throw new Exception('There was an error retrieving geolocation lookup data.', ExceptionCode::MISSING_GEOLOCATION_LOOKUP_DATA->value);
        }

        return new GeolocationLookup(
            city:        $apiResponseDecoded['CITY'] ?? null,
            countryCode: $apiResponseDecoded['COUNTRY'] ?? null,
            ipAddress:   $apiResponseDecoded['USER_IP'] ?? null,
            postalCode:  $apiResponseDecoded['POSTAL'] ?? null,
            stateCode:   $apiResponseDecoded['STATE'] ?? null,
        );
    }
}
