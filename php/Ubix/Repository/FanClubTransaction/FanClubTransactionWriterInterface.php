<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubTransaction;

use Ubix\Model\FanClubTransaction;

/**
 * Interface for writing fan club transaction data
 */
interface FanClubTransactionWriterInterface
{
    /**
     * Save a fan club transaction
     *
     * @param FanClubTransaction $transaction The fan club transaction to save
     *
     * @return void
     */
    public function save(FanClubTransaction $transaction): void;
}
