<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Exception;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to interact with XVT and its API
 *
 * @see \Ubix\Tests\Service\XvtServiceTest PHPUnit test case
 */
final class XvtService
{
    public const API_TIMEOUT = 1000; // In seconds

    private const API_PROTOCOL_AND_HOSTNAME_XNXX    = 'https://www.xnxx.com';
    private const API_PROTOCOL_AND_HOSTNAME_XVIDEOS = 'https://www.xvideos.com';

    private const CODE_2FA_CODE_NEEDED = 119;

    private const DOMAINS_XNXX = [
        'cams.xnxx.com',
        'xnxx-cams.com',
        'xnxx.com',
    ];

    private const LANGUAGE_DEFAULT = 'en';

    private const RANDOM_IP_ADDRESSES = [
        '204.8.234.0/24',
        '4.35.153.128/25',
    ];

    private const SIMPLE_CACHE_KEY_LAST_RANDOM_IP_ADDRESS = 'XvtService_RandomIpAddress';
    private const SIMPLE_CACHE_TTL_LAST_RANDOM_IP_ADDRESS = 3600; // 3600 = 60 * 60

    private const STATUS_SUCCESS = 'active';

    /**
     * Constructor
     *
     * @param Logger            $logger            Logger
     * @param HttpClient        $httpClient        HTTP client
     * @param RequestFactory    $requestFactory    Request factory
     * @param StreamFactory     $streamFactory     Stream factory
     * @param JsonService       $jsonService       JSON service
     * @param NetworkingService $networkingService Networking service
     * @param SimpleCache       $simpleCache       Simple cache
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private StreamFactory $streamFactory,
        private JsonService $jsonService,
        private NetworkingService $networkingService,
        private SimpleCache $simpleCache,
    ) {
    }

    /**
     * Authenticate a user via the XVT API
     *
     * @param string  $username                    The username
     * @param string  $password                    The password
     * @param string  $originatingDomain           The domain the authentication is originating from
     * @param string  $ipAddress                   The user's IP address
     * @param ?string $twoFactorAuthenticationCode The 2fa code (optional) (default: null)
     * @param string  $language                    The user's language (optional) (default: 'en')
     *
     * @throws Exception If the API times out
     *
     * @return bool Whether or not the authentication check was successful
     */
    public function authenticate(
        string $username,
        string $password,
        string $originatingDomain,
        string $ipAddress,
        ?string $twoFactorAuthenticationCode = null,
        string $language = self::LANGUAGE_DEFAULT,
    ): bool {
        //
        //  If the request is coming from an internal IP address don't use it and generate a random one instead
        //
        if ($this->networkingService->isInternalIpAddress($ipAddress)) {
            $ipAddress = $this->getRandomIpAddress();
        }

        //
        //  Call the XVT API
        //
        try {
            $apiResponse = $this->apiCall($originatingDomain, 'credentials-login', $language, [
                'api_user_ip'           => $ipAddress,
                'api_user_ip_forwarded' => '',
                'signin-form'           => [
                    'login'      => $username,
                    'password'   => $password,
                    'rememberme' => true,
                ],
            ]);
        } catch (Exception $e) {
            throw new Exception('The XVT API has timed out, please try again in 30 seconds.', ExceptionCode::XVT_API_TIMED_OUT->value, $e);
        }

        //
        //  Decode the API response from JSON and act on it
        //
        /**
         * @var array<string, int|string> $apiResponseDecoded
         */
        $apiResponseDecoded = $this->jsonService->decode($apiResponse);

        //
        //  Handle the API requesting a two factor authentication (2fa) code
        //
        if (isset($apiResponseDecoded['code']) && $apiResponseDecoded['code'] === self::CODE_2FA_CODE_NEEDED) {
            //
            //  If the user has NOT included a two factor authentication code (or auth_data wasn't returned from the API) then throw an exception
            //
            if (empty($apiResponseDecoded['auth_data']) || $twoFactorAuthenticationCode === null) {
                throw new Exception('Check your email for a two factor authentication code.', ExceptionCode::TWO_FACTOR_AUTHENTICATION_MISSING_FOR_XVT->value);
            }

            //
            //  Perform a second API call to verify the two factor authentication code
            //
            try {
                // TEMPORARY: this *should* work but is untested as I haven't been able to induce it - I keep getting authenticated without a 2fa request using the `joesantia.vstest@gmail.com` account
                $secondApiResponse = $this->apiCall($originatingDomain, 'totp-verify-code', $language, [
                    'api_user_ip'           => $ipAddress,
                    'api_user_ip_forwarded' => '',
                    'totp-auth'             => [
                        'auth-data' => $apiResponseDecoded['auth_data'],
                        'code'      => $twoFactorAuthenticationCode,
                    ],
                ]);

                /**
                 * @var array<string, int|string> $secondApiResponseDecoded
                 */
                $secondApiResponseDecoded = $this->jsonService->decode($secondApiResponse);

                //
                //  If the status came back indicating a success return true or else throw an exception
                //
                if (isset($secondApiResponseDecoded['status']) && $secondApiResponseDecoded['status'] === self::STATUS_SUCCESS) {
                    return true;
                } else {
                    throw new Exception('The two factor authentication code submitted is invalid.', ExceptionCode::TWO_FACTOR_AUTHENTICATION_INVALID_FOR_XVT->value);
                }
            } catch (Exception $e) {
                throw new Exception('The XVT API has timed out, please try again in 30 seconds.', ExceptionCode::XVT_API_TIMED_OUT->value, $e);
            }
        }

        //
        //  If the status came back indicating a success return true or else throw an exception
        //
        return isset($apiResponseDecoded['status']) && $apiResponseDecoded['status'] === self::STATUS_SUCCESS;
    }

    /**
     * Call the XVT API
     *
     * @param string               $originatingDomain The domain the API call is originating from
     * @param string               $action            The API action
     * @param string               $language          The user's language
     * @param array<string, mixed> $postData          The post data to send in the API call
     *
     * @return string The API response
     */
    private function apiCall(string $originatingDomain, string $action, string $language, array $postData = []): string
    {
        //
        //  The API URL must be generated with 'SEC_TOKEN' as a placeholder for the security token before generating the actual security token (their design choice, not ours)
        //
        $apiUrl = '/cams/auth/SEC_TOKEN/' . $language . '/' . $action;
        $apiUrl = str_replace(
            '/SEC_TOKEN/',
            '/' . $this->generateSecurityToken($apiUrl) . '/',
            $apiUrl,
        );
        $apiUrl = $this->getApiProtocolAndHostname($originatingDomain) . $apiUrl;

        //
        //  Do a POST to the XVT API and return the response
        //
        $apiRequest = $this->requestFactory
            ->createRequest('POST', $apiUrl)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($this->streamFactory->createStream(http_build_query($postData)));

        $apiResponse = $this->httpClient->sendRequest($apiRequest);

        return (string)$apiResponse->getBody();
    }

    /**
     * Generate a security token based on an API URI
     *
     * @param string $uri The API URI
     *
     * @return string The generated security token
     */
    private function generateSecurityToken(string $uri): string
    {
        $timestamp = (new DateTime())->format('U');

        return hash('sha256', $timestamp . '+' . $uri . '+' . getenv('XVT_KEY')) . $timestamp;
    }

    /**
     * Get the API's protocol and hostname
     *
     * @param string $originatingDomain The domain the API call is originating from
     *
     * @return string The API's protocol and hostname
     */
    private function getApiProtocolAndHostname(string $originatingDomain): string
    {
        //
        //  If the originating domain is one of the XNXX domains then return the XNXX API protocol and hostname otherwise fallback to the XVideos API protocol and hostname
        //
        return in_array($originatingDomain, self::DOMAINS_XNXX, true) ? self::API_PROTOCOL_AND_HOSTNAME_XNXX : self::API_PROTOCOL_AND_HOSTNAME_XVIDEOS;
    }

    /**
     * Generate a random IP address
     *
     * @return string The random IP address
     */
    private function getRandomIpAddress(): string
    {
        $randomIpAddress = null;

        //
        //  Check the cache for the last random IP address that was generated
        //
        $lastGenerated = $this->simpleCache->get(self::SIMPLE_CACHE_KEY_LAST_RANDOM_IP_ADDRESS);

        //
        //  Generate a new random IP address based on the last one that was generated
        //
        if ($lastGenerated === null || !filter_var($lastGenerated, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) { // There is no record of the last random IP address generated (or the record is not a valid IP address) so start from the beginning
            $randomIpAddress = $this->networkingService->getFirstUsableIpAddressInCidrRange(self::RANDOM_IP_ADDRESSES[0]);
        } else {
            assert(is_string($lastGenerated));

            foreach (self::RANDOM_IP_ADDRESSES as $i => $randomIpAddressRange) {
                if ($this->networkingService->isIpv4AddressInCidrRange($lastGenerated, $randomIpAddressRange)) {
                    try {
                        $randomIpAddress = $this->networkingService->getNextUsableIpAddressInCidrRange(
                            $randomIpAddressRange,
                            $lastGenerated,
                        );
                    } catch (Exception $e) { // Exception will be thrown if there is no next usable IP address in the CIDR range
                        $randomIpAddress = $this->networkingService->getFirstUsableIpAddressInCidrRange(
                            self::RANDOM_IP_ADDRESSES[isset(self::RANDOM_IP_ADDRESSES[$i + 1]) ? $i + 1 : 0], // If there is a next range then use it otherwise start back at the beginning
                        );
                    }

                    break; // We have our random IP address so break out of the foreach loop immedaitely
                }
            }

            if ($randomIpAddress === null) { // The $lastGenerated IP address wasn't found in any existing range so start back at the beginning
                $randomIpAddress = $this->networkingService->getFirstUsableIpAddressInCidrRange(self::RANDOM_IP_ADDRESSES[0]);
            }
        }

        //
        //  Cache the random IP address
        //
        $this->simpleCache->set(
            self::SIMPLE_CACHE_KEY_LAST_RANDOM_IP_ADDRESS,
            $randomIpAddress,
            self::SIMPLE_CACHE_TTL_LAST_RANDOM_IP_ADDRESS,
        );

        //
        //  Return the random IP address
        //
        return $randomIpAddress;
    }
}
