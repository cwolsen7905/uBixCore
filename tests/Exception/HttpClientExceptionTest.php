<?php

declare(strict_types=1);

namespace Ubix\Tests\Exception;

use Ubix\Exception\HttpClientException;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Exception\HttpClientException
 *
 * @coversDefaultClass \Ubix\Exception\HttpClientException
 */
final class HttpClientExceptionTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(HttpClientException::class);
    }
}
