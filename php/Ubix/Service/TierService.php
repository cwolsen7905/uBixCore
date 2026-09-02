<?php

declare(strict_types=1);

namespace Ubix\Service;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Tier\TierStatus;
use Ubix\Model\Tier;
use Ubix\Model\TierBenefit;
use Ubix\Repository\Tier\TierReaderInterface as TierReader;
use Ubix\Repository\Tier\TierWriterInterface as TierWriter;

/**
 * Creator tier management: CRUD, transactional reorder, benefit lists
 *
 * @see \Ubix\Tests\Service\TierServiceTest PHPUnit test case
 */
final class TierService
{
    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param TierReader $tierReader Tier reader
     * @param TierWriter $tierWriter Tier writer
     */
    public function __construct(
        private Logger $logger,
        private TierReader $tierReader,
        private TierWriter $tierWriter,
    ) {
    }

    /**
     * Get one of the creator's own tiers, enforcing ownership
     *
     * @param int $creatorId The owning creator id
     * @param int $tierId    The tier id
     *
     * @return Tier The tier
     *
     * @throws InvalidArgumentException When the tier is missing or not owned by the creator
     */
    public function getOwnTier(int $creatorId, int $tierId): Tier
    {
        $tier = $this->tierReader->getTierById($tierId);
        if ($tier === null || $tier->getCreatorId() !== $creatorId) {
            throw new InvalidArgumentException('Tier not found', ExceptionCode::VALIDATION_FAILED->value);
        }

        return $tier;
    }

    /**
     * List a creator's tiers (optionally active-only), lowest position first
     *
     * @param int  $creatorId  The creator id
     * @param bool $activeOnly Exclude archived tiers
     *
     * @return array<int, Tier> The tiers
     */
    public function listTiers(int $creatorId, bool $activeOnly = false): array
    {
        return $this->tierReader->listTiersForCreator($creatorId, $activeOnly);
    }

    /**
     * The benefits for a set of tiers, ordered within each tier
     *
     * @param array<int, int> $tierIds The tier ids
     *
     * @return array<int, TierBenefit> The benefits
     */
    public function listBenefits(array $tierIds): array
    {
        return $this->tierReader->listBenefitsForTiers($tierIds);
    }

    /**
     * Create a tier at the next free position (FR-101/104/105)
     *
     * @param Tier               $tier     The tier to create (creatorId set by the caller)
     * @param array<int, string> $benefits Initial ordered benefit lines
     *
     * @return void
     */
    public function createTier(Tier $tier, array $benefits = []): void
    {
        $creatorId = $tier->getCreatorId();
        assert($creatorId !== null);

        $existing     = $this->tierReader->listTiersForCreator($creatorId);
        $nextPosition = 1;
        foreach ($existing as $row) {
            $nextPosition = max($nextPosition, ($row->getPosition() ?? 0) + 1);
        }

        $tier->setPosition($nextPosition);
        $this->tierWriter->createTier($tier);

        $tierId = $tier->getId();
        assert($tierId !== null);

        if ($benefits !== []) {
            $this->tierWriter->replaceBenefits($tierId, $benefits);
        }

        $this->logger->info('Tier created', ['creator_id' => $creatorId, 'position' => $nextPosition, 'tier_id' => $tierId]);
    }

    /**
     * Update a tier's editable fields (never position/status here)
     *
     * @param Tier $tier The tier carrying new values (id + creatorId required)
     *
     * @return void
     */
    public function updateTier(Tier $tier): void
    {
        $this->tierWriter->updateTier($tier);
    }

    /**
     * Archive or reactivate a tier (FR-103)
     *
     * @param Tier       $tier   The creator's own tier
     * @param TierStatus $status The new status
     *
     * @return void
     */
    public function updateStatus(Tier $tier, TierStatus $status): void
    {
        $tierId = $tier->getId();
        assert($tierId !== null);

        $this->tierWriter->updateTierStatus($tierId, $status->value);
        $this->logger->info('Tier status changed', ['status' => $status->value, 'tier_id' => $tierId]);
    }

    /**
     * Replace a tier's ordered benefit list (FR-201)
     *
     * @param Tier               $tier     The creator's own tier
     * @param array<int, string> $benefits The new benefit lines, in order
     *
     * @return void
     */
    public function replaceBenefits(Tier $tier, array $benefits): void
    {
        $tierId = $tier->getId();
        assert($tierId !== null);

        $this->tierWriter->replaceBenefits($tierId, $benefits);
    }

    /**
     * Renumber a creator's tiers (FR-105): every owned tier must appear exactly
     * once; positions become 1..N gap-free in the given order
     *
     * @param int             $creatorId The creator id
     * @param array<int, int> $tierIds   The creator's tier ids in the desired order
     *
     * @return void
     *
     * @throws InvalidArgumentException When the id set does not exactly match the creator's tiers
     */
    public function reorderTiers(int $creatorId, array $tierIds): void
    {
        $owned    = $this->tierReader->listTiersForCreator($creatorId);
        $ownedIds = [];
        foreach ($owned as $tier) {
            $ownedIds[] = $tier->getId();
        }

        $requested = array_values($tierIds);
        $sortedA   = $ownedIds;
        $sortedB   = $requested;
        sort($sortedA);
        sort($sortedB);
        if ($sortedA !== $sortedB) {
            throw new InvalidArgumentException(
                'Reorder must include each of the creator\'s tiers exactly once',
                ExceptionCode::VALIDATION_FAILED->value,
            );
        }

        $this->tierWriter->reorderPositions($requested);

        $this->logger->info('Tiers reordered', ['creator_id' => $creatorId, 'order' => $requested]);
    }
}
