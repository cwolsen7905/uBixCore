<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\FanClubApi;

use Ubix\Controller\FanClubApi\AgeVerificationController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\FanClubApi\AgeVerificationController
 *
 * @coversDefaultClass \Ubix\Controller\FanClubApi\AgeVerificationController
 */
final class AgeVerificationControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AgeVerificationController::class);
    }
}
