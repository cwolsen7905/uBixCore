<?php

declare(strict_types=1);

namespace Ubix\Tests\Controller\AffiliateApi;

use Ubix\Controller\AffiliateApi\AttributionController;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Controller\AffiliateApi\AttributionController
 *
 * @coversDefaultClass \Ubix\Controller\AffiliateApi\AttributionController
 */
final class AttributionControllerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AttributionController::class);
    }
}
