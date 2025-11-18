<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\SoloAcquisitionCampaign;

use Ubix\Enum\SoloAcquisitionCampaign\SoloAcquisitionCampaignStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\SoloAcquisitionCampaign\SoloAcquisitionCampaignStatus
 *
 * @coversDefaultClass \Ubix\Enum\SoloAcquisitionCampaign\SoloAcquisitionCampaignStatus
 */
final class SoloAcquisitionCampaignStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SoloAcquisitionCampaignStatus::class);
    }
}
