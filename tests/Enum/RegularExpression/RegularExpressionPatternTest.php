<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\RegularExpression;

use Ubix\Enum\RegularExpression\RegularExpressionPattern;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\RegularExpression\RegularExpressionPattern
 *
 * @coversDefaultClass \Ubix\Enum\RegularExpression\RegularExpressionPattern
 */
final class RegularExpressionPatternTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RegularExpressionPattern::class);
    }
}
