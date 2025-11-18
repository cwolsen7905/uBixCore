<?php

declare(strict_types=1);

namespace Ubix\Service;

use InvalidArgumentException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\FilterStringResult;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to filter data
 *
 * @see \Ubix\Tests\Service\FilterServiceTest PHPUnit test case
 */
final class FilterService
{
    /**
     * Constructor
     *
     * @param Logger         $logger                 Logger
     * @param HttpClient     $httpClient             HTTP client
     * @param RequestFactory $requestFactory         Request factory
     * @param StreamFactory  $streamFactory          Stream factory
     * @param JsonService    $jsonService            JSON service
     * @param string         $bearerToken            The bearer token to use for API calls
     * @param string         $apiProtocolAndHostname The API protocol and hostname
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private StreamFactory $streamFactory,
        private JsonService $jsonService,
        private string $bearerToken,
        private string $apiProtocolAndHostname,
    ) {
    }

    /**
     * Filter a string
     *
     * @param string $string   The string to filter
     * @param string $category The category
     * @param string $zone     The zone
     *
     * @throws InvalidArgumentException When a parameter is invalid
     *
     * @return FilterStringResult DTO with the results of the API call
     */
    public function string(string $string, string $category = 'chat', string $zone = 'chat'): FilterStringResult
    {
        //
        //  Validate parameters
        //
        if (trim($string) === '') {
            throw new InvalidArgumentException('You must include a non-empty string.', ExceptionCode::MISSING_STRING_FOR_FILTER->value);
        }

        //
        //  Format the string for the API
        //
        $formattedString = preg_replace('/[^a-zA-Z0-9]/', ' ', $string) ?? ''; // Replace all non-alphanumeric characters with a space
        $formattedString = preg_replace('!\s+!', ' ', $formattedString) ?? ''; // Remove repeat spaces and replace with a single space

        //
        //  Call the VSM internal filter API
        //
        $apiUrl = $this->apiProtocolAndHostname . '/filter?' . http_build_query([
            'category' => $category,
            'key'      => $this->bearerToken,
            'verbose'  => 1,
            'zone'     => $zone,
        ]);

        $apiRequest = $this->requestFactory
            ->createRequest('POST', $apiUrl)
            ->withHeader('Content-Type', 'Content-Type: application/json')
            ->withBody($this->streamFactory->createStream(
                $this->jsonService->encode(['message' => $formattedString]),
            ));

        $apiResponse = $this->httpClient->sendRequest($apiRequest);

        //
        //  Decode the API response from JSON and return the data in a DTO
        //
        /**
         * @var array{
         *     message?: string,
         *     metadata?: array{
         *         spam?: bool,
         *         filtered?: bool,
         *         filtered_words?: string[],
         *     },
         * } $apiResponseDecoded
         */
        $apiResponseDecoded = $this->jsonService->decode((string)$apiResponse->getBody());

        return new FilterStringResult(
            original:          $string,
            originalFormatted: $formattedString,
            filtered:          $apiResponseDecoded['message'] ?? null,
            isSpam:            $apiResponseDecoded['metadata']['spam'] ?? null,
            wasFiltered:       $apiResponseDecoded['metadata']['filtered'] ?? null,
            filteredWords:     $apiResponseDecoded['metadata']['filtered_words'] ?? null,
        );
    }
}
