<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Migrate;

use Ubix\Console\Command\Migrate\ReconcileCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Migrate\ReconcileCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Migrate\ReconcileCommand
 * @coversDefaultClass \Ubis\Console\Command\Migrate\ReconcileCommand
 */
final class ReconcileCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ReconcileCommand::class);
    }
}
