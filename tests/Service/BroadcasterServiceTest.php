<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Ubix\Service\BroadcasterService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\BroadcasterService
 *
 * @coversDefaultClass \Ubix\Service\BroadcasterService
 */
final class BroadcasterServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(BroadcasterService::class);
    }
}
