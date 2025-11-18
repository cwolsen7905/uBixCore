<?php

declare(strict_types=1);

namespace Ubix\Repository\Deal;

use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\String\MpCode;
use Ubix\Model\Deal;

/**
 * Interface DealReaderInterface
 *
 * Provides methods to read deal related data.
 */
interface DealReaderInterface
{
    /**
     * Get deals by affiliate ID
     *
     * @param AffiliateId $affiliateId The affiliate ID
     *
     * @return array<Deal> The list of deals for the affiliate
     */
    public function getDealsByAffiliateId(AffiliateId $affiliateId): array;

    /**
     * Check if a Media Buying Code is a Deal Media Buying Code
     *
     * @param MpCode $mpCode The media buying code
     *
     * @return array<Deal> The deal media buying code information
     */
    public function getDealMpCode(MpCode $mpCode): array;
}
