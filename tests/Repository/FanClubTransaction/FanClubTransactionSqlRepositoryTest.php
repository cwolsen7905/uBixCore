<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\FanClubTransaction;

use Ubix\Repository\FanClubTransaction\FanClubTransactionSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\FanClubTransaction\FanClubTransactionSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\FanClubTransaction\FanClubTransactionSqlRepository
 */
final class FanClubTransactionSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FanClubTransactionSqlRepository::class);
    }
}
