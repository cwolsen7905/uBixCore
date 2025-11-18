<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PlatformUserVelocityCount;

use Ubix\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountSqlRepository
 */
final class PlatformUserVelocityCountSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PlatformUserVelocityCountSqlRepository::class);
    }
}
