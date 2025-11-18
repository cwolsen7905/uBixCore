<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Message;

use Ubix\Enum\Message\MessageFolderId;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Message\MessageFolderId
 *
 * @coversDefaultClass \Ubix\Enum\Message\MessageFolderId
 */
final class MessageFolderIdTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MessageFolderId::class);
    }
}
