<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\SowingMeApi;

use Ubix\Controller\SowingMeApi\EmailConfirmationController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\SowingMeApi\EmailConfirmationController
 *
 * @coversDefaultClass \Ubix\Controller\SowingMeApi\EmailConfirmationController
 */
final class EmailConfirmationControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(EmailConfirmationController::class);
    }
}
