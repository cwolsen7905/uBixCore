<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PlatformUserLoginAttempt;

use Ubix\Repository\PlatformUserLoginAttempt\PlatformUserLoginAttemptSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PlatformUserLoginAttempt\PlatformUserLoginAttemptSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PlatformUserLoginAttempt\PlatformUserLoginAttemptSqlRepository
 */
final class PlatformUserLoginAttemptSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PlatformUserLoginAttemptSqlRepository::class);
    }
}
