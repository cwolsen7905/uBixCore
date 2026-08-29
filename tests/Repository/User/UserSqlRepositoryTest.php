<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\User;

use Ubix\Repository\User\UserSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\User\UserSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\User\UserSqlRepository
 */
final class UserSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UserSqlRepository::class);
    }
}
