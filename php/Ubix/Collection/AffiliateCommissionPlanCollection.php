<?php

declare(strict_types=1);

namespace Ubix\Collection;

use Ubix\Collection\CollectionInterface as Collection;
use Ubix\DataType\String\MpCode;
use Ubix\Enum\Affiliate\AffiliateProductType;
use Ubix\Model\AffiliateCommissionPlan;

/**
 * Collection class for AffiliateCommissionPlan objects.
 *
 * Provides efficient retrieval of plans based on MP code and product type.
 *
 * @see \Ubix\Tests\Collection\AffiliateCommissionPlanCollectionTest PHPUnit test case
 */
final class AffiliateCommissionPlanCollection implements Collection
{
    /**
     * @var array<string, array<string, AffiliateCommissionPlan>>
     */
    private array $index = [];

    /**
     * Constructor
     *
     * @param array<AffiliateCommissionPlan> $plans The array of AffiliateCommissionPlan objects to initialize the collection with.
     */
    public function __construct(
        // @phpstan-ignore-next-line
        private array $plans,
    ) {
        foreach ($plans as $plan) {
            $this->add($plan);
        }
    }

    /**
     * Add an AffiliateCommissionPlan to the collection.
     *
     * @param AffiliateCommissionPlan $plan The plan to add.
     *
     * @return void
     */
    public function add(AffiliateCommissionPlan $plan): void
    {
        $mpCode = $plan->getMpCode()->value ?? 'default';

        $productType = $plan->getProductType()->value ?? 'default';

        $this->index[$mpCode][$productType] = $plan;
    }

    /**
     * Get an AffiliateCommissionPlan by MP code and product type.
     *
     * @param MpCode               $mpCode      The MP code to look up.
     * @param AffiliateProductType $productType The product type to look up.
     *
     * @return ?AffiliateCommissionPlan The matching plan, or null if not found.
     */
    public function getByMpCodeAndProductType(MpCode $mpCode, AffiliateProductType $productType): ?AffiliateCommissionPlan
    {
        return $this->index[$mpCode->value][$productType->value] ?? null;
    }

    /**
     * Get the default AffiliateCommissionPlan for a given product type.
     *
     * @param AffiliateProductType $productType The product type to look up.
     *
     * @return ?AffiliateCommissionPlan The default plan for the product type, or null if not found.
     */
    public function getDefaultByProductType(AffiliateProductType $productType): ?AffiliateCommissionPlan
    {
        return $this->index['default'][$productType->value] ?? null;
    }
}
