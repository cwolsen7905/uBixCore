<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\SoloAcquisitionCampaign;

use Ubix\Repository\SoloAcquisitionCampaign\SoloAcquisitionCampaignSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\SoloAcquisitionCampaign\SoloAcquisitionCampaignSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\SoloAcquisitionCampaign\SoloAcquisitionCampaignSqlRepository
 */
final class SoloAcquisitionCampaignSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SoloAcquisitionCampaignSqlRepository::class);
    }
}
