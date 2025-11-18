<?php

declare(strict_types=1);

namespace Ubix\Enum\Affiliate;

/**
 * Enumeration of product types
 *
 * @see \Ubix\Tests\Enum\Affiliate\AffiliateProductTypeTest PHPUnit test case
 */
enum AffiliateProductType: string
{
    case LIVE       = 'live';
    case VIP        = 'vip';
    case EXTERNAL   = 'external';
    case VOD        = 'vod';
    case VIDEO_CHAT = 'videochat';
    case PERFORMER  = 'performer';
    case DATING     = 'dating';
    case PSYCHIC    = 'psychic';
}
