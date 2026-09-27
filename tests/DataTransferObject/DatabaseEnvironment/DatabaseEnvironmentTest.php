<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\DatabaseEnvironment;

use Ubix\DataTransferObject\DatabaseEnvironment\DatabaseEnvironment;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\DatabaseEnvironment\DatabaseEnvironment
 *
 * @coversDefaultClass \Ubix\DataTransferObject\DatabaseEnvironment\DatabaseEnvironment
 */
final class DatabaseEnvironmentTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DatabaseEnvironment::class);
    }
}
