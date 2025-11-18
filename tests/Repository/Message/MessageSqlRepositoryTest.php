<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Message;

use Ubix\Repository\Message\MessageSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Message\MessageSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Message\MessageSqlRepository
 */
final class MessageSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MessageSqlRepository::class);
    }
}
