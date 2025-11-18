<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PendingPlatformUser;

use Ubix\Repository\PendingPlatformUser\PendingPlatformUserSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PendingPlatformUser\PendingPlatformUserSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PendingPlatformUser\PendingPlatformUserSqlRepository
 */
final class PendingPlatformUserSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PendingPlatformUserSqlRepository::class);
    }
}
