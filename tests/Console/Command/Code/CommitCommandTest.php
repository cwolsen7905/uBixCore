<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Code;

use Ubix\Console\Command\Code\CommitCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Code\CommitCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Code\CommitCommand
 */
final class CommitCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CommitCommand::class);
    }
}
