<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\SowingMeWeb;

use Ubix\Controller\SowingMeWeb\SowingMeWebController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\SowingMeWeb\SowingMeWebController
 *
 * @coversDefaultClass \Ubix\Controller\SowingMeWeb\SowingMeWebController
 */
final class SowingMeWebControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SowingMeWebController::class);
    }
}
