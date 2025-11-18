<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\FanClubApi;

use Ubix\Controller\FanClubApi\PlatformUserController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\FanClubApi\PlatformUserController
 *
 * @coversDefaultClass \Ubix\Controller\FanClubApi\PlatformUserController
 */
final class PlatformUserControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PlatformUserController::class);
    }
}
