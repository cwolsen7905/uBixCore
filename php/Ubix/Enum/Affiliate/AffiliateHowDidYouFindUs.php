<?php

declare(strict_types=1);

namespace Ubix\Enum\Affiliate;

/**
 * Enumeration of how did you find us
 *
 * @see \Ubix\Tests\Enum\Affiliate\AffiliateHowDidYouFindUsTest PHPUnit test case
 */
enum AffiliateHowDidYouFindUs: string
{
    case WORD_OF_MOUTH  = 'wordofmouth';
    case INDUSTRY_FORUM = 'industryforum';
    case TRADE_SHOW     = 'tradeshow';
    case ONLINE_SEARCH  = 'onlinesearch';
    case OTHER          = 'other';
}
