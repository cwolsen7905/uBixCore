<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\BillingTransaction;

use Ubix\Repository\BillingTransaction\BillingTransactionSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\BillingTransaction\BillingTransactionSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\BillingTransaction\BillingTransactionSqlRepository
 */
final class BillingTransactionSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(BillingTransactionSqlRepository::class);
    }
}
