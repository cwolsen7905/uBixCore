<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Tier;

use Ubix\Repository\Tier\TierSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Tier\TierSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Tier\TierSqlRepository
 */
final class TierSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TierSqlRepository::class);
    }
}
