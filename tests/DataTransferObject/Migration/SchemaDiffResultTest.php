<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Migration;

use Ubix\DataTransferObject\Migration\SchemaDiffResult;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Migration\SchemaDiffResult
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Migration\SchemaDiffResult
 * @coversDefaultClass \Ubis\DataTransferObject\Migration\SchemaDiffResult
 */
final class SchemaDiffResultTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SchemaDiffResult::class);
    }
}
