<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Int;

use Ubix\DataType\Int\TinyInt;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Int\TinyInt
 *
 * @coversDefaultClass \Ubix\DataType\Int\TinyInt
 */
final class TinyIntTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TinyInt::class);
    }
}
