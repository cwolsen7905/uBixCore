<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\AgeVerification;

use Ubix\Enum\AgeVerification\AgeVerificationRequirement;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\AgeVerification\AgeVerificationRequirement
 *
 * @coversDefaultClass \Ubix\Enum\AgeVerification\AgeVerificationRequirement
 */
final class AgeVerificationRequirementTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AgeVerificationRequirement::class);
    }
}
