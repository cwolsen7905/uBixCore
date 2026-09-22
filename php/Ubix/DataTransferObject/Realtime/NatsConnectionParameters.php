<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Realtime;

use SensitiveParameter;
use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for connecting a publisher to a NATS server
 *
 * @see \Ubix\Service\Realtime\NatsRealtimePublisherService
 * @see \Ubix\Tests\DataTransferObject\Realtime\NatsConnectionParametersTest PHPUnit test case
 */
final readonly class NatsConnectionParameters implements Dto
{
    /**
     * Constructor
     *
     * @param string  $url            Server URL, `nats://host:port`
     * @param ?string $user           Username, or null for a server without auth
     * @param ?string $password       Password for `user`
     * @param string  $name           Client name the server shows in its connection list
     * @param float   $timeoutSeconds Connect and read timeout
     */
    public function __construct(
        public readonly string $url = 'nats://127.0.0.1:4222',
        public readonly ?string $user = null,
        #[SensitiveParameter]
        public readonly ?string $password = null,
        public readonly string $name = 'ubix-publisher',
        public readonly float $timeoutSeconds = 2.0,
    ) {
    }
}
