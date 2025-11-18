<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PlatformUserAgeVerification;

use Ubix\Repository\PlatformUserAgeVerification\PlatformUserAgeVerificationSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PlatformUserAgeVerification\PlatformUserAgeVerificationSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PlatformUserAgeVerification\PlatformUserAgeVerificationSqlRepository
 */
final class PlatformUserAgeVerificationSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PlatformUserAgeVerificationSqlRepository::class);
    }
}
