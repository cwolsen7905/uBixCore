<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\ModelSignupApi;

use Ubix\Controller\ModelSignupApi\ReasonsController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\ModelSignupApi\ReasonsController
 *
 * @coversDefaultClass \Ubix\Controller\ModelSignupApi\ReasonsController
 */
final class ReasonsControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ReasonsController::class);
    }
}
