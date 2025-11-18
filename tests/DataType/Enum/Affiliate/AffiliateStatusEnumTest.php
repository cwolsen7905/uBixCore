<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Enum\Affiliate;

use Ubix\DataType\Enum\Affiliate\AffiliateStatusEnum;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Enum\Affiliate\AffiliateStatusEnum
 *
 * @coversDefaultClass \Ubix\DataType\Enum\Affiliate\AffiliateStatusEnum
 */
final class AffiliateStatusEnumTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateStatusEnum::class);
    }
}
