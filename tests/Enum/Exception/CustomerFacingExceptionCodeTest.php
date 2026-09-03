<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Exception;

use ReflectionClass;
use Ubix\Enum\Exception\CustomerFacingExceptionCode;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Exception\CustomerFacingExceptionCode
 *
 * @coversDefaultClass \Ubix\Enum\Exception\CustomerFacingExceptionCode
 */
final class CustomerFacingExceptionCodeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CustomerFacingExceptionCode::class);
    }

    /**
     * Test that the enum is exclusively unique integers
     *
     * @return void
     */
    public function testExceptionCodesAreUniqueIntegers(): void
    {
        $class = new ReflectionClass(CustomerFacingExceptionCode::class);

        $this->assertNotEmpty($class->getConstants());

        $values = [];

        foreach ($class->getConstants() as $name => $value) {
            assert($value instanceof CustomerFacingExceptionCode);

            $this->assertNotContains(
                $value->value,
                $values,
                'Customer facing exception codes must be unique but `' . $name . '` reuses `' . (string)$value->value . '`',
            );

            $this->assertIsInt(
                $value->value,
                'Customer facing exception codes must be integers but `' . $name . '` is of type `' . gettype($value->value) . '`',
            );

            $values[] = $value->value;
        }
    }
}
