<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\User;

use Ubix\Enum\User\UserStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\User\UserStatus
 *
 * @coversDefaultClass \Ubix\Enum\User\UserStatus
 */
final class UserStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UserStatus::class);
    }
}
