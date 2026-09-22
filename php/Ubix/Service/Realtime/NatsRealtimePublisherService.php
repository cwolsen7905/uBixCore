<?php

declare(strict_types=1);

namespace Ubix\Service\Realtime;

use Closure;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\DataTransferObject\Realtime\NatsConnectionParameters;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\JsonService;
use Ubix\Service\Realtime\RealtimePublisherServiceInterface as RealtimePublisherService;

/**
 * Publish-only NATS client, speaking the core text protocol over a socket
 *
 * Publishing is five protocol verbs (INFO, CONNECT, PUB, PING, PONG), so this
 * speaks them itself rather than taking a client library whose subscription,
 * request/reply and JetStream machinery a PHP request would never use. No
 * TLS: it is meant for a broker inside the same cluster.
 *
 * Every publish is followed by a PING and waits for the PONG. NATS reports a
 * refused publish (a permissions violation) asynchronously as `-ERR`, so the
 * round trip is what turns "written to a socket" into "accepted by the
 * server". The connection opens on first use and is reused for the rest of
 * the process, so a job publishing thousands of events pays one handshake.
 *
 * @see https://docs.nats.io/reference/reference-protocols/nats-protocol
 * @see \Ubix\Tests\Service\Realtime\NatsRealtimePublisherServiceTest PHPUnit test case
 */
final class NatsRealtimePublisherService implements RealtimePublisherService
{
    /**
     * A concrete subject: dot-separated tokens, no wildcards, no whitespace
     */
    private const string SUBJECT_PATTERN = '/^[A-Za-z0-9_-]+(\.[A-Za-z0-9_-]+)*$/';

    /**
     * The open connection, when there is one
     *
     * An array because PHP cannot type a property as a resource, and the uBix
     * standard forbids untyped properties; slot 0 holds the stream.
     *
     * @var array<int, resource>
     */
    private array $connection = [];

    /**
     * Largest payload the server accepts, from its INFO line
     */
    private int $maxPayload = 1048576;

    /**
     * Constructor
     *
     * @param Logger                   $logger       The Monolog logger
     * @param JsonService              $jsonService  JSON encoding and decoding
     * @param NatsConnectionParameters $parameters   Where and how to connect
     * @param ?Closure                 $streamOpener Opens the socket; tests pass one that returns a socket pair. Default: TCP to the URL's host and port
     */
    public function __construct(
        private Logger $logger,
        private JsonService $jsonService,
        private NatsConnectionParameters $parameters,
        private ?Closure $streamOpener = null,
    ) {
    }

    /**
     * Closes the connection
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Publish one event and wait for the server to accept it
     *
     * @param string               $subject A concrete, dot-separated subject (no wildcards)
     * @param array<string, mixed> $payload JSON-encodable event body
     *
     * @throws RuntimeException When the subject is invalid, the payload cannot be encoded, or the server refuses or cannot be reached
     *
     * @return void
     */
    public function publish(string $subject, array $payload): void
    {
        if (preg_match(self::SUBJECT_PATTERN, $subject) !== 1) {
            throw new RuntimeException('Not a publishable NATS subject: ' . $subject, ExceptionCode::REALTIME_PUBLISH_FAILED->value);
        }

        try {
            $body = $this->jsonService->encode($payload);
        } catch (DtoException $e) {
            throw new RuntimeException('Real-time payload is not JSON-encodable: ' . $e->getMessage(), ExceptionCode::REALTIME_PUBLISH_FAILED->value, $e);
        }

        $stream = $this->stream();

        if (strlen($body) > $this->maxPayload) {
            throw new RuntimeException('Real-time payload of ' . strlen($body) . ' bytes exceeds the server maximum of ' . $this->maxPayload, ExceptionCode::REALTIME_PUBLISH_FAILED->value);
        }

        try {
            $this->write($stream, 'PUB ' . $subject . ' ' . strlen($body) . "\r\n" . $body . "\r\nPING\r\n");
            $this->awaitPong($stream);
        } catch (RuntimeException $e) {
            // The connection is in an unknown state; the next publish reconnects.
            $this->disconnect();

            throw $e;
        }
    }

    /**
     * The open stream, connecting and handshaking first when needed
     *
     * @throws RuntimeException When the server cannot be reached or refuses the connection
     *
     * @return resource
     */
    private function stream()
    {
        if (isset($this->connection[0])) {
            return $this->connection[0];
        }

        $stream = $this->open();

        try {
            $info = $this->readLine($stream);
            if (!str_starts_with($info, 'INFO ')) {
                throw new RuntimeException('Expected INFO from NATS, got: ' . $info, ExceptionCode::REALTIME_PUBLISH_FAILED->value);
            }

            $serverInfo = $this->jsonService->decode(substr($info, 5));
            if (is_int($serverInfo['max_payload'] ?? null)) {
                $this->maxPayload = $serverInfo['max_payload'];
            }

            $connect = [
                'lang'     => 'php',
                'name'     => $this->parameters->name,
                'pedantic' => false,
                'protocol' => 1,
                'verbose'  => false,
                'version'  => 'ubix',
            ];
            if ($this->parameters->user !== null) {
                $connect['user'] = $this->parameters->user;
                $connect['pass'] = $this->parameters->password ?? '';
            }

            $this->write($stream, 'CONNECT ' . $this->jsonService->encode($connect) . "\r\nPING\r\n");
            $this->awaitPong($stream);
        } catch (RuntimeException | DtoException $e) {
            fclose($stream);

            throw new RuntimeException('NATS handshake failed: ' . $e->getMessage(), ExceptionCode::REALTIME_PUBLISH_FAILED->value, $e);
        }

        $this->connection = [$stream];

        return $stream;
    }

    /**
     * Open the socket
     *
     * @throws RuntimeException When it cannot be opened
     *
     * @return resource
     */
    private function open()
    {
        if ($this->streamOpener !== null) {
            $stream = ($this->streamOpener)($this->parameters);
        } else {
            $host = parse_url($this->parameters->url, PHP_URL_HOST);
            $port = parse_url($this->parameters->url, PHP_URL_PORT);
            if (!is_string($host) || $host === '') {
                throw new RuntimeException('NATS URL has no host: ' . $this->parameters->url, ExceptionCode::REALTIME_PUBLISH_FAILED->value);
            }

            $errno   = 0;
            $errstr  = '';
            $address = 'tcp://' . $host . ':' . (is_int($port) ? $port : 4222);
            $stream  = @stream_socket_client($address, $errno, $errstr, $this->parameters->timeoutSeconds); // phpcs:ignore Generic.PHP.NoSilencedErrors -- a refused connection is reported through $errstr and the exception below, not a PHP warning
            if ($stream === false) {
                throw new RuntimeException('Cannot reach NATS at ' . $this->parameters->url . ': ' . $errstr, ExceptionCode::REALTIME_PUBLISH_FAILED->value);
            }
        }

        if (!is_resource($stream)) {
            throw new RuntimeException('The NATS stream opener did not return a stream', ExceptionCode::REALTIME_PUBLISH_FAILED->value);
        }

        $seconds = (int) $this->parameters->timeoutSeconds;
        stream_set_timeout($stream, $seconds, (int) (($this->parameters->timeoutSeconds - $seconds) * 1000000));

        return $stream;
    }

    /**
     * Read lines until the server's PONG, answering its PINGs and failing on -ERR
     *
     * @param resource $stream The connection
     *
     * @throws RuntimeException When the server reports an error or the read fails
     *
     * @return void
     */
    private function awaitPong($stream): void
    {
        while (true) {
            $line = $this->readLine($stream);

            if ($line === 'PONG') {
                return;
            }

            if ($line === 'PING') {
                $this->write($stream, "PONG\r\n");

                continue;
            }

            if (str_starts_with($line, '-ERR')) {
                $this->logger->warning('NATS refused an operation', ['error' => $line]);

                throw new RuntimeException('NATS error: ' . $line, ExceptionCode::REALTIME_PUBLISH_FAILED->value);
            }

            // +OK (verbose mode), INFO updates on cluster topology changes, and
            // anything else are not the answer to our PING; keep reading.
        }
    }

    /**
     * Read one CRLF-terminated protocol line
     *
     * @param resource $stream The connection
     *
     * @throws RuntimeException On EOF or timeout
     *
     * @return string The line without its terminator
     */
    private function readLine($stream): string
    {
        $line = fgets($stream);
        if ($line === false) {
            $meta = stream_get_meta_data($stream);

            throw new RuntimeException(($meta['timed_out'] ? 'Timed out reading from' : 'Connection closed by') . ' NATS', ExceptionCode::REALTIME_PUBLISH_FAILED->value);
        }

        return rtrim($line, "\r\n");
    }

    /**
     * Write all of a buffer
     *
     * @param resource $stream The connection
     * @param string   $data   What to send
     *
     * @throws RuntimeException When the write fails
     *
     * @return void
     */
    private function write($stream, string $data): void
    {
        while ($data !== '') {
            $written = @stream_socket_sendto($stream, $data); // phpcs:ignore Generic.PHP.NoSilencedErrors -- a broken pipe is reported by the return value and the exception below, not a PHP notice
            if ($written === false || $written <= 0) {
                throw new RuntimeException('Write to NATS failed', ExceptionCode::REALTIME_PUBLISH_FAILED->value);
            }

            $data = substr($data, $written);
        }
    }

    /**
     * Close the connection if one is open
     *
     * @return void
     */
    private function disconnect(): void
    {
        if (isset($this->connection[0]) && is_resource($this->connection[0])) {
            fclose($this->connection[0]);
        }

        $this->connection = [];
    }
}
