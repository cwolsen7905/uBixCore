<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Broadcaster;

use Ubix\Repository\Broadcaster\BroadcasterSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Broadcaster\BroadcasterSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Broadcaster\BroadcasterSqlRepository
 */
final class BroadcasterSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(BroadcasterSqlRepository::class);
    }
}
