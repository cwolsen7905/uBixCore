<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUserPayment;

use Ubix\Model\PlatformUserPayment;

/**
 * Interface for reading platform user payment data
 */
interface PlatformUserPaymentReaderInterface
{
    /**
     * Get platform user(s) by ID
     *
     * @param int $userId The platform user's ID
     *
     * @return PlatformUserPayment[] An array of matching platform user payment(s)
     */
    public function getByUserId(int $userId): array;
}
