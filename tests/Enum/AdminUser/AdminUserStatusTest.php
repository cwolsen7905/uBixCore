<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\AdminUser;

use Ubix\Enum\AdminUser\AdminUserStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\AdminUser\AdminUserStatus
 *
 * @coversDefaultClass \Ubix\Enum\AdminUser\AdminUserStatus
 */
final class AdminUserStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AdminUserStatus::class);
    }
}
