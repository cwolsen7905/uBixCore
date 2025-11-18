<?php

declare(strict_types=1);

namespace Ubix\Enum\Affiliate;

/**
 * Enumeration of rate types
 *
 * @see \Ubix\Tests\Enum\Affiliate\AffiliateStatusTest PHPUnit test case
 */
enum AffiliateStatus: string
{
    case ACTIVE    = 'active';
    case INACTIVE  = 'inactive';
    case PENDING   = 'pending';
    case SUSPENDED = 'suspended';
    case CLOSED    = 'closed';
    case DELETED   = 'deleted';
}
