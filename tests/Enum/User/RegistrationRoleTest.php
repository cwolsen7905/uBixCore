<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\User;

use Ubix\Enum\User\RegistrationRole;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\User\RegistrationRole
 *
 * @coversDefaultClass \Ubix\Enum\User\RegistrationRole
 */
final class RegistrationRoleTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RegistrationRole::class);
    }
}
