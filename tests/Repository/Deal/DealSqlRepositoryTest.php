<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Deal;

use Ubix\Repository\Deal\DealSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Deal\DealSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Deal\DealSqlRepository
 */
final class DealSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DealSqlRepository::class);
    }
}
