<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\K8s;

use Ubix\Console\Command\K8s\DeployMariadbCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\K8s\DeployMariadbCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\K8s\DeployMariadbCommand
 */
final class DeployMariadbCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DeployMariadbCommand::class);
    }
}
