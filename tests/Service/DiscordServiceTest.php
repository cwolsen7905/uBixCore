<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Service\DiscordService;
use Ubix\Service\JsonService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\DiscordService
 *
 * @coversDefaultClass \Ubix\Service\DiscordService
 */
final class DiscordServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const string CHANNEL = 'alerts';

    private const string WEBHOOK_URL = 'https://discord.example/api/webhooks/1/abc';

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DiscordService::class);
    }

    /**
     * Rejects an empty message before contacting Discord.
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
        $this->expectExceptionCode(ExceptionCode::MISSING_DISCORD_MESSAGE->value);

        $this->service($httpClient)->sendToChannel('   ', self::CHANNEL);
    }

    /**
     * Rejects a channel the host never configured, rather than silently dropping it.
     *
     * A dropped alert is worse than a loud one: nobody discovers the missing webhook
     * until the day it was needed.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelRejectsAnUnconfiguredChannel(): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(ExceptionCode::DISCORD_CHANNEL_NOT_CONFIGURED->value);

        $this->service($httpClient)->sendToChannel('hello', 'no-such-channel');
    }

    /**
     * Posts to the channel's own webhook URL, as JSON, and reports success.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelPostsToTheChannelWebhook(): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (Request $request): bool {
                $this->assertSame('POST', $request->getMethod());
                $this->assertSame(self::WEBHOOK_URL, (string) $request->getUri());
                $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
                $this->assertSame('{"content":"a video failed","username":"uBix"}', (string) $request->getBody());

                return true;
            }))
            ->willReturn(new Psr7Response(204));

        $this->assertTrue($this->service($httpClient)->sendToChannel('a video failed', self::CHANNEL));
    }

    /**
     * Reports failure rather than throwing when Discord rejects the message.
     *
     * The caller is a cron sweep or a deploy script; a notifier that throws turns
     * "we could not tell you" into a second failure of the thing that was fine.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelReportsARejectionWithoutThrowing(): void
    {
        $httpClient = $this->createStub(HttpClient::class);
        $httpClient->method('sendRequest')->willReturn(new Psr7Response(404, [], 'unknown webhook'));

        $this->assertFalse($this->service($httpClient)->sendToChannel('hello', self::CHANNEL));
    }

    /**
     * Reports failure rather than throwing when Discord cannot be reached at all.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelReportsATransportErrorWithoutThrowing(): void
    {
        $httpClient = $this->createStub(HttpClient::class);
        $httpClient->method('sendRequest')->willThrowException(new class ('no route to host') extends RuntimeException implements ClientException {
        });

        $this->assertFalse($this->service($httpClient)->sendToChannel('hello', self::CHANNEL));
    }

    /**
     * Trims a message to Discord's limit and says that it did.
     *
     * @return void
     *
     * @covers ::sendToChannel
     */
    public function testSendToChannelTrimsAnOversizedMessage(): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (Request $request): bool {
                $payload = (new JsonService($this->createStub(Logger::class)))->decode((string) $request->getBody());
                $this->assertArrayHasKey('content', $payload);
                $this->assertIsString($payload['content']);
                $this->assertSame(2000, mb_strlen($payload['content']));
                $this->assertStringEndsWith('(truncated)', $payload['content']);

                return true;
            }))
            ->willReturn(new Psr7Response(204));

        $this->assertTrue($this->service($httpClient)->sendToChannel(str_repeat('x', 2500), self::CHANNEL));
    }

    /**
     * Lets a caller ask whether there is anywhere to send, so a host can configure
     * alerting per environment without every caller knowing which.
     *
     * @return void
     *
     * @covers ::hasChannel
     */
    public function testHasChannelAnswersForConfiguredAndUnconfiguredChannels(): void
    {
        $service = $this->service($this->createStub(HttpClient::class));

        $this->assertTrue($service->hasChannel(self::CHANNEL));
        $this->assertFalse($service->hasChannel('no-such-channel'));
        $this->assertFalse($service->hasChannel('blank'));
    }

    /**
     * Build DiscordService with real PSR-17 factories and JSON service; only the
     * external HTTP boundary is doubled, per the unit-testing policy.
     *
     * @param HttpClient $httpClient HTTP client (external boundary)
     *
     * @return DiscordService
     */
    private function service(HttpClient $httpClient): DiscordService
    {
        $logger       = $this->createStub(Logger::class);
        $psr17Factory = new Psr17Factory();

        return new DiscordService(
            $logger,
            $httpClient,
            $psr17Factory,
            $psr17Factory,
            new JsonService($logger),
            [self::CHANNEL => self::WEBHOOK_URL, 'blank' => '   '],
            'uBix',
        );
    }
}
