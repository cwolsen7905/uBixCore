<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Audit;

use Ubix\DataTransferObject\Audit\PiiAccess;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Audit\PiiAccess
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Audit\PiiAccess
 */
final class PiiAccessTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PiiAccess::class);
    }
}
