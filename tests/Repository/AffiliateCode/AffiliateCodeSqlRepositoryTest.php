<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\AffiliateCode;

use Ubix\Repository\AffiliateCode\AffiliateCodeSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\AffiliateCode\AffiliateCodeSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\AffiliateCode\AffiliateCodeSqlRepository
 */
final class AffiliateCodeSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateCodeSqlRepository::class);
    }
}
