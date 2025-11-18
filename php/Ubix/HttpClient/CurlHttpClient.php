<?php

declare(strict_types=1);

namespace Ubix\HttpClient;

use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\HttpClientException;

/**
 * HTTP client that uses cURL to make requests
 *
 * POSTing JSON example:
 * ```
 * $postingJsonRequest = $requestFactory
 *     ->createRequest('POST', 'https://localhost/example-endpoint/')
 *     ->withHeader('Content-Type', 'Content-Type: application/json')
 *     ->withBody($streamFactory->createStream('{"example": "json"}'));
 *
 * $postingJsonResponse = $httpClient->sendRequest($postingJsonRequest);
 *
 * var_dump((string)$postingJsonResponse->getBody());
 * ```
 *
 * POSTing form data example:
 * ```
 * $postingFormDataRequest = $requestFactory
 *     ->createRequest('POST', 'https://localhost/example-endpoint/')
 *     ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
 *     ->withBody($streamFactory->createStream(
 *         http_build_query(['example' => 'data']),
 *     ));
 *
 * $postingFormDataResponse = $httpClient->sendRequest($postingFormDataRequest);
 *
 * var_dump((string)$postingFormDataResponse->getBody());
 * ```
 *
 * @see \Ubix\Tests\HttpClient\CurlHttpClientTest PHPUnit test case
 */
final class CurlHttpClient implements HttpClient
{
    private const TIMEOUT_DEFAULT = 5; // In seconds

    /**
     * Constructor
     *
     * @param Logger        $logger        Logger
     * @param StreamFactory $streamFactory Stream factory
     * @param int           $timeout       Timeout for requests in seconds (optional) (default: self::TIMEOUT_DEFAULT)
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private StreamFactory $streamFactory,
        private int $timeout = self::TIMEOUT_DEFAULT,
    ) {
    }

    /**
     * Sends a PSR-7 HTTP request using cURL and return a PSR-7 response
     *
     * @param Request $request PSR request
     *
     * @throws HttpClientException If there is a cURL error
     *
     * @return Response PSR response
     */
    public function sendRequest(Request $request): Response
    {
        //
        //  Initalize and execute the cURL request
        //
        $method = strtoupper($request->getMethod());
        assert($method !== '');

        $ch = curl_init((string)$request->getUri()); // phpcs:ignore Generic.PHP.ForbiddenFunctions -- This is the only place in our code we allow curl_init, otherwise developers must be using \Ubix\HttpClient\CurlHttpClient::sendRequest()
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => array_values($this->normalizeHeaders($request)),
            CURLOPT_POSTFIELDS     => (string)$request->getBody(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
        ]);

        $raw = curl_exec($ch);
        if ($raw === false) {
            throw new HttpClientException('There was a cURL error.', ExceptionCode::CURL_HTTP_CLIENT_ERROR->value);
        }
        assert(is_string($raw));

        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        curl_close($ch);

        //
        //  Build a PSR-7 response
        //
        $response = new Psr7Response($status);

        return $response->withBody(
            $this->streamFactory->createStream($raw),
        );
    }

    /**
     * Converts PSR-7 request headers to cURL-compatible header strings
     *
     * @param Request $request PSR request
     *
     * @return string[] Array of headers formatted for cURL
     */
    private function normalizeHeaders(Request $request): array
    {
        $normalizedHeaders = [];

        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $normalizedHeaders[] = $name . ': ' . $value;
            }
        }

        return $normalizedHeaders;
    }
}
