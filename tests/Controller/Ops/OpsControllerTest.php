<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\Ops;

use Ubix\Controller\Ops\OpsController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\Ops\OpsController
 *
 * @coversDefaultClass \Ubix\Controller\Ops\OpsController
 */
final class OpsControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(OpsController::class);
    }
}
