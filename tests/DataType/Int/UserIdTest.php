<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Int;

use Ubix\DataType\Int\UserId;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Int\UserId
 *
 * @coversDefaultClass \Ubix\DataType\Int\UserId
 */
final class UserIdTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UserId::class);
    }
}
