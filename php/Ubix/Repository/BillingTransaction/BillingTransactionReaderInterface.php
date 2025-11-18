<?php

declare(strict_types=1);

namespace Ubix\Repository\BillingTransaction;

use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\Model\Transaction;

/**
 * Interface for reading broadcaster data
 */
interface BillingTransactionReaderInterface
{
    /**
     * Get the most recent transaction by user ID
     *
     * @param PlatformUserId $userId The user's ID
     *
     * @return Transaction An array containing the transaction details
     */
    public function getMostRecentByUserId(PlatformUserId $userId): Transaction;

        /**
         * Get the most recent transaction by user ID
         *
         * @param PlatformUserId $userId   The user's ID
         * @param UbixDateTime    $dateTime The date to compare against in 'Y-m-d H:i:s' format
         *
         * @return Transaction An array containing the transaction details
         */
    public function getMostRecentByUserIdBeforeDate(PlatformUserId $userId, UbixDateTime $dateTime): Transaction;
}
