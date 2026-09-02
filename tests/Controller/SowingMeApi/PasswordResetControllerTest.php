<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\SowingMeApi;

use Ubix\Controller\SowingMeApi\PasswordResetController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\SowingMeApi\PasswordResetController
 *
 * @coversDefaultClass \Ubix\Controller\SowingMeApi\PasswordResetController
 */
final class PasswordResetControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PasswordResetController::class);
    }
}
