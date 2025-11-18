<?php

declare(strict_types=1);

namespace Ubix\Tests\HttpClient;

use Ubix\HttpClient\CurlHttpClient;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\HttpClient\CurlHttpClient
 *
 * @coversDefaultClass \Ubix\HttpClient\CurlHttpClient
 */
final class CurlHttpClientTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CurlHttpClient::class);
    }
}
