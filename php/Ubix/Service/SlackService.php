<?php

declare(strict_types=1);

namespace Ubix\Service;

use InvalidArgumentException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Ubix\DataTransferObject\SlackResponse;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;

/**
 * Service to interact with Slack
 *
 * Example:
 * ```
 * $slackService->sendToChannel(
 *     message: 'Hello world!',
 *     channel: '#notification-test',
 * );
 * ```
 *
 * @see \Ubix\Tests\Service\SlackServiceTest PHPUnit test case
 */
final class SlackService
{
    private const CHANNEL_DEFAULT = '#notification-test';

    private const CHANNELS_WHITELIST = [
        'activity',
        'affiliate-alerts',
        'affiliate_team',
        'bc-alerts',
        'bc-rejections',
        'bcnewapplications',
        'billing-alerts',
        'billingupdates',
        'broadcastalerts',
        'broadcasting-processes-monitoring',
        'chat-alerts',
        'chat_system',
        'chatdev',
        'cs-alerts',
        'databases',
        'earnings',
        'email',
        'email-alerts',
        'hacklab-alerts',
        'interactive_usage',
        'it-systems',
        'mbing-alerts',
        'mobilefirstbroadcasts',
        'model-access-monitoring',
        'model-acq-alerts',
        'model-monitor-tool-alert',
        'modelclips',
        'notification-test',
        'pa-alerts',
        'paygen-reports',
        'payments-generation-report',
        'php-alerts',
        'php-pager-duty',
        'reports',
        'risk-alerts',
        'seo-alerts',
        'siteissues',
        'siteupdates',
        'siteupdates-log',
        'stream',
        'test-bc-messages',
        'trafficvolume',
        'uawg-alerts',
        'user-acq-alerts',
        'userdev-alerts',
        'vod',
        'xvtgeneral',
        'yankahacker',
    ];

    private const ICON_DEFAULT = ':robot_face:';

    private const RESPONSE_SUCCESS = 'ok';

    private const SIMPLE_CACHE_KEY_PREFIX = 'slack_msg_';

    private const USERNAME_DEFAULT = 'Flirtbot';

    /**
     * Constructor
     *
     * @param Logger         $logger         Logger
     * @param HttpClient     $httpClient     HTTP client
     * @param RequestFactory $requestFactory Request factory
     * @param StreamFactory  $streamFactory  Stream factory
     * @param SimpleCache    $simpleCache    Simple cache
     * @param JsonService    $jsonService    JSON service
     * @param string         $apiEndpoint    API endpoint
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private StreamFactory $streamFactory,
        private SimpleCache $simpleCache,
        private JsonService $jsonService,
        private string $apiEndpoint,
    ) {
    }

    /**
     * Send a message to a Slack channel
     *
     * Example:
     * ```
     * $slackService->sendToChannel(
     *     message: 'Hello world!',
     *     channel: '#notification-test',
     * );
     * ```
     *
     * @param string          $message               The message
     * @param string[]|string $channel               The channel(s) (optional) (default: self::CHANNEL_DEFAULT)
     * @param string          $username              The username (optional) (default: self::USERNAME_DEFAULT)
     * @param string          $icon                  The icon (optional) (default: self::ICON_DEFAULT)
     * @param ?int            $preventDuplicatesTime The amount of time to prevent duplicate messages from being sent or set to null to disable (optional) (default: null)
     *
     * @throws DtoException If the Slack API returns anything but a successful response
     * @throws InvalidArgumentException If an invalid parameter is passed in
     *
     * @return void
     */
    public function sendToChannel(
        string $message,
        array|string $channel = self::CHANNEL_DEFAULT,
        string $username = self::USERNAME_DEFAULT,
        string $icon = self::ICON_DEFAULT,
        ?int $preventDuplicatesTime = null,
    ): void {
        //
        //  Validate parameters
        //
        if (trim($message) === '') {
            throw new InvalidArgumentException('You must include a message', ExceptionCode::MISSING_SLACK_MESSAGE->value);
        }

        $channels = is_array($channel) ? $channel : [$channel];

        foreach ($channels as $channel) {
            if (trim($channel) === '') {
                throw new InvalidArgumentException('You must include a channel', ExceptionCode::MISSING_SLACK_CHANNEL->value);
            }

            if (!in_array(ltrim($channel, '#'), self::CHANNELS_WHITELIST, true)) {
                throw new InvalidArgumentException('The `' . $channel . '` channel is not in the white list', ExceptionCode::SLACK_CHANNEL_NOT_WHITELISTED->value);
            }
        }

        //
        //  Send the message to each channel
        //
        foreach ($channels as $channel) {
            //
            //  Check the cache to prevent a duplicate message
            //
            $cacheEntry = $this->simpleCache->get($this->getCacheKey($channel, $message));
            if ($cacheEntry !== null) {
                continue; // This duplicate prevention happens silently as per the legacy system
            }

            //
            //  Call the Slack API
            //
            $apiRequest = $this->requestFactory
                ->createRequest('POST', $this->apiEndpoint)
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withBody($this->streamFactory->createStream(http_build_query([
                    'payload' => $this->jsonService->encode([
                        'channel'    => '#' . ltrim($channel, '#'),
                        'icon_emoji' => strpos($icon, 'http') !== false ? '' : $icon,
                        'icon_url'   => strpos($icon, 'http') !== false ? $icon : '',
                        'link_names' => 1,
                        'text'       => $message,
                        'username'   => $username,
                    ]),
                ])));

            $apiResponse = $this->httpClient->sendRequest($apiRequest);

            $dto = new SlackResponse((string)$apiResponse->getBody());
            if ($dto->response !== self::RESPONSE_SUCCESS) {
                throw new DtoException('The Slack API did not return a successful response', ExceptionCode::SLACK_API_ERROR->value, $dto);
            }

            //
            //  If the parameter was passed cache the message to prevent future duplicates
            //
            if ($preventDuplicatesTime !== null && $preventDuplicatesTime > 0) {
                $this->simpleCache->set(
                    $this->getCacheKey($channel, $message),
                    '_',
                    $preventDuplicatesTime,
                );
            }
        }
    }

    /**
     * Get a cache key
     *
     * @param string $channel The channel
     * @param string $message The message
     *
     * @return string The cache key
     */
    private function getCacheKey(string $channel, string $message): string
    {
        return self::SIMPLE_CACHE_KEY_PREFIX . md5($channel . $message);
    }
}
