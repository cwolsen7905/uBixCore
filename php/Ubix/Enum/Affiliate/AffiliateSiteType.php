<?php

declare(strict_types=1);

namespace Ubix\Enum\Affiliate;

/**
 * Enumeration of rate types
 *
 * @see \Ubix\Tests\Enum\Affiliate\AffiliateSiteTypeTest PHPUnit test case
 */
enum AffiliateSiteType: string
{
    case ADULT          = 'adult';
    case RTB            = 'rtb';
    case AFFILIATE      = 'affiliate';
    case PSYCHIC        = 'psychic';
    case PPC            = 'ppc';
    case XVC            = 'xvc';
    case XVT            = 'xvt';
    case XVT_TRAFFIC    = 'xvtraffic';
    case VOLUME         = 'volume';
    case MB_RETARGETING = 'mb_retargeting';
}
