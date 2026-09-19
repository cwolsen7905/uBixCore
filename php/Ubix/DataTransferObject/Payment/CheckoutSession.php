<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a hosted checkout session a payer can be sent to
 *
 * Returned by {@see \Ubix\Service\Payment\PaymentProviderInterface::createOneOffCheckout()}
 * and {@see \Ubix\Service\Payment\PaymentProviderInterface::createSubscriptionCheckout()}.
 *
 * The URL points at the provider's own hosted payment page, never at the host's
 * application. That is the entire point of this DTO's shape: if a host only ever
 * receives a URL to redirect to, it has no opportunity to build a card form of
 * its own, and the card details it never sees are card details it cannot leak.
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\CheckoutSessionTest PHPUnit test case
 */
final readonly class CheckoutSession implements Dto
{
    /**
     * Constructor
     *
     * @param string $url                    The provider-hosted payment page to redirect the payer to
     * @param string $providerSessionId      The provider's own id for this checkout session; a host records it so a later webhook naming the same session can be matched to whatever the host provisionally recorded when it created the session
     * @param int    $expiresAtUnixTimestamp Unix timestamp after which the hosted page stops accepting payment; a host that wants to re-offer checkout past this point creates a new session rather than re-using this URL
     */
    public function __construct(
        public readonly string $url = '',
        public readonly string $providerSessionId = '',
        public readonly int $expiresAtUnixTimestamp = 0,
    ) {
    }
}
