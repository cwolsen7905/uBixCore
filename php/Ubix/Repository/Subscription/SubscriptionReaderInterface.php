<?php

declare(strict_types=1);

namespace Ubix\Repository\Subscription;

use Ubix\DataTransferObject\PagedObjects;
use Ubix\Model\Subscription;

/**
 * Reads subscriptions
 */
interface SubscriptionReaderInterface
{
    /**
     * Get the supporter's non-terminal subscription to one creator, if any
     *
     * @param int $userId    The supporter's user id
     * @param int $creatorId The creator id
     *
     * @return ?Subscription The active/past_due row, or null
     */
    public function getActiveSubscription(int $userId, int $creatorId): ?Subscription;

    /**
     * List all of a supporter's subscription rows (every creator, any status)
     *
     * @param int $userId The supporter's user id
     *
     * @return array<int, Subscription> The subscriptions, newest first
     */
    public function listSubscriptionsForUser(int $userId): array;

    /**
     * Page through a creator's non-terminal subscribers (offset pagination)
     *
     * @param int $creatorId The creator id
     * @param int $limit     Page size
     * @param int $offset    Row offset
     *
     * @return PagedObjects The page of Subscription models plus offset/total
     */
    public function listSubscribersForCreator(int $creatorId, int $limit, int $offset): PagedObjects;
}
