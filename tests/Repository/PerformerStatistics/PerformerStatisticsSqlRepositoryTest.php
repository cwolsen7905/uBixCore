<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PerformerStatistics;

use Ubix\Repository\PerformerStatistics\PerformerStatisticsSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PerformerStatistics\PerformerStatisticsSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PerformerStatistics\PerformerStatisticsSqlRepository
 */
final class PerformerStatisticsSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PerformerStatisticsSqlRepository::class);
    }
}
