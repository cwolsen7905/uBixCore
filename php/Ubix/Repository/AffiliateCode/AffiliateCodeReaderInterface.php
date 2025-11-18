<?php

declare(strict_types=1);

namespace Ubix\Repository\AffiliateCode;

use Ubix\DataType\Int\AffiliateId;
use Ubix\Model\AffiliateCode;

/**
 * Interface AffiliateCodeReaderInterface
 *
 * Provides methods to read affiliate code related data.
 */
interface AffiliateCodeReaderInterface
{
    /**
     * Get Affiliate Codes by Affiliate ID
     *
     * @param AffiliateId $affiliateId The affiliate ID
     *
     * @return array<AffiliateCode> An array of affiliate codes
     */
    public function getAffiliateCodes(AffiliateId $affiliateId): array;
}
