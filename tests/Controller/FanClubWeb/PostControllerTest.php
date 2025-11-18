<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\FanClubWeb;

use Ubix\Controller\FanClubWeb\PostController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\FanClubWeb\PostController
 *
 * @coversDefaultClass \Ubix\Controller\FanClubWeb\PostController
 */
final class PostControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PostController::class);
    }
}
