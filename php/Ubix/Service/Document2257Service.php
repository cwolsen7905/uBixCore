<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Document2257UbixApiRequestData;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to handle 2257 documents
 *
 * @see \Ubix\Tests\Service\Document2257ServiceTest PHPUnit test case
 */
final class Document2257Service
{
    /**
     * Constructor
     *
     * @param Logger         $logger                 Logger
     * @param HttpClient     $httpClient             HTTP client
     * @param RequestFactory $requestFactory         Request factory
     * @param StreamFactory  $streamFactory          Stream factory
     * @param Base64Service  $base64Service          Base64 service
     * @param JsonService    $jsonService            JSON service
     * @param string         $bearerToken            The bearer token to use for API calls
     * @param string         $apiProtocolAndHostname The API protocol and hostname
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private StreamFactory $streamFactory,
        private Base64Service $base64Service,
        private JsonService $jsonService,
        private string $bearerToken,
        private string $apiProtocolAndHostname,
    ) {
    }

    /**
     * Get the contents of an image of a 2257 document
     *
     * @param Document2257UbixApiRequestData $requestData The API request details
     *
     * @throws Exception If the API response contains an error
     *
     * @return string The image contents
     */
    public function getImage(Document2257UbixApiRequestData $requestData): string
    {
        //
        //  Call the VSM internal filter API
        //
        $apiUrl = $this->apiProtocolAndHostname . '/broadcasting/2257-img-fill-and-sign.php';

        $apiPostData = [
            'data' => [
                'All Other Names (1)'            => $requestData->allOtherNames[0] ?? '',
                'All Other Names (2)'            => $requestData->allOtherNames[1] ?? '',
                'All Other Names (3)'            => $requestData->allOtherNames[2] ?? '',
                'Begin Date'                     => $requestData->beginDate,
                'Date of Birth'                  => $requestData->dateOfBirth,
                'Form of Identification'         => $requestData->formOfIdentification,
                'ID #'                           => $requestData->idNumber,
                'Issuing Authority'              => $requestData->issuingAuthority,
                'Legal First Name'               => $requestData->legalFirstName,
                'Legal Last Name'                => $requestData->legalLastName,
                'model_id'                       => $requestData->modelId,
                'Primary Producer'               => $requestData->primaryProducer,
                'signature_name'                 => $requestData->signature,
                'Stage Name for This Production' => $requestData->stageNameForThisProduction,
                'Today\'s Date'                  => $requestData->todaysDate,
            ],
        ];

        $apiRequest = $this->requestFactory
            ->createRequest('POST', $apiUrl)
            ->withHeader('Authorization', 'Bearer ' . $this->bearerToken)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($this->streamFactory->createStream(http_build_query($apiPostData)));

        $apiResponse = $this->httpClient->sendRequest($apiRequest);

        //
        //  Decode the API response from JSON and return the PDF document contents
        //
        /**
         * @var array{
         *     code?: int,
         *     message?: string,
         *     data?: array{
         *         document?: string,
         *     },
         * } $apiResponseDecoded
         */
        $apiResponseDecoded = $this->jsonService->decode((string)$apiResponse->getBody());

        if (
            !array_key_exists('data', $apiResponseDecoded)
            || !array_key_exists('document', $apiResponseDecoded['data'])
            || trim($apiResponseDecoded['data']['document']) === ''
            || (array_key_exists('code', $apiResponseDecoded) && $apiResponseDecoded['code'] !== 0)
        ) {
            throw new Exception(
                array_key_exists('code', $apiResponseDecoded) ? 'There was an error processing the document (error code #' . (string)$apiResponseDecoded['code'] . ')' : 'There was an error processing the document',
                ExceptionCode::DOCUMENT_2257_VSM_API_BAD_RESPONSE->value,
            );
        }

        return $this->base64Service->decode($apiResponseDecoded['data']['document']);
    }
}
