<?php

declare(strict_types=1);

namespace Ubix\Repository\Affiliate;

use Exception;
use Ubix\DataType\Int\AffiliateId;
use Ubix\Model\Affiliate;
use Ubix\Model\PlatformUser;

/**
 * Interface AffiliateWriterInterface
 *
 * Provides methods to write affiliate-related data.
 */
interface AffiliateWriterInterface
{
    /**
     * Save the affiliate
     *
     * @param Affiliate $affiliate The Affiliate to Save
     *
     * @return void
     *
     * @throws Exception If unable to save the affiliate
     */
    public function save(Affiliate $affiliate): void;

    /**
     * Delete the affiliate
     *
     * @param AffiliateId $affiliateId The Affiliate ID to Delete
     *
     * @return void
     */
    public function deleteById(AffiliateId $affiliateId): void;

    /**
     * Update the user's MP code and bounty paid
     *
     * @param PlatformUser $user The user object containing the updated information
     *
     * @return void
     */
    public function updateUserMpCodeAndBountyPaid(PlatformUser $user): void;
}
