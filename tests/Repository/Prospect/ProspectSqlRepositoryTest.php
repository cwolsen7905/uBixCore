<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Prospect;

use Ubix\Repository\Prospect\ProspectSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Prospect\ProspectSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Prospect\ProspectSqlRepository
 */
final class ProspectSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectSqlRepository::class);
    }
}
