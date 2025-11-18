<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Bool;

use Ubix\DataType\Bool\NullableBoolean;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Bool\NullableBoolean
 *
 * @coversDefaultClass \Ubix\DataType\Bool\NullableBoolean
 */
final class NullableBooleanTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(NullableBoolean::class);
    }
}
