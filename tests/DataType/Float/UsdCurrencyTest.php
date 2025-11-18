<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Float;

use Ubix\DataType\Float\UsdCurrency;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Float\UsdCurrency
 *
 * @coversDefaultClass \Ubix\DataType\Float\UsdCurrency
 */
final class UsdCurrencyTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UsdCurrency::class);
    }
}
