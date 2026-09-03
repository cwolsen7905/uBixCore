<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Int;

use Ubix\DataType\Int\MediumInt;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Int\MediumInt
 *
 * @coversDefaultClass \Ubix\DataType\Int\MediumInt
 */
final class MediumIntTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MediumInt::class);
    }
}
