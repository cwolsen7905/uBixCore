<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Affiliate;

use Ubix\Enum\Affiliate\AffiliateReferrerStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Affiliate\AffiliateReferrerStatus
 *
 * @coversDefaultClass \Ubix\Enum\Affiliate\AffiliateReferrerStatus
 */
final class AffiliateReferrerStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateReferrerStatus::class);
    }
}
