<?php

declare(strict_types=1);

namespace Ubix\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Repository\Subscription\SubscriptionReaderInterface as SubscriptionReader;
use Ubix\Repository\Tier\TierReaderInterface as TierReader;

/**
 * Resolves a supporter's current tier position for a creator (TDS §5, FR-501..504)
 *
 * Position 0 is the implicit free tier: no non-terminal subscription row means
 * free-tier entitlement. The platform EntitlementService compares the returned
 * position against a resource's required position — this class never gates.
 *
 * @see \Ubix\Tests\Service\TierPrecedenceServiceTest PHPUnit test case
 */
final class TierPrecedenceService
{
    /**
     * Constructor
     *
     * @param Logger             $logger             Logger
     * @param SubscriptionReader $subscriptionReader Subscription reader
     * @param TierReader         $tierReader         Tier reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SubscriptionReader $subscriptionReader,
        private TierReader $tierReader,
    ) {
    }

    /**
     * The supporter's current tier position for a creator (0 = free tier)
     *
     * @param int $userId    The supporter's user id
     * @param int $creatorId The creator id
     *
     * @return int The tier position, 0 when no active subscription exists
     */
    public function resolve(int $userId, int $creatorId): int
    {
        $subscription = $this->subscriptionReader->getActiveSubscription($userId, $creatorId);
        if ($subscription === null || $subscription->getTierId() === null) {
            return 0;
        }

        $tier = $this->tierReader->getTierById($subscription->getTierId());

        return $tier?->getPosition() ?? 0;
    }
}
