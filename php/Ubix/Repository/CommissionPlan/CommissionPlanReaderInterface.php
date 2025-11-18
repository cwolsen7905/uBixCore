<?php

declare(strict_types=1);

namespace Ubix\Repository\CommissionPlan;

use Ubix\DataType\Int\AffiliateId;
use Ubix\Model\AffiliateCommissionPlan;

/**
 * Interface AffiliateReaderInterface
 *
 * Provides methods to read affiliate-related data.
 */
interface CommissionPlanReaderInterface
{
    /**
     * Retrieves the commission plans for a given affiliate.
     *
     * This includes affiliate-specific plans and fills in any missing default plans.
     *
     * @param AffiliateId $affiliateId The ID of the affiliate.
     *
     * @return array<AffiliateCommissionPlan> The list of commission plans for the affiliate.
     */
    public function getAffiliatePlans(AffiliateId $affiliateId): array;

    /**
     * Retrieves the default commission plans.
     *
     * @return array<array{id: int,name: string,description: string,commission_type_id: int,rate_type: string,product_type: string, processing_fees: float,is_default: int,status: int}> The list of default commission plans.
     */
    public function getDefaultPlans(): array;
}
