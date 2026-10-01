<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Exception\DtoException;
use Ubix\Service\JsonService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\JsonService
 *
 * @coversDefaultClass \Ubix\Service\JsonService
 */
final class JsonServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(JsonService::class);
    }

    /**
     * Flags shape the output; without them it is as before
     *
     * @return void
     */
    public function testEncodeHonoursFlags(): void
    {
        $service = new JsonService($this->createStub(Logger::class));
        $value   = ['name' => 'Café — Grace', 'url' => 'https://example.test/a'];

        $this->assertSame('{"name":"Caf\\u00e9 \\u2014 Grace","url":"https:\\/\\/example.test\\/a"}', $service->encode($value));
        $this->assertSame("{\n    \"name\": \"Café — Grace\",\n    \"url\": \"https://example.test/a\"\n}", $service->encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * JSON_THROW_ON_ERROR cannot bypass the service's own failure report
     *
     * @return void
     */
    public function testEncodeStillReportsFailuresItsOwnWay(): void
    {
        $this->expectException(DtoException::class);

        (new JsonService($this->createStub(Logger::class)))->encode("\xB1\x31", JSON_THROW_ON_ERROR);
    }
}
