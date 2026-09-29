<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Ubix\Service\NativeProcessService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\NativeProcessService
 *
 * @coversDefaultClass \Ubix\Service\NativeProcessService
 */
final class NativeProcessServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(NativeProcessService::class);
    }
}
