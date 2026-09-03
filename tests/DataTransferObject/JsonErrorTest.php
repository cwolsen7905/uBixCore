<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject;

use Ubix\DataTransferObject\JsonError;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\JsonError
 *
 * @coversDefaultClass \Ubix\DataTransferObject\JsonError
 */
final class JsonErrorTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(JsonError::class);
    }
}
