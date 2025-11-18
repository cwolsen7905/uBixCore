<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\DateTime;

use Ubix\DataType\DateTime\NullableUbixDateTime;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\DateTime\NullableUbixDateTime
 *
 * @coversDefaultClass \Ubix\DataType\DateTime\NullableUbixDateTime
 */
final class NullableUbixDateTimeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(NullableUbixDateTime::class);
    }
}
