<?php

declare(strict_types=1);

namespace Ubix\Enum\Affiliate;

/**
 * Enumeration of affiliate referrer status
 *
 * @see \Ubix\Tests\Enum\Affiliate\AffiliateReferrerStatusTest PHPUnit test case
 */
enum AffiliateReferrerStatus: string
{
    case YES = 'Y';
    case NO  = 'N';
}
