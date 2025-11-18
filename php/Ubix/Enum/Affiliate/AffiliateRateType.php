<?php

declare(strict_types=1);

namespace Ubix\Enum\Affiliate;

/**
 * Enumeration of rate types
 *
 * @see \Ubix\Tests\Enum\Affiliate\AffiliateRateTypeTest PHPUnit test case
 */
enum AffiliateRateType: string
{
    case SLIDING     = 'sliding';
    case PERCENT     = 'percent';
    case PPS         = 'pps';
    case PPS_SLIDING = 'pps_sliding';
    case CPL         = 'cpl';
    case CPA         = 'cpa';
    case CPR         = 'cpr';
}
