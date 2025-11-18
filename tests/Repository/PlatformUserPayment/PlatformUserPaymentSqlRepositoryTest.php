<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PlatformUserPayment;

use Ubix\Repository\PlatformUserPayment\PlatformUserPaymentSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PlatformUserPayment\PlatformUserPaymentSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PlatformUserPayment\PlatformUserPaymentSqlRepository
 */
final class PlatformUserPaymentSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PlatformUserPaymentSqlRepository::class);
    }
}
