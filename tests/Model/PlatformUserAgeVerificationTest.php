<?php

declare(strict_types=1);

namespace Ubix\Tests\Model;

use Ubix\Model\PlatformUserAgeVerification;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Model\PlatformUserAgeVerification
 *
 * @coversDefaultClass \Ubix\Model\PlatformUserAgeVerification
 */
final class PlatformUserAgeVerificationTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PlatformUserAgeVerification::class);
    }
}
