<?php

declare(strict_types=1);

namespace Ubix\Repository\Tier;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\TierOptions;
use Ubix\Enum\Tier\TierBillingInterval;
use Ubix\Enum\Tier\TierStatus;
use Ubix\Model\Tier;
use Ubix\Model\TierBenefit;
use Ubix\Repository\Tier\TierReaderInterface as TierReader;
use Ubix\Repository\Tier\TierWriterInterface as TierWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * SQL-backed tier repository
 *
 * @see \Ubix\Tests\Repository\Tier\TierSqlRepositoryTest PHPUnit test case
 */
final class TierSqlRepository implements TierReader, TierWriter
{
    /**
     * Constructor
     *
     * @param Logger     $logger     The Monolog logger
     * @param SqlService $sqlService The SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getTierById(int $tierId): ?Tier
    {
        return $this->query(new TierOptions(id: $tierId, limit: 1))[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function listTiersForCreator(int $creatorId, bool $activeOnly = false): array
    {
        return $this->query(new TierOptions(creatorId: $creatorId, activeOnly: $activeOnly));
    }

    /**
     * {@inheritDoc}
     */
    public function listBenefitsForTiers(array $tierIds): array
    {
        if ($tierIds === []) {
            return [];
        }

        $placeholders = [];
        $parameters   = [];
        foreach (array_values($tierIds) as $index => $tierId) {
            $placeholders[]              = ':tier' . $index;
            $parameters['tier' . $index] = $tierId;
        }

        $sql = 'SELECT id, tier_id, description, position, created_at, updated_at
                FROM sowingme.tier_benefits
                WHERE tier_id IN (' . implode(', ', $placeholders) . ')
                ORDER BY tier_id, position';

        $benefits = [];
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $benefits[] = new TierBenefit(
                id:          (int) $row['id'],
                tierId:      (int) $row['tier_id'],
                description: is_string($row['description']) ? $row['description'] : null,
                position:    (int) $row['position'],
                createdAt:   is_string($row['created_at']) ? new DateTime($row['created_at']) : null,
                updatedAt:   is_string($row['updated_at']) ? new DateTime($row['updated_at']) : null,
            );
        }

        return $benefits;
    }

    /**
     * {@inheritDoc}
     */
    public function createTier(Tier $tier): void
    {
        $sql = 'INSERT INTO sowingme.tiers
                    (creator_id, name, description, price_amount, price_currency, billing_interval, position, status)
                VALUES
                    (:creator_id, :name, :description, :price_amount, :price_currency, :billing_interval, :position, :status)';

        $this->sqlService->query($sql, [
            'billing_interval' => $tier->getBillingInterval()->value ?? TierBillingInterval::MONTH->value,
            'creator_id'       => $tier->getCreatorId(),
            'description'      => $tier->getDescription(),
            'name'             => $tier->getName(),
            'position'         => $tier->getPosition(),
            'price_amount'     => $tier->getPriceAmount(),
            'price_currency'   => $tier->getPriceCurrency() ?? 'USD',
            'status'           => $tier->getStatus()->value ?? TierStatus::ACTIVE->value,
        ]);

        $tier->setId((int) $this->sqlService->lastInsertId());
    }

    /**
     * {@inheritDoc}
     */
    public function updateTier(Tier $tier): void
    {
        $sql = 'UPDATE sowingme.tiers
                SET name = :name,
                    description = :description,
                    price_amount = :price_amount,
                    price_currency = :price_currency,
                    billing_interval = :billing_interval
                WHERE id = :id';

        $this->sqlService->query($sql, [
            'billing_interval' => $tier->getBillingInterval()->value ?? TierBillingInterval::MONTH->value,
            'description'      => $tier->getDescription(),
            'id'               => $tier->getId(),
            'name'             => $tier->getName(),
            'price_amount'     => $tier->getPriceAmount(),
            'price_currency'   => $tier->getPriceCurrency() ?? 'USD',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function updateTierStatus(int $tierId, string $status): void
    {
        $sql = 'UPDATE sowingme.tiers SET status = :status WHERE id = :id';

        $this->sqlService->query($sql, ['id' => $tierId, 'status' => $status]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception When the transactional renumbering fails (rolled back)
     */
    public function reorderPositions(array $orderedTierIds): void
    {
        $this->sqlService->beginTransaction();
        try {
            foreach (array_values($orderedTierIds) as $index => $tierId) {
                $this->updatePosition($tierId, 10000 + $index + 1);
            }
            foreach (array_values($orderedTierIds) as $index => $tierId) {
                $this->updatePosition($tierId, $index + 1);
            }
            $this->sqlService->commit();
        } catch (Exception $e) {
            $this->sqlService->rollBack();
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function replaceBenefits(int $tierId, array $descriptions): void
    {
        $this->sqlService->query('DELETE FROM sowingme.tier_benefits WHERE tier_id = :tier_id', ['tier_id' => $tierId]);

        foreach (array_values($descriptions) as $index => $description) {
            $this->sqlService->query(
                'INSERT INTO sowingme.tier_benefits (tier_id, description, position) VALUES (:tier_id, :description, :position)',
                ['description' => $description, 'position' => $index + 1, 'tier_id' => $tierId],
            );
        }
    }

    /**
     * Set one tier's position
     *
     * @param int $tierId   The tier id
     * @param int $position The new position
     *
     * @return void
     */
    private function updatePosition(int $tierId, int $position): void
    {
        $sql = 'UPDATE sowingme.tiers SET position = :position WHERE id = :id';

        $this->sqlService->query($sql, ['id' => $tierId, 'position' => $position]);
    }

    /**
     * Query tiers
     *
     * @param TierOptions $options DTO of options to generate the query
     *
     * @return array<int, Tier> The matching tiers, lowest position first
     */
    private function query(TierOptions $options): array
    {
        $sql        = 'SELECT id, creator_id, name, description, price_amount, price_currency, billing_interval, position, status, created_at, updated_at
                FROM sowingme.tiers';
        $where      = [];
        $parameters = [];

        if ($options->id !== null) {
            $where[]          = 'id = :id';
            $parameters['id'] = $options->id;
        }

        if ($options->creatorId !== null) {
            $where[]                 = 'creator_id = :creatorId';
            $parameters['creatorId'] = $options->creatorId;
        }

        if ($options->status !== null) {
            $where[]              = 'status = :status';
            $parameters['status'] = $options->status;
        } elseif ($options->activeOnly) {
            $where[]              = 'status = :status';
            $parameters['status'] = TierStatus::ACTIVE->value;
        }

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY position';

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        $tiers = [];
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $tiers[] = new Tier(
                id:              (int) $row['id'],
                creatorId:       (int) $row['creator_id'],
                name:            is_string($row['name']) ? $row['name'] : null,
                description:     is_string($row['description']) ? $row['description'] : null,
                priceAmount:     (int) $row['price_amount'],
                priceCurrency:   is_string($row['price_currency']) ? $row['price_currency'] : null,
                billingInterval: is_string($row['billing_interval']) ? TierBillingInterval::from($row['billing_interval']) : null,
                position:        (int) $row['position'],
                status:          is_string($row['status']) ? TierStatus::from($row['status']) : null,
                createdAt:       is_string($row['created_at']) ? new DateTime($row['created_at']) : null,
                updatedAt:       is_string($row['updated_at']) ? new DateTime($row['updated_at']) : null,
            );
        }

        return $tiers;
    }
}
