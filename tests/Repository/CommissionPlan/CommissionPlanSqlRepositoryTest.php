<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\CommissionPlan;

use Ubix\Repository\CommissionPlan\CommissionPlanSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\CommissionPlan\CommissionPlanSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\CommissionPlan\CommissionPlanSqlRepository
 */
final class CommissionPlanSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CommissionPlanSqlRepository::class);
    }
}
