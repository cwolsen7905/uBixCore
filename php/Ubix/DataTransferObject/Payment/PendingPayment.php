<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Payment;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * What a page needs to confirm a payment or save a card with the provider's embedded form
 *
 * @see \Ubix\Tests\DataTransferObject\Payment\PendingPaymentTest PHPUnit test case
 */
final readonly class PendingPayment implements Dto
{
    /**
     * Constructor
     *
     * @param string $providerReference The provider's id for the subscription, payment or setup being confirmed
     * @param string $clientSecret      The secret the embedded form confirms with; hand it to the page that shows the form and nowhere else
     */
    public function __construct(
        public readonly string $providerReference = '',
        public readonly string $clientSecret = '',
    ) {
    }
}
