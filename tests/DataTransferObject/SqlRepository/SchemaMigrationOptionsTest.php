<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\SqlRepository\SchemaMigrationOptions;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\SqlRepository\SchemaMigrationOptions
 *
 * @coversDefaultClass \Ubix\DataTransferObject\SqlRepository\SchemaMigrationOptions
 */
final class SchemaMigrationOptionsTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SchemaMigrationOptions::class);
    }
}
