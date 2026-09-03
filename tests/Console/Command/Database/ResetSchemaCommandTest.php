<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Database;

use Ubix\Console\Command\Database\ResetSchemaCommand;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Database\ResetSchemaCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Database\ResetSchemaCommand
 */
final class ResetSchemaCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ResetSchemaCommand::class);
    }
}
