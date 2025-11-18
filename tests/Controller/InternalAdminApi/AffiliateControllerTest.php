<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\InternalAdminApi;

use Ubix\Controller\InternalAdminApi\AffiliateController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\InternalAdminApi\AffiliateController
 *
 * @coversDefaultClass \Ubix\Controller\InternalAdminApi\AffiliateController
 */
final class AffiliateControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateController::class);
    }
}
