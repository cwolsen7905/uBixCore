<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Migration;

use Ubix\DataTransferObject\Migration\MigrationStatusEntry;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\DataTransferObject\Migration\MigrationStatusEntry
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Migration\MigrationStatusEntry
 * @coversDefaultClass \Ubis\DataTransferObject\Migration\MigrationStatusEntry
 */
final class MigrationStatusEntryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationStatusEntry::class);
    }
}
