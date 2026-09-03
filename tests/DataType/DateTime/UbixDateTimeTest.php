<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\DateTime;

use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\DateTime\UbixDateTime
 *
 * @coversDefaultClass \Ubix\DataType\DateTime\UbixDateTime
 */
final class UbixDateTimeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UbixDateTime::class);
    }
}
