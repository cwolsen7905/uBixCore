<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Exception;

use ReflectionClass;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Exception\ExceptionCode
 *
 * @coversDefaultClass \Ubix\Enum\Exception\ExceptionCode
 */
final class ExceptionCodeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ExceptionCode::class);
    }

    /**
     * Test that the enum is exclusively unique integers
     *
     * @return void
     */
    public function testExceptionCodesAreUniqueIntegers(): void
    {
        $class = new ReflectionClass(ExceptionCode::class);

        $this->assertNotEmpty($class->getConstants());

        $values = [];

        foreach ($class->getConstants() as $name => $value) {
            assert($value instanceof ExceptionCode);

            $this->assertNotContains(
                $value->value,
                $values,
                'Exception codes must be unique but `' . $name . '` reuses `' . (string)$value->value . '`',
            );

            $this->assertIsInt(
                $value->value,
                'Exception codes must be integers but `' . $name . '` is of type `' . gettype($value->value) . '`',
            );

            $values[] = $value->value;
        }
    }
}
