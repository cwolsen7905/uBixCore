<?php

declare(strict_types=1);

namespace Ubix\Service;

use InvalidArgumentException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to access Google reCAPTCHA
 *
 * @see \Ubix\Tests\Service\RecaptchaServiceTest PHPUnit test case
 */
final class RecaptchaService
{
    private const API_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Constructor
     *
     * @param Logger         $logger         Logger
     * @param HttpClient     $httpClient     HTTP client
     * @param RequestFactory $requestFactory Request factory
     * @param StreamFactory  $streamFactory  Stream factory
     * @param JsonService    $jsonService    JSON service
     * @param string         $publicKey      The public key to use for API calls
     * @param string         $secretKey      The secret key to use for API calls
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private StreamFactory $streamFactory,
        private JsonService $jsonService,
        private string $publicKey, // @phpstan-ignore property.onlyWritten (The public key is needed for some API calls but none that have been coded yet, we still set up both keys so that they are ready when the rest of the API calls are coded)
        private string $secretKey,
    ) {
    }

    /**
     * Validate a captcha
     *
     * @param string $captcha The captcha
     *
     * @throws InvalidArgumentException If an empty string is passed as a captcha
     *
     * @return bool Whether or not the captcha is valid
     */
    public function isValid(string $captcha): bool
    {
        //
        //  Validate parameters
        //
        if (trim($captcha) === '') {
            throw new InvalidArgumentException('You must include a captcha.', ExceptionCode::MISSING_CAPTCHA_FOR_RECAPTCHA->value);
        }

        //
        //  Call the reCAPTCHA API
        //
        $apiRequest = $this->requestFactory
            ->createRequest('POST', self::API_ENDPOINT)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($this->streamFactory->createStream(
                http_build_query(['response' => $captcha, 'secret' => $this->secretKey]),
            ));

        $apiResponse = $this->httpClient->sendRequest($apiRequest);

        //
        //  Decode the API response from JSON and return the data in a DTO
        //
        /**
         * @var array{
         *     success?: bool,
         *     challenge_ts?: string,
         *     hostname?: string,
         * } $apiResponseDecoded
         */
        $apiResponseDecoded = $this->jsonService->decode((string)$apiResponse->getBody());

        return ($apiResponseDecoded['success'] ?? false) === true;
    }
}
