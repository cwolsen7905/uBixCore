<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a webhook whose signature has already been verified
 *
 * Returned by {@see \Ubix\Service\Payment\PaymentProviderServiceInterface::verifyWebhook()}.
 *
 * **The existence of this DTO is the security control.** A webhook endpoint is
 * an unauthenticated, publicly-reachable URL that tells a host money moved, so
 * anyone on the internet can POST an invented payment to it. The only thing
 * separating a real event from a forged one is the provider's signature, and
 * the only way to make that check impossible to forget is to make verified
 * event data a *different type* from an inbound request body. A handler that
 * takes this DTO cannot be handed an unverified payload; there is no
 * constructor path to one that skips the check.
 *
 * For the same reason the raw body is never carried here: a caller holding this
 * DTO has no way to accidentally re-parse the unverified bytes.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\VerifiedWebhookEventTest PHPUnit test case
 */
final readonly class VerifiedWebhookEvent implements Dto
{
    /**
     * Constructor
     *
     * @param string               $providerEventId        The provider's own id for this event; the idempotency key -- providers retry deliveries, so the same event id will arrive more than once and a host must make the second arrival a no-op
     * @param string               $type                   The provider's event type string (for example `checkout.session.completed`); a host dispatches on this and must ignore, not fail on, a type it does not recognise, since providers add event types without notice
     * @param array<string, mixed> $payload                The verified event body, decoded; structure is the provider's, so a host reads it through whatever mapping it keeps for that provider
     * @param int                  $createdAtUnixTimestamp When the provider recorded the event; used to detect a replayed delivery that is too old to be plausible
     */
    public function __construct(
        public readonly string $providerEventId = '',
        public readonly string $type = '',
        public readonly array $payload = [],
        public readonly int $createdAtUnixTimestamp = 0,
    ) {
    }
}
