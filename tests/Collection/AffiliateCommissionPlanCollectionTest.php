<?php

declare(strict_types=1);

namespace Ubix\Tests\Collection;

use Ubix\Collection\AffiliateCommissionPlanCollection;
use Ubix\DataType\String\MpCode;
use Ubix\Enum\Affiliate\AffiliateProductType;
use Ubix\Enum\Affiliate\AffiliateRateType;
use Ubix\Model\AffiliateCommissionPlan;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Collection\AffiliateCommissionPlanCollection
 *
 * @coversDefaultClass \Ubix\Collection\AffiliateCommissionPlanCollection
 */
final class AffiliateCommissionPlanCollectionTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateCommissionPlanCollection::class);
    }

    /**
     * Test the constructor and add method
     *
     * @return void
     */
    public function testConstructorAndAddMethod(): void
    {
        $plan1      = new AffiliateCommissionPlan(
            mpCode:      new MpCode('MP001'),
            productType: AffiliateProductType::LIVE,
            rateType:    AffiliateRateType::PPS,
        );
        $plan2      = new AffiliateCommissionPlan(
            mpCode:      new MpCode('MP002'),
            productType: AffiliateProductType::VIP,
            rateType:    AffiliateRateType::SLIDING,
        );
        $collection = new AffiliateCommissionPlanCollection(plans: [$plan1]);
        $collection->add($plan2);
        $this->assertInstanceOf(AffiliateCommissionPlanCollection::class, $collection);
    }

    /**
     * Test the get method
     *
     * @return void
     */
    public function testGetByMpCodeAndProductTypeMethod(): void
    {
        $plan1          = new AffiliateCommissionPlan(
            mpCode:      new MpCode('MP001'),
            productType: AffiliateProductType::LIVE,
            rateType:    AffiliateRateType::PPS,
        );
        $plan2          = new AffiliateCommissionPlan(
            mpCode:      new MpCode('MP002'),
            productType: AffiliateProductType::VIP,
            rateType:    AffiliateRateType::SLIDING,
        );
        $collection     = new AffiliateCommissionPlanCollection(plans: [$plan1, $plan2]);
        $retrievedPlan1 = $collection->getByMpCodeAndProductType(new MpCode('MP001'), AffiliateProductType::LIVE);
        $retrievedPlan2 = $collection->getByMpCodeAndProductType(new MpCode('MP002'), AffiliateProductType::VIP);
        $this->assertSame($plan1, $retrievedPlan1);
        $this->assertSame($plan2, $retrievedPlan2);
        $this->assertNull($collection->getByMpCodeAndProductType(new MpCode('MP003'), AffiliateProductType::VIP));
    }

    /**
     * Test the getDefaultByProductType method
     *
     * @return void
     */
    public function testGetDefaultByProductTypeMethod(): void
    {
        $defaultPlan          = new AffiliateCommissionPlan(
            mpCode:      null,
            productType: AffiliateProductType::LIVE,
            rateType:    AffiliateRateType::PPS,
        );
        $collection           = new AffiliateCommissionPlanCollection(plans: [$defaultPlan]);
        $retrievedDefaultPlan = $collection->getDefaultByProductType(AffiliateProductType::LIVE);
        $this->assertSame($defaultPlan, $retrievedDefaultPlan);
        $this->assertNull($collection->getDefaultByProductType(AffiliateProductType::VIP));
    }
}
