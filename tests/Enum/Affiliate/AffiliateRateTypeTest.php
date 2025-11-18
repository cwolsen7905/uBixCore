<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Affiliate;

use Ubix\Enum\Affiliate\AffiliateRateType;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Affiliate\AffiliateRateType
 *
 * @coversDefaultClass \Ubix\Enum\Affiliate\AffiliateRateType
 */
final class AffiliateRateTypeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateRateType::class);
    }
}
