<?php

declare(strict_types=1);

namespace Ubix\Enum\Subscription;

/**
 * A subscription's lifecycle status — written by the payments surface (FR-301)
 *
 * @see \Ubix\Tests\Enum\Subscription\SubscriptionStatusTest PHPUnit test case
 */
enum SubscriptionStatus: string
{
    case ACTIVE   = 'active';
    case PAST_DUE = 'past_due';
    case CANCELED = 'canceled';
    case EXPIRED  = 'expired';
}
