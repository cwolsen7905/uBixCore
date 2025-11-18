<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\InternalAdminApi;

use Ubix\Controller\InternalAdminApi\AuthController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\InternalAdminApi\AuthController
 *
 * @coversDefaultClass \Ubix\Controller\InternalAdminApi\AuthController
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
