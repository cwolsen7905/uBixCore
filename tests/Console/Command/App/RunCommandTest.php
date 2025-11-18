<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\App;

use Ubix\Console\Command\App\RunCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\App\RunCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\App\RunCommand
 */
final class RunCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RunCommand::class);
    }
}
