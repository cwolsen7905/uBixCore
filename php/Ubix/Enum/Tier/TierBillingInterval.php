<?php

declare(strict_types=1);

namespace Ubix\Enum\Tier;

/**
 * How often a paid tier bills (subscription-tiers TDS §2.1)
 *
 * @see \Ubix\Tests\Enum\Tier\TierBillingIntervalTest PHPUnit test case
 */
enum TierBillingInterval: string
{
    case MONTH = 'month';
    case YEAR  = 'year';
}
