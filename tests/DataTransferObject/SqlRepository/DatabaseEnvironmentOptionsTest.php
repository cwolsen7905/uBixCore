<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\SqlRepository\DatabaseEnvironmentOptions;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\SqlRepository\DatabaseEnvironmentOptions
 *
 * @coversDefaultClass \Ubix\DataTransferObject\SqlRepository\DatabaseEnvironmentOptions
 */
final class DatabaseEnvironmentOptionsTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DatabaseEnvironmentOptions::class);
    }
}
