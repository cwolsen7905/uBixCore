<?php

declare(strict_types=1);

namespace Ubix\Repository\Subscription;

use Ubix\Model\Subscription;

/**
 * Writes subscriptions — callable ONLY from the payments surface's services
 * (subscription-tiers TDS §3.3; convention enforced in review, not runtime)
 */
interface SubscriptionWriterInterface
{
    /**
     * Persist a new subscription row (the new id is set on the model)
     *
     * @param Subscription $subscription The subscription to create
     *
     * @return void
     */
    public function createSubscription(Subscription $subscription): void;

    /**
     * Update a subscription's status / provider fields / period end
     *
     * @param Subscription $subscription The subscription carrying new values (id required)
     *
     * @return void
     */
    public function updateSubscription(Subscription $subscription): void;
}
