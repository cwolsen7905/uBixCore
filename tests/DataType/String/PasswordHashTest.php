<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\String;

use Ubix\DataType\String\PasswordHash;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\String\PasswordHash
 *
 * @coversDefaultClass \Ubix\DataType\String\PasswordHash
 */
final class PasswordHashTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PasswordHash::class);
    }
}
