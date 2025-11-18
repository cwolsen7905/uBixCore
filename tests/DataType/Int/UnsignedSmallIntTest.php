<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Int;

use Ubix\DataType\Int\UnsignedSmallInt;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Int\UnsignedSmallInt
 *
 * @coversDefaultClass \Ubix\DataType\Int\UnsignedSmallInt
 */
final class UnsignedSmallIntTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UnsignedSmallInt::class);
    }
}
