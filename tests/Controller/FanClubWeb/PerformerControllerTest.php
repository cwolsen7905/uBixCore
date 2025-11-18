<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\FanClubWeb;

use Ubix\Controller\FanClubWeb\PerformerController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\FanClubWeb\PerformerController
 *
 * @coversDefaultClass \Ubix\Controller\FanClubWeb\PerformerController
 */
final class PerformerControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PerformerController::class);
    }
}
