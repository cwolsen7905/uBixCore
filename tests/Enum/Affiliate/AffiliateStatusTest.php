<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Affiliate;

use Ubix\Enum\Affiliate\AffiliateStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Affiliate\AffiliateStatus
 *
 * @coversDefaultClass \Ubix\Enum\Affiliate\AffiliateStatus
 */
final class AffiliateStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateStatus::class);
    }
}
