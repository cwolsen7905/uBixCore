<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Cron\Affiliates;

use Ubix\Console\Command\Cron\Affiliates\ExampleTwoCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Cron\Affiliates\ExampleTwoCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Cron\Affiliates\ExampleTwoCommand
 */
final class ExampleTwoCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ExampleTwoCommand::class);
    }
}
