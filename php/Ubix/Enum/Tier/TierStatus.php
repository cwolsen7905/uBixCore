<?php

declare(strict_types=1);

namespace Ubix\Enum\Tier;

/**
 * A tier's lifecycle status — archived tiers hide from new subscribers only
 *
 * @see \Ubix\Tests\Enum\Tier\TierStatusTest PHPUnit test case
 */
enum TierStatus: string
{
    case ACTIVE   = 'active';
    case ARCHIVED = 'archived';
}
