<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Realtime;

use Psr\Log\NullLogger;
use RuntimeException;
use Ubix\DataTransferObject\Realtime\NatsConnectionParameters;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Service\JsonService;
use Ubix\Service\Realtime\NatsRealtimePublisherService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Realtime\NatsRealtimePublisherService
 *
 * The publisher talks to one end of a socket pair; the test plays the server
 * on the other end. What the server "says" is written up front (the kernel
 * buffers it), and what the client said is read back afterwards, so the
 * protocol is checked byte for byte with no broker and no threads.
 *
 * @coversDefaultClass \Ubix\Service\Realtime\NatsRealtimePublisherService
 */
final class NatsRealtimePublisherServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const string INFO = 'INFO {"server_id":"test","max_payload":64}' . "\r\n";

    /**
     * The test's end of the pair: the "server"
     *
     * @var array<int, resource>
     */
    private array $server = [];

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(NatsRealtimePublisherService::class);
    }

    /**
     * Test the handshake and a publish on the wire, and that the connection is reused
     *
     * @return void
     */
    public function testHandshakesOnceThenPublishesAndConfirms(): void
    {
        $publisher = $this->publisher(self::INFO . "PONG\r\nPONG\r\nPONG\r\n", new NatsConnectionParameters(user: 'gateway', password: 's3cret', name: 'test-client'));

        $publisher->publish('app.user.42.notify', ['id' => 7]);
        $publisher->publish('app.user.43.notify', ['id' => 8]);

        $sent = $this->sent();
        $this->assertStringStartsWith('CONNECT {', $sent);
        $this->assertStringContainsString('"user":"gateway","pass":"s3cret"', $sent);
        $this->assertStringContainsString('"name":"test-client"', $sent);
        $this->assertStringContainsString("}\r\nPING\r\nPUB app.user.42.notify 8\r\n{\"id\":7}\r\nPING\r\nPUB app.user.43.notify 8\r\n{\"id\":8}\r\nPING\r\n", $sent);
        $this->assertSame(1, substr_count($sent, 'CONNECT '), 'one handshake for both publishes');
    }

    /**
     * Test that no credentials are sent to a server without auth
     *
     * @return void
     */
    public function testSendsNoCredentialsWhenNoneAreConfigured(): void
    {
        $this->publisher(self::INFO . "PONG\r\nPONG\r\n")->publish('a.b', []);

        $this->assertStringNotContainsString('"user"', $this->sent());
    }

    /**
     * Test that a PING from the server while we wait is answered, not mistaken for our PONG
     *
     * @return void
     */
    public function testAnswersServerPingsWhileWaiting(): void
    {
        $this->publisher(self::INFO . "PONG\r\nPING\r\n+OK\r\nPONG\r\n")->publish('a.b', ['x' => 1]);

        $this->assertStringEndsWith("PING\r\nPONG\r\n", $this->sent());
    }

    /**
     * Test that a refused publish (permissions violation) throws
     *
     * @return void
     */
    public function testThrowsWhenTheServerRefusesThePublish(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(ExceptionCode::REALTIME_PUBLISH_FAILED->value);
        $this->expectExceptionMessage("NATS error: -ERR 'Permissions Violation for Publish to \"a.b\"'");

        $this->publisher(self::INFO . "PONG\r\n-ERR 'Permissions Violation for Publish to \"a.b\"'\r\n")->publish('a.b', []);
    }

    /**
     * Test that a failed login throws from the handshake
     *
     * @return void
     */
    public function testThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("NATS handshake failed: NATS error: -ERR 'Authorization Violation'");

        $this->publisher(self::INFO . "-ERR 'Authorization Violation'\r\n")->publish('a.b', []);
    }

    /**
     * Test that wildcards and malformed subjects are refused before any I/O
     *
     * @return void
     */
    public function testRefusesUnpublishableSubjects(): void
    {
        $publisher = $this->publisher('');

        foreach (['a.*', 'a.>', 'a b', '', 'a..b'] as $subject) {
            try {
                $publisher->publish($subject, []);
                $this->fail('published to "' . $subject . '"');
            } catch (RuntimeException $e) {
                $this->assertSame(ExceptionCode::REALTIME_PUBLISH_FAILED->value, $e->getCode());
            }
        }

        $this->assertSame('', $this->sent(), 'nothing was sent');
    }

    /**
     * Test that a payload over the server's max_payload is refused
     *
     * @return void
     */
    public function testRefusesAPayloadOverTheServerMaximum(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exceeds the server maximum of 64');

        $this->publisher(self::INFO . "PONG\r\n")->publish('a.b', ['x' => str_repeat('y', 64)]);
    }

    /**
     * Test that a server that hangs up mid-handshake is reported, not waited on
     *
     * @return void
     */
    public function testThrowsWhenTheServerHangsUp(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(ExceptionCode::REALTIME_PUBLISH_FAILED->value);
        $this->expectExceptionMessage('NATS handshake failed');

        $publisher = $this->publisher(self::INFO);
        fclose($this->server[0]);
        $this->server = [];

        $publisher->publish('a.b', []);
    }

    /**
     * Test that a publish after a failure reconnects rather than reusing a broken socket
     *
     * @return void
     */
    public function testReconnectsAfterAFailedPublish(): void
    {
        $pairs = [];
        foreach ([self::INFO . "PONG\r\n-ERR 'Permissions Violation'\r\n", self::INFO . "PONG\r\nPONG\r\n"] as $script) {
            $pair = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
            $this->assertIsArray($pair);
            stream_socket_sendto($pair[1], $script);
            $pairs[] = $pair;
        }

        $opener = new class ($pairs) {
            private int $opened = 0;

            /**
             * Constructor
             *
             * @param array<int, array<resource>> $pairs Socket pairs, handed out in order
             */
            public function __construct(
                private array $pairs,
            ) {
            }

            /**
             * How many connections have been opened
             *
             * @return int The count
             */
            public function opened(): int
            {
                return $this->opened;
            }

            /**
             * The next pair's client end
             *
             * @return resource
             */
            public function next()
            {
                $stream        = $this->pairs[$this->opened][0];
                $this->opened += 1;

                return $stream;
            }
        };

        $publisher = new NatsRealtimePublisherService(
            new NullLogger(),
            new JsonService(new NullLogger()),
            new NatsConnectionParameters(timeoutSeconds: 1.0),
            $opener->next(...),
        );

        try {
            $publisher->publish('a.b', []);
            $this->fail('the refused publish did not throw');
        } catch (RuntimeException $e) {
            // Expected: the first connection refuses the publish.
            $this->assertSame(ExceptionCode::REALTIME_PUBLISH_FAILED->value, $e->getCode());
        }

        $publisher->publish('a.b', []);
        $this->assertSame(2, $opener->opened(), 'the second publish opened a fresh connection');

        fclose($pairs[0][1]);
        fclose($pairs[1][1]);
    }

    /**
     * Tear down
     *
     * @return void
     */
    protected function tearDown(): void
    {
        if (isset($this->server[0])) {
            fclose($this->server[0]);
        }

        parent::tearDown();
    }

    /**
     * A publisher wired to a socket pair whose server end has already "said" $script
     *
     * @param string                    $script     What the server sends, in order
     * @param ?NatsConnectionParameters $parameters Connection parameters
     *
     * @return NatsRealtimePublisherService The publisher
     */
    private function publisher(string $script, ?NatsConnectionParameters $parameters = null): NatsRealtimePublisherService
    {
        $pair = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        $this->assertIsArray($pair);
        [$client, $server] = $pair;

        stream_socket_sendto($server, $script);
        $this->server = [$server];

        return new NatsRealtimePublisherService(
            new NullLogger(),
            new JsonService(new NullLogger()),
            $parameters ?? new NatsConnectionParameters(timeoutSeconds: 1.0),
            static function () use ($client) {
                return $client;
            },
        );
    }

    /**
     * Everything the client has written so far
     *
     * @return string The bytes
     */
    private function sent(): string
    {
        stream_set_blocking($this->server[0], false);
        $sent = stream_get_contents($this->server[0]);

        return $sent === false ? '' : $sent;
    }
}
