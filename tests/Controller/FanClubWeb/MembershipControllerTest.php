<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\FanClubWeb;

use Ubix\Controller\FanClubWeb\MembershipController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\FanClubWeb\MembershipController
 *
 * @coversDefaultClass \Ubix\Controller\FanClubWeb\MembershipController
 */
final class MembershipControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MembershipController::class);
    }
}
