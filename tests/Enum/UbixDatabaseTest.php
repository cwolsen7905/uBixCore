<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum;

use Ubix\Enum\UbixDatabase;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\UbixDatabase
 *
 * @coversDefaultClass \Ubix\Enum\UbixDatabase
 */
final class UbixDatabaseTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UbixDatabase::class);
    }
}
