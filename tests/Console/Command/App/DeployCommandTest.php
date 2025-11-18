<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\App;

use Ubix\Console\Command\App\DeployCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\App\DeployCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\App\DeployCommand
 */
final class DeployCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DeployCommand::class);
    }
}
