<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Geolocation;

use Ubix\Service\Geolocation\UbixGeolocationService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Geolocation\UbixGeolocationService
 *
 * @coversDefaultClass \Ubix\Service\Geolocation\UbixGeolocationService
 */
final class UbixGeolocationServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UbixGeolocationService::class);
    }
}
