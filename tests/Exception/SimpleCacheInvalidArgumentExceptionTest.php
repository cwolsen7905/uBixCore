<?php

declare(strict_types=1);

namespace Ubix\Tests\Exception;

use Ubix\Exception\SimpleCacheInvalidArgumentException;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Exception\SimpleCacheInvalidArgumentException
 *
 * @coversDefaultClass \Ubix\Exception\SimpleCacheInvalidArgumentException
 */
final class SimpleCacheInvalidArgumentExceptionTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SimpleCacheInvalidArgumentException::class);
    }
}
