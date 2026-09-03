<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Cron;

use Ubix\Console\Command\Cron\ListCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Cron\ListCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Cron\ListCommand
 */
final class ListCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ListCommand::class);
    }
}
