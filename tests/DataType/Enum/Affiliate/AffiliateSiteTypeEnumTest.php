<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\Enum\Affiliate;

use Ubix\DataType\Enum\Affiliate\AffiliateSiteTypeEnum;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\Enum\Affiliate\AffiliateSiteTypeEnum
 *
 * @coversDefaultClass \Ubix\DataType\Enum\Affiliate\AffiliateSiteTypeEnum
 */
final class AffiliateSiteTypeEnumTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateSiteTypeEnum::class);
    }
}
