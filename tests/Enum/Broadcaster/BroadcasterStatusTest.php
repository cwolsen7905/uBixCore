<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Broadcaster;

use Ubix\Enum\Broadcaster\BroadcasterStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Broadcaster\BroadcasterStatus
 *
 * @coversDefaultClass \Ubix\Enum\Broadcaster\BroadcasterStatus
 */
final class BroadcasterStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(BroadcasterStatus::class);
    }
}
