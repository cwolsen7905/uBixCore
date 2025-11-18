<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\FanClubWeb;

use Ubix\Controller\FanClubWeb\HomeController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\FanClubWeb\HomeController
 *
 * @coversDefaultClass \Ubix\Controller\FanClubWeb\HomeController
 */
final class HomeControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(HomeController::class);
    }
}
