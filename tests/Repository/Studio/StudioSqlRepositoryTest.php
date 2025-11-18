<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Studio;

use Ubix\Repository\Studio\StudioSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Studio\StudioSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Studio\StudioSqlRepository
 */
final class StudioSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(StudioSqlRepository::class);
    }
}
