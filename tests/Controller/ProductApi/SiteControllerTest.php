<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\ProductApi;

use Ubix\Controller\ProductApi\SiteController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\ProductApi\SiteController
 *
 * @coversDefaultClass \Ubix\Controller\ProductApi\SiteController
 */
final class SiteControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SiteController::class);
    }
}
