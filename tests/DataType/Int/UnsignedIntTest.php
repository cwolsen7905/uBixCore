<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Int;

use Ubix\DataType\Int\UnsignedInt;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Int\UnsignedInt
 *
 * @coversDefaultClass \Ubix\DataType\Int\UnsignedInt
 */
final class UnsignedIntTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UnsignedInt::class);
    }
}
