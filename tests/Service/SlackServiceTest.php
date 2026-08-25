<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\JsonService;
use Ubix\Service\SlackService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\Service\SlackService
 *
 * @coversDefaultClass \Ubix\Service\SlackService
 * @coversDefaultClass \Ubis\Service\SlackService
 */
final class SlackServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const WHITELISTED_CHANNEL = 'databases';

    private const API_ENDPOINT = 'https://slack.example/api';

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SlackService::class);
    }

    /**
     * Rejects an empty message before contacting the API.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelRejectsEmptyMessage(): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(ExceptionCode::MISSING_SLACK_MESSAGE->value);

        $this->service($httpClient)->sendToChannel('   ', self::WHITELISTED_CHANNEL);
    }

    /**
     * Rejects a channel that is not on the white list.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelRejectsNonWhitelistedChannel(): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(ExceptionCode::SLACK_CHANNEL_NOT_WHITELISTED->value);

        $this->service($httpClient)->sendToChannel('hello', 'not-a-real-channel');
    }

    /**
     * Posts the message to the Slack API when the response is successful.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelSendsMessageOnSuccess(): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->once())->method('sendRequest')->willReturn(new Psr7Response(200, [], 'ok'));

        $this->service($httpClient)->sendToChannel('hello', self::WHITELISTED_CHANNEL);
    }

    /**
     * Throws when the Slack API does not return a successful response.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelThrowsOnApiError(): void
    {
        $httpClient = $this->createStub(HttpClient::class);
        $httpClient->method('sendRequest')->willReturn(new Psr7Response(200, [], 'invalid_channel'));

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::SLACK_API_ERROR->value);

        $this->service($httpClient)->sendToChannel('hello', self::WHITELISTED_CHANNEL);
    }

    /**
     * Silently skips the API call when a matching message is already in
     * the duplicate-prevention cache.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelSkipsDuplicateWhenCached(): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $cache = $this->createStub(SimpleCache::class);
        $cache->method('get')->willReturn('_');

        $this->service($httpClient, $cache)->sendToChannel('hello', self::WHITELISTED_CHANNEL);
    }

    /**
     * Build SlackService with real PSR-17 factories and JSON service; only
     * the external HTTP boundary and the cache store are doubled, per the
     * unit-testing policy.
     *
     * @param HttpClient   $httpClient HTTP client (external boundary)
     * @param ?SimpleCache $cache      Cache store (defaults to an always-miss stub)
     *
     * @return SlackService
     */
    private function service(HttpClient $httpClient, ?SimpleCache $cache = null): SlackService
    {
        $logger       = $this->createStub(Logger::class);
        $psr17Factory = new Psr17Factory();

        return new SlackService(
            $logger,
            $httpClient,
            $psr17Factory,
            $psr17Factory,
            $cache ?? $this->createStub(SimpleCache::class),
            new JsonService($logger),
            self::API_ENDPOINT,
        );
    }
}
