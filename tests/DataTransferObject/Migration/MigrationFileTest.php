<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Migration;

use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\DataTransferObject\Migration\MigrationFile
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Migration\MigrationFile
 * @coversDefaultClass \Ubis\DataTransferObject\Migration\MigrationFile
 */
final class MigrationFileTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationFile::class);
    }
}
