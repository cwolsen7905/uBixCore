<?php

declare(strict_types=1);

namespace Ubix\Service\Realtime;

use RuntimeException;

/**
 * Publishes real-time events to subscribers (browser gateways, workers)
 *
 * The seam a host calls after it has written the row an event is about.
 * Delivery is at-most-once: a subscriber that misses an event recovers by
 * re-reading the host's own API, so an event is a nudge, never the only copy
 * of anything. That is also why a host should catch a failure here and log
 * it rather than fail the request that caused it.
 */
interface RealtimePublisherServiceInterface
{
    /**
     * Publish one event
     *
     * @param string               $subject A concrete, dot-separated subject (no wildcards)
     * @param array<string, mixed> $payload JSON-encodable event body
     *
     * @throws RuntimeException When the subject is invalid, the payload cannot be encoded, or the server refuses or cannot be reached
     *
     * @return void
     */
    public function publish(string $subject, array $payload): void;
}
