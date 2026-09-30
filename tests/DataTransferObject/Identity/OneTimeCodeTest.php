<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Identity;

use Ubix\DataTransferObject\Identity\OneTimeCode;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Identity\OneTimeCode
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Identity\OneTimeCode
 */
final class OneTimeCodeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(OneTimeCode::class);
    }
}
