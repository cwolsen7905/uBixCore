<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\SowingMeApi;

use Ubix\Controller\SowingMeApi\AuthController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\SowingMeApi\AuthController
 *
 * @coversDefaultClass \Ubix\Controller\SowingMeApi\AuthController
 */
final class AuthControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AuthController::class);
    }
}
