<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubTransaction;

use Ubix\Model\FanClubTransaction;

/**
 * Interface for reading fan club transaction data
 */
interface FanClubTransactionReaderInterface
{
    /**
     * Get all fan club transactions by ID
     *
     * @param int $id The transaction's ID
     *
     * @return FanClubTransaction[] An array of fan club transaction objects
     */
    public function getById(int $id): array;

    /**
     * Get all fan club transactions by platform user ID
     *
     * @param int $userId The transaction's platform user's ID
     *
     * @return FanClubTransaction[] An array of fan club transaction objects
     */
    public function getByUserId(int $userId): array;

    /**
     * Get all fan club transactions by performer ID
     *
     * @param int $performerId The transaction's performer's ID
     *
     * @return FanClubTransaction[] An array of fan club transaction objects
     */
    public function getByModelId(int $performerId): array;
}
