<?php

declare(strict_types=1);

namespace Ubix\Repository\Tier;

use Ubix\Model\Tier;
use Ubix\Model\TierBenefit;

/**
 * Reads tiers and their benefits
 */
interface TierReaderInterface
{
    /**
     * Get one tier by id
     *
     * @param int $tierId The tier id
     *
     * @return ?Tier The tier, or null when unknown
     */
    public function getTierById(int $tierId): ?Tier;

    /**
     * List a creator's tiers ordered by position
     *
     * @param int  $creatorId  The creator id
     * @param bool $activeOnly Exclude archived tiers
     *
     * @return array<int, Tier> The tiers, lowest position first
     */
    public function listTiersForCreator(int $creatorId, bool $activeOnly = false): array;

    /**
     * List the benefits for a set of tiers, ordered within each tier
     *
     * @param array<int, int> $tierIds The tier ids
     *
     * @return array<int, TierBenefit> The benefits, grouped by tier order
     */
    public function listBenefitsForTiers(array $tierIds): array;
}
