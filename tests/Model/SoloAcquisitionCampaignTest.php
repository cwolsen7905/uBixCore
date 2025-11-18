<?php

declare(strict_types=1);

namespace Ubix\Tests\Model;

use Ubix\Model\SoloAcquisitionCampaign;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Model\SoloAcquisitionCampaign
 *
 * @coversDefaultClass \Ubix\Model\SoloAcquisitionCampaign
 */
final class SoloAcquisitionCampaignTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SoloAcquisitionCampaign::class);
    }
}
