<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Migrate;

use Ubix\Console\Command\Migrate\StatusCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Migrate\StatusCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Migrate\StatusCommand
 * @coversDefaultClass \Ubis\Console\Command\Migrate\StatusCommand
 */
final class StatusCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(StatusCommand::class);
    }
}
