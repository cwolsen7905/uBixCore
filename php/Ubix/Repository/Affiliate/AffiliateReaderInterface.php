<?php

declare(strict_types=1);

namespace Ubix\Repository\Affiliate;

use Exception;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\String\MpCode;
use Ubix\Model\Affiliate;

/**
 * Interface AffiliateReaderInterface
 *
 * Provides methods to read affiliate related data.
 */
interface AffiliateReaderInterface
{
    /**
     * Get Affiliate by ID
     *
     * @param AffiliateId $affiliateId The affiliate ID
     *
     * @return Affiliate The affiliate or null if not found
     *
     * @throws Exception If the affiliate is not found
     */
    public function getAffiliateById(AffiliateId $affiliateId): Affiliate;

    /**
     * Get Affiliate by Media Buying Code
     *
     * @param MpCode $mpCode The media buying code
     *
     * @return Affiliate The affiliate ID or null if not found
     *
     * @throws Exception If the affiliate is not found
     */
    public function getAffiliateByMpCode(MpCode $mpCode): Affiliate;

   /**
    * Get Affiliates Associated with Media Buying Campaigns
    *
    * @return array<int> An array of affiliate IDs associated with media buying campaigns.
    */
    public function getMediaBuyingCampaignAffiliates(): array;

    /**
     * Returns The Users Registration Code Which Happens To Be A String In The DB
     *
     * @param PlatformUserId $userId The User ID
     *
     * @return MpCode
     */
    public function getUserRegistrationMpCode(PlatformUserId $userId): MpCode;

    /**
     * Load House Accounts
     *
     * @return array<int> An array of house account affiliate IDs
     */
    public function loadHouseAccounts(): array;
}
