<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PlatformUser;

use Ubix\Repository\PlatformUser\PlatformUserSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PlatformUser\PlatformUserSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PlatformUser\PlatformUserSqlRepository
 */
final class PlatformUserSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PlatformUserSqlRepository::class);
    }
}
