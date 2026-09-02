<?php

declare(strict_types=1);

namespace Ubix\Repository\Tier;

use Ubix\Model\Tier;

/**
 * Writes tiers and their benefits
 */
interface TierWriterInterface
{
    /**
     * Persist a new tier (the new id is set on the model)
     *
     * @param Tier $tier The tier to create
     *
     * @return void
     */
    public function createTier(Tier $tier): void;

    /**
     * Update a tier's editable fields (name, description, price, interval)
     *
     * @param Tier $tier The tier carrying the new values (id required)
     *
     * @return void
     */
    public function updateTier(Tier $tier): void;

    /**
     * Set one tier's status
     *
     * @param int    $tierId The tier id
     * @param string $status The TierStatus value
     *
     * @return void
     */
    public function updateTierStatus(int $tierId, string $status): void;

    /**
     * Transactionally renumber tiers to positions 1..N in the given order —
     * two passes so UNIQUE (creator_id, position) never collides mid-shuffle
     *
     * @param array<int, int> $orderedTierIds The tier ids in the desired order
     *
     * @return void
     */
    public function reorderPositions(array $orderedTierIds): void;

    /**
     * Replace a tier's ordered benefit list
     *
     * @param int                $tierId       The tier id
     * @param array<int, string> $descriptions The new benefit lines, in order
     *
     * @return void
     */
    public function replaceBenefits(int $tierId, array $descriptions): void;
}
