<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Migration;

use Ubix\DataTransferObject\Migration\DestructiveStatementMatch;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Migration\DestructiveStatementMatch
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Migration\DestructiveStatementMatch
 * @coversDefaultClass \Ubis\DataTransferObject\Migration\DestructiveStatementMatch
 */
final class DestructiveStatementMatchTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DestructiveStatementMatch::class);
    }
}
