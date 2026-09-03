<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Migration;

use Ubix\Enum\Migration\DestructiveStatementKind;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Migration\DestructiveStatementKind
 *
 * @coversDefaultClass \Ubix\Enum\Migration\DestructiveStatementKind
 * @coversDefaultClass \Ubis\Enum\Migration\DestructiveStatementKind
 */
final class DestructiveStatementKindTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DestructiveStatementKind::class);
    }
}
