<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\App;

use Ubix\Console\Command\App\BuildCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\App\BuildCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\App\BuildCommand
 */
final class BuildCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(BuildCommand::class);
    }
}
