<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\NetworkingService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\NetworkingService
 *
 * @coversDefaultClass \Ubix\Service\NetworkingService
 * @see                \Ubix\Tests\Tests\Service\NetworkingServiceTestTest PHPUnit test case
 */
final class NetworkingServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(NetworkingService::class);
    }

    /**
     * With no host configuration nothing is internal and nothing is blocked
     *
     * @return void
     */
    public function testShipsNoNetworksOrBlocks(): void
    {
        $service = new NetworkingService($this->createStub(Logger::class));

        $this->assertFalse($service->isInternalIpAddress('10.1.2.3'));
        $this->assertFalse($service->isBlockedIpAddress('203.0.113.7'));
    }

    /**
     * Host-supplied CIDR ranges and prefixes are honoured
     *
     * @return void
     */
    public function testHonoursHostSuppliedLists(): void
    {
        $service = new NetworkingService($this->createStub(Logger::class), ['10.0.0.0/8', '192.168.1'], ['203.0.113']);

        $this->assertTrue($service->isInternalIpAddress('10.20.30.40'));
        $this->assertTrue($service->isInternalIpAddress('192.168.1.99'));
        $this->assertFalse($service->isInternalIpAddress('172.16.0.1'));
        $this->assertTrue($service->isBlockedIpAddress('203.0.113.7'));
        $this->assertFalse($service->isBlockedIpAddress('198.51.100.1'));
    }
}
