<?php

declare(strict_types=1);

namespace Ubix\Repository\Subscription;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\PagedObjects;
use Ubix\DataTransferObject\SqlRepository\SubscriptionOptions;
use Ubix\Enum\Subscription\SubscriptionStatus;
use Ubix\Model\Subscription;
use Ubix\Repository\Subscription\SubscriptionReaderInterface as SubscriptionReader;
use Ubix\Repository\Subscription\SubscriptionWriterInterface as SubscriptionWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * SQL-backed subscription repository
 *
 * @see \Ubix\Tests\Repository\Subscription\SubscriptionSqlRepositoryTest PHPUnit test case
 */
final class SubscriptionSqlRepository implements SubscriptionReader, SubscriptionWriter
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
    public function getActiveSubscription(int $userId, int $creatorId): ?Subscription
    {
        return $this->query(new SubscriptionOptions(userId: $userId, creatorId: $creatorId, nonTerminalOnly: true, limit: 1))[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function listSubscriptionsForUser(int $userId): array
    {
        return $this->query(new SubscriptionOptions(userId: $userId));
    }

    /**
     * {@inheritDoc}
     */
    public function listSubscribersForCreator(int $creatorId, int $limit, int $offset): PagedObjects
    {
        $total = (int) $this->sqlService->getColumn(
            'SELECT COUNT(*) FROM sowingme.subscriptions WHERE creator_id = :creatorId AND active_key IS NOT NULL',
            ['creatorId' => $creatorId],
        );

        $subscriptions = $this->query(new SubscriptionOptions(creatorId: $creatorId, nonTerminalOnly: true, limit: $limit, offset: $offset));

        return new PagedObjects(objects: $subscriptions, offset: $offset, total: $total);
    }

    /**
     * {@inheritDoc}
     */
    public function createSubscription(Subscription $subscription): void
    {
        $sql = 'INSERT INTO sowingme.subscriptions
                    (user_id, creator_id, tier_id, status, provider_subscription_id, provider_customer_id, current_period_end)
                VALUES
                    (:user_id, :creator_id, :tier_id, :status, :provider_subscription_id, :provider_customer_id, :current_period_end)';

        $this->sqlService->query($sql, [
            'creator_id'               => $subscription->getCreatorId(),
            'current_period_end'       => $subscription->getCurrentPeriodEnd()?->format('Y-m-d H:i:s'),
            'provider_customer_id'     => $subscription->getProviderCustomerId(),
            'provider_subscription_id' => $subscription->getProviderSubscriptionId(),
            'status'                   => $subscription->getStatus()->value ?? SubscriptionStatus::ACTIVE->value,
            'tier_id'                  => $subscription->getTierId(),
            'user_id'                  => $subscription->getUserId(),
        ]);

        $subscription->setId((int) $this->sqlService->lastInsertId());
    }

    /**
     * {@inheritDoc}
     */
    public function updateSubscription(Subscription $subscription): void
    {
        $sql = 'UPDATE sowingme.subscriptions
                SET tier_id = :tier_id,
                    status = :status,
                    provider_subscription_id = :provider_subscription_id,
                    provider_customer_id = :provider_customer_id,
                    current_period_end = :current_period_end,
                    canceled_at = :canceled_at
                WHERE id = :id';

        $this->sqlService->query($sql, [
            'canceled_at'              => $subscription->getCanceledAt()?->format('Y-m-d H:i:s'),
            'current_period_end'       => $subscription->getCurrentPeriodEnd()?->format('Y-m-d H:i:s'),
            'id'                       => $subscription->getId(),
            'provider_customer_id'     => $subscription->getProviderCustomerId(),
            'provider_subscription_id' => $subscription->getProviderSubscriptionId(),
            'status'                   => $subscription->getStatus()->value ?? SubscriptionStatus::ACTIVE->value,
            'tier_id'                  => $subscription->getTierId(),
        ]);
    }

    /**
     * Query subscriptions
     *
     * @param SubscriptionOptions $options DTO of options to generate the query
     *
     * @return array<int, Subscription> The matching subscriptions, newest first
     */
    private function query(SubscriptionOptions $options): array
    {
        $sql        = 'SELECT id, user_id, creator_id, tier_id, status, provider_subscription_id, provider_customer_id, current_period_end, canceled_at, created_at, updated_at
                FROM sowingme.subscriptions';
        $where      = [];
        $parameters = [];

        if ($options->id !== null) {
            $where[]          = 'id = :id';
            $parameters['id'] = $options->id;
        }

        if ($options->userId !== null) {
            $where[]              = 'user_id = :userId';
            $parameters['userId'] = $options->userId;
        }

        if ($options->creatorId !== null) {
            $where[]                 = 'creator_id = :creatorId';
            $parameters['creatorId'] = $options->creatorId;
        }

        if ($options->nonTerminalOnly) {
            $where[] = 'active_key IS NOT NULL';
        }

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY created_at DESC';

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
            if ($options->offset !== null) {
                $sql .= ' OFFSET ' . $options->offset;
            }
        }

        $subscriptions = [];
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $subscriptions[] = new Subscription(
                id:                     (int) $row['id'],
                userId:                 (int) $row['user_id'],
                creatorId:              (int) $row['creator_id'],
                tierId:                 (int) $row['tier_id'],
                status:                 is_string($row['status']) ? SubscriptionStatus::from($row['status']) : null,
                providerSubscriptionId: is_string($row['provider_subscription_id']) ? $row['provider_subscription_id'] : null,
                providerCustomerId:     is_string($row['provider_customer_id']) ? $row['provider_customer_id'] : null,
                currentPeriodEnd:       is_string($row['current_period_end']) ? new DateTime($row['current_period_end']) : null,
                canceledAt:             is_string($row['canceled_at']) ? new DateTime($row['canceled_at']) : null,
                createdAt:              is_string($row['created_at']) ? new DateTime($row['created_at']) : null,
                updatedAt:              is_string($row['updated_at']) ? new DateTime($row['updated_at']) : null,
            );
        }

        return $subscriptions;
    }
}
