<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\SqlRepository\UserOptions;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\SqlRepository\UserOptions
 *
 * @coversDefaultClass \Ubix\DataTransferObject\SqlRepository\UserOptions
 */
final class UserOptionsTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UserOptions::class);
    }
}
