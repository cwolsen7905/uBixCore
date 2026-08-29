<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\String;

use Ubix\DataType\String\FirstName;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\String\FirstName
 *
 * @coversDefaultClass \Ubix\DataType\String\FirstName
 */
final class FirstNameTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FirstName::class);
    }
}
