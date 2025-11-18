<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\FanClubWeb;

use Ubix\Controller\FanClubWeb\AuthenticationController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\FanClubWeb\AuthenticationController
 *
 * @coversDefaultClass \Ubix\Controller\FanClubWeb\AuthenticationController
 */
final class AuthenticationControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AuthenticationController::class);
    }
}
