<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Collection\AffiliateCommissionPlanCollection;
use Ubix\DataTransferObject\MediaBuyingDetails;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\String\MpCode;
use Ubix\Enum\Affiliate\AffiliateProductType;
use Ubix\Enum\Affiliate\AffiliateRateType;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\AffiliateCommissionPlan;
use Ubix\Repository\Affiliate\AffiliateReaderInterface as AffiliateReader;
use Ubix\Repository\CommissionPlan\CommissionPlanReaderInterface as CommissionPlanReader;
use Ubix\Repository\Deal\DealReaderInterface as DealReader;

/**
 * Service to filter data
 *
 * @see \Ubix\Tests\Service\AffiliateServiceTest PHPUnit test case
 */
final class AffiliateService
{
    /**
     * List of affiliate IDs that should always be considered as media buying affiliates.
     *
     * These are hardcoded corrections to supplement the database query results.
     */
    private const MEDIA_BUYING_CORRECTIONS = [
        10013749, // Yahoo PI + Internal WL Domains
        10017146, // Black Label Ads
        10021048, // Yahoo PI
        10022082,
        10022229, // Yahoo PI
        10023928,
        10025576,
        10031419,
        10062521,
    ];

    /**
     * Constructor.
     *
     * @param Logger               $logger               The logger interface
     * @param AffiliateReader      $affiliateReader      The Attribution Repo For DB Changes
     * @param CommissionPlanReader $commissionPlanReader The Commission Plan Reader
     * @param DealReader           $dealReader           The Deal Reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private AffiliateReader $affiliateReader,
        private CommissionPlanReader $commissionPlanReader,
        private DealReader $dealReader,
    ) {
    }

    /**
     * Determines if a given affiliate or user mp_code is part of a media buying campaign or deal.
     *
     * @param AffiliateId $affiliateId The ID of the affiliate to check.
     * @param MpCode      $userMpCode  The user's mp_code to check.
     *
     * @return MediaBuyingDetails Details indicating if it's a media buy and the type
     */
    public function getMediaBuyingDetails(AffiliateId $affiliateId, MpCode $userMpCode): MediaBuyingDetails
    {
        //  Check if the mp_code supplied belongs to a Media Buy which
        //  would cause us to skip our normal attribution checks.
        $isMediaBuying = false;

        $mediaBuyType = '';

        if (in_array($affiliateId->value, array_merge(self::MEDIA_BUYING_CORRECTIONS, $this->affiliateReader->getMediaBuyingCampaignAffiliates()), true)) {
            $isMediaBuying = true;
            $mediaBuyType  = 'mb_campaign_aff';
        }


        //
        //  Check if mp_code belongs to a Deal
        //
        if (empty($isMediaBuying)) {
            if (count($this->dealReader->getDealMpCode($userMpCode)) > 0) {
                $isMediaBuying = true;

                $mediaBuyType = 'mb_deal_mp';
            }
        }

        //
        //  Check if affiliate_id belongs to a Deal
        //
        if (empty($isMediaBuying)) {
            if (count($this->dealReader->getDealsByAffiliateId($affiliateId)) > 0) {
                $isMediaBuying = true;

                $mediaBuyType = 'mb_deals';
            }
        }

        return new MediaBuyingDetails(
            isMediaBuying: $isMediaBuying,
            type:          $mediaBuyType,
        );
    }

    /**
     * Check If The Affiliate Is A House Account
     *
     * @param AffiliateId $affiliateId The Affiliate Id
     *
     * @return bool
     */
    public function isHouseAccount(AffiliateId $affiliateId): bool
    {
        $houseAccounts = $this->affiliateReader->loadHouseAccounts();

        return in_array($affiliateId->value, $houseAccounts, true);
    }

    /**
     * Get Rate Type By MP Code and Product Type
     *
     * This method retrieves the rate type for a given media buying code (mpCode)
     * and product type. It first determines the affiliate associated with the mpCode,
     * then fetches the affiliate's commission plans. If a specific plan for the
     * provided product type exists, it returns that rate type. If not, it checks
     * for a default plan. If neither is found, it returns false.
     *
     * @param MpCode               $mpCode      The media buying code to look up.
     * @param AffiliateProductType $productType The product type to look up.
     *
     * @return AffiliateRateType The rate type if found, otherwise false.
     *
     * @throws Exception If no rate type is found for the given mp_code and product type.
     */
    public function getRateTypeByMpCode(MpCode $mpCode, AffiliateProductType $productType): AffiliateRateType
    {
        try {
            $affiliate = $this->affiliateReader->getAffiliateByMpCode($mpCode);
        } catch (Exception $e) {
            throw new Exception('No affiliate found for mp_code ' . $mpCode->value, ExceptionCode::AFFILIATE_NOT_FOUND->value);
        }

        if ($affiliate->getId() === null) {
            throw new Exception('No affiliate found for mp_code ' . $mpCode->value, ExceptionCode::AFFILIATE_NOT_FOUND->value);
        }

        $plans = $this->commissionPlanReader->getAffiliatePlans($affiliate->getId());

        $collection = new AffiliateCommissionPlanCollection($plans);

        $plan = $collection->getByMpCodeAndProductType($mpCode, $productType);

        if ($plan instanceof AffiliateCommissionPlan && $plan->getRateType() instanceof AffiliateRateType) {
            return $plan->getRateType();
        }

        $plan = $collection->getDefaultByProductType($productType);

        if ($plan instanceof AffiliateCommissionPlan && $plan->getRateType() instanceof AffiliateRateType) {
            return $plan->getRateType();
        }

        $defaultPlans = $this->commissionPlanReader->getDefaultPlans();

        foreach ($defaultPlans as $defaultPlan) {
            if ($defaultPlan['product_type'] === $productType->value) {
                return AffiliateRateType::from($defaultPlan['rate_type']);
            }
        }

        throw new Exception('No rate type found for mp_code ' . $mpCode->value . ' and product type ' . $productType->value, ExceptionCode::RATE_TYPE_NOT_FOUND->value);
    }
}
