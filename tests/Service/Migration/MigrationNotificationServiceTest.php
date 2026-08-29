<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use DateTimeImmutable;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Ubix\DataTransferObject\Migration\AppliedMigration;
use Ubix\Enum\Env;
use Ubix\Service\JsonService;
use Ubix\Service\Migration\MigrationNotificationService;
use Ubix\Service\SlackService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Migration\MigrationNotificationService
 *
 * @coversDefaultClass \Ubix\Service\Migration\MigrationNotificationService
 * @coversDefaultClass \Ubis\Service\Migration\MigrationNotificationService
 */
final class MigrationNotificationServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const API_ENDPOINT = 'https://slack.example/api';

    /**
     * Captured outbound HTTP request bodies, one per Slack API call this test made.
     *
     * @var string[]
     */
    private array $capturedBodies = [];

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationNotificationService::class);
    }

    /**
     * Posts a single Slack message to #databases with a header, one bullet per
     * migration and the actor footer when applying on a notified environment.
     *
     * @return void
     *
     * @covers ::notifyApplied
     */
    public function testNotifyAppliedPostsHeaderBulletsAndFooter(): void
    {
        $applied = [
            $this->migration('2026_01_01_000000_create_widgets', 'flirt4free', 'cli:chris'),
            $this->migration('2026_01_02_000000_alter_widgets', 'SYSTEMS', 'cli:chris'),
        ];

        $this->service($this->capturingHttpClient())->notifyApplied(Env::DEV, $applied);

        $this->assertCount(1, $this->capturedBodies);
        $message = $this->decodeSlackMessage($this->capturedBodies[0]);

        $expected = implode(PHP_EOL, [
            '*Migrations applied — DEV* (2)',
            '• 2026_01_01_000000_create_widgets (flirt4free)',
            '• 2026_01_02_000000_alter_widgets (SYSTEMS)',
            'by `cli:chris`',
        ]);
        $this->assertSame($expected, $message);
    }

    /**
     * Does not contact Slack at all when applying on the sandbox environment.
     *
     * @return void
     *
     * @covers ::notifyApplied
     */
    public function testNotifyAppliedSkipsSandbox(): void
    {
        $this->service($this->capturingHttpClient())->notifyApplied(
            Env::SANDBOX,
            [$this->migration('2026_01_01_000000_create_widgets', 'flirt4free', 'cli:chris')],
        );

        $this->assertSame([], $this->capturedBodies);
    }

    /**
     * Does not contact Slack at all when applying on the test environment.
     *
     * @return void
     *
     * @covers ::notifyApplied
     */
    public function testNotifyAppliedSkipsTest(): void
    {
        $this->service($this->capturingHttpClient())->notifyApplied(
            Env::TEST,
            [$this->migration('2026_01_01_000000_create_widgets', 'flirt4free', 'cli:chris')],
        );

        $this->assertSame([], $this->capturedBodies);
    }

    /**
     * Does not contact Slack when nothing applied, even on a notified environment.
     *
     * @return void
     *
     * @covers ::notifyApplied
     */
    public function testNotifyAppliedSkipsWhenNothingApplied(): void
    {
        $this->service($this->capturingHttpClient())->notifyApplied(Env::PROD, []);

        $this->assertSame([], $this->capturedBodies);
    }

    /**
     * Swallows a Slack transport failure so a notification outage never fails
     * the migration command, and logs the error instead.
     *
     * @return void
     *
     * @covers ::notifyApplied
     */
    public function testNotifyAppliedSwallowsSlackFailure(): void
    {
        $httpClient = $this->createStub(HttpClient::class);
        $httpClient->method('sendRequest')->willReturn(new Psr7Response(200, [], 'invalid_channel'));

        $logger = $this->createMock(Logger::class);
        $logger->expects($this->once())->method('error');

        $service = $this->service($httpClient, $logger);
        $service->notifyApplied(
            Env::PROD,
            [$this->migration('2026_01_01_000000_create_widgets', 'flirt4free', 'cli:chris')],
        );

        $this->assertSame([], $this->capturedBodies);
    }

    /**
     * Posts a reconciliation message naming the environment and noting the file
     * was recorded as applied without running.
     *
     * @return void
     *
     * @covers ::notifyReconciled
     */
    public function testNotifyReconciledPostsReconciledMessage(): void
    {
        $reconciled = $this->migration('2026_01_03_000000_hotfix_index', 'VSCASH', 'manual:chris+destructive-ack');

        $this->service($this->capturingHttpClient())->notifyReconciled(Env::STAGING, $reconciled);

        $this->assertCount(1, $this->capturedBodies);
        $message = $this->decodeSlackMessage($this->capturedBodies[0]);

        $expected = implode(PHP_EOL, [
            '*Migration reconciled — STAGING* (recorded as applied without running)',
            '• 2026_01_03_000000_hotfix_index (VSCASH)',
            'by `manual:chris+destructive-ack`',
        ]);
        $this->assertSame($expected, $message);
    }

    /**
     * Does not contact Slack when reconciling on a non-notified environment.
     *
     * @return void
     *
     * @covers ::notifyReconciled
     */
    public function testNotifyReconciledSkipsSandbox(): void
    {
        $this->service($this->capturingHttpClient())->notifyReconciled(
            Env::SANDBOX,
            $this->migration('2026_01_03_000000_hotfix_index', 'VSCASH', 'manual:chris+destructive-ack'),
        );

        $this->assertSame([], $this->capturedBodies);
    }

    /**
     * Build an HTTP client stub that records each outbound request body and
     * returns a successful Slack response.
     *
     * @return HttpClient
     */
    private function capturingHttpClient(): HttpClient
    {
        $httpClient = $this->createStub(HttpClient::class);
        $httpClient->method('sendRequest')->willReturnCallback(
            function (Request $request): Psr7Response {
                $this->capturedBodies[] = (string)$request->getBody();

                return new Psr7Response(200, [], 'ok');
            },
        );

        return $httpClient;
    }

    /**
     * Decode the Slack `text` field out of a captured urlencoded request body.
     *
     * @param string $body The captured request body
     *
     * @return string The Slack message text
     */
    private function decodeSlackMessage(string $body): string
    {
        parse_str($body, $parsed);
        $rawPayload = $parsed['payload'] ?? '';
        $this->assertIsString($rawPayload);

        $payload = (new JsonService($this->createStub(Logger::class)))->decode($rawPayload);
        $text    = $payload['text'] ?? '';
        $this->assertIsString($text);

        return $text;
    }

    /**
     * Build an AppliedMigration with the fields the notifier reads, leaving the
     * rest at representative defaults.
     *
     * @param string $id             Migration ID
     * @param string $targetDatabase Target database
     * @param string $appliedBy      Actor identifier
     *
     * @return AppliedMigration
     */
    private function migration(string $id, string $targetDatabase, string $appliedBy): AppliedMigration
    {
        return new AppliedMigration(
            $id,
            $targetDatabase,
            'A representative migration description',
            str_repeat('a', 64),
            new DateTimeImmutable('2026-06-15 12:00:00'),
            $appliedBy,
            42,
        );
    }

    /**
     * Build a MigrationNotificationService wrapping a real SlackService whose
     * only doubled collaborators are the external HTTP boundary and the cache,
     * per the unit-testing policy.
     *
     * @param HttpClient $httpClient HTTP client (external boundary)
     * @param ?Logger    $logger     Logger (defaults to a stub)
     *
     * @return MigrationNotificationService
     */
    private function service(HttpClient $httpClient, ?Logger $logger = null): MigrationNotificationService
    {
        $logger     ??= $this->createStub(Logger::class);
        $psr17Factory = new Psr17Factory();

        $slackService = new SlackService(
            $this->createStub(Logger::class),
            $httpClient,
            $psr17Factory,
            $psr17Factory,
            $this->createStub(SimpleCache::class),
            new JsonService($this->createStub(Logger::class)),
            self::API_ENDPOINT,
        );

        return new MigrationNotificationService($logger, $slackService);
    }
}
