<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\ModelSignupApi;

use Ubix\Controller\ModelSignupApi\StageNameController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\ModelSignupApi\StageNameController
 *
 * @coversDefaultClass \Ubix\Controller\ModelSignupApi\StageNameController
 */
final class StageNameControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(StageNameController::class);
    }
}
