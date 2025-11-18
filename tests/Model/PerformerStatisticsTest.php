<?php

declare(strict_types=1);

namespace Ubix\Tests\Model;

use Ubix\Model\PerformerStatistics;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Model\PerformerStatistics
 *
 * @coversDefaultClass \Ubix\Model\PerformerStatistics
 */
final class PerformerStatisticsTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PerformerStatistics::class);
    }
}
