<?php

declare(strict_types=1);

namespace Ubix\Service;

use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to post a message to a Discord channel through an incoming webhook
 *
 * The counterpart to {@see SlackService}, and different in one structural way: Slack
 * takes one API endpoint plus a channel name, while a Discord webhook URL **is** the
 * channel. So the host supplies a name → URL map and this service looks the channel up;
 * an unmapped name is a programming error rather than something to route elsewhere.
 *
 * Example:
 * ```
 * $discordService->sendToChannel(
 *     message: 'A video failed to package.',
 *     channel: 'alerts',
 * );
 * ```
 *
 * **A failed delivery does not throw.** An alert is almost never the caller's actual
 * work — it is a cron sweep or a deploy script saying what happened — and a notifier
 * that throws turns "we could not tell you about the failure" into a second, louder
 * failure of the thing that was otherwise fine. So a transport error or a rejection
 * from Discord is logged and reported as `false`. What *does* throw is a call that
 * could never work: an empty message, or a channel the host never configured. Those
 * are wrong in the source, not at runtime.
 *
 * @see \Ubix\Tests\Service\DiscordServiceTest PHPUnit test case
 */
final class DiscordService
{
    /**
     * Discord rejects a message body longer than this many characters
     */
    private const int CONTENT_LIMIT = 2000;

    /**
     * Appended to a message trimmed to fit `CONTENT_LIMIT`
     */
    private const string TRUNCATION_SUFFIX = '… (truncated)';

    /**
     * Constructor
     *
     * @param Logger                $logger          Logger
     * @param HttpClient            $httpClient      HTTP client
     * @param RequestFactory        $requestFactory  Request factory
     * @param StreamFactory         $streamFactory   Stream factory
     * @param JsonService           $jsonService     JSON service
     * @param array<string, string> $channelWebhooks Channel name → incoming-webhook URL, supplied by the host
     * @param ?string               $username        Overrides the webhook's own name in Discord (optional)
     */
    public function __construct(
        private Logger $logger,
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private StreamFactory $streamFactory,
        private JsonService $jsonService,
        private array $channelWebhooks = [],
        private ?string $username = null,
    ) {
    }

    /**
     * Post a message to one of the host's configured channels
     *
     * @param string $message The message; trimmed to Discord's 2,000-character limit
     * @param string $channel The channel name, as the host's map spells it
     *
     * @throws InvalidArgumentException If the message is empty or the channel is not configured
     *
     * @return bool Whether Discord accepted it — false for a transport error or a rejection, both logged
     */
    public function sendToChannel(string $message, string $channel): bool
    {
        if (trim($message) === '') {
            throw new InvalidArgumentException('You must include a message', ExceptionCode::MISSING_DISCORD_MESSAGE->value);
        }

        $webhookUrl = $this->channelWebhooks[$channel] ?? null;
        if ($webhookUrl === null || trim($webhookUrl) === '') {
            throw new InvalidArgumentException(
                'No Discord webhook is configured for the `' . $channel . '` channel',
                ExceptionCode::DISCORD_CHANNEL_NOT_CONFIGURED->value,
            );
        }

        $payload = ['content' => $this->fit($message)];
        if ($this->username !== null && trim($this->username) !== '') {
            $payload['username'] = $this->username;
        }

        $request = $this->requestFactory
            ->createRequest('POST', $webhookUrl)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($this->jsonService->encode($payload)));

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientException $e) {
            // Deliberately not rethrown: see the class docblock. The channel is named
            // but the message is not, because an alert can carry anything.
            $this->logger->warning('Could not reach Discord', ['channel' => $channel, 'error' => $e->getMessage()]);

            return false;
        }

        $status = $response->getStatusCode();

        // A webhook answers 204 with no body on success; anything outside 2xx is a
        // rejection worth logging with its body, which is where Discord says why.
        if ($status < 200 || $status > 299) {
            $this->logger->warning('Discord rejected a message', [
                'body'    => (string) $response->getBody(),
                'channel' => $channel,
                'status'  => $status,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Whether the host has a webhook for a channel
     *
     * Lets a caller skip building a message it has nowhere to send, and lets a host
     * configure alerting per environment without the callers knowing which.
     *
     * @param string $channel The channel name
     *
     * @return bool
     */
    public function hasChannel(string $channel): bool
    {
        $webhookUrl = $this->channelWebhooks[$channel] ?? null;

        return $webhookUrl !== null && trim($webhookUrl) !== '';
    }

    /**
     * Bring a message inside Discord's character limit
     *
     * @param string $message The message
     *
     * @return string The message, trimmed and marked if it had to be
     */
    private function fit(string $message): string
    {
        if (mb_strlen($message) <= self::CONTENT_LIMIT) {
            return $message;
        }

        return mb_substr($message, 0, self::CONTENT_LIMIT - mb_strlen(self::TRUNCATION_SUFFIX)) . self::TRUNCATION_SUFFIX;
    }
}
