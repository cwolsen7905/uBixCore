<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Realtime;

use Ubix\DataTransferObject\Realtime\NatsConnectionParameters;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Realtime\NatsConnectionParameters
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Realtime\NatsConnectionParameters
 */
final class NatsConnectionParametersTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(NatsConnectionParameters::class);
    }
}
