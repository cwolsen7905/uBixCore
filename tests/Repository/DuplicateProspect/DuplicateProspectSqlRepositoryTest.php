<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\DuplicateProspect;

use Ubix\Repository\DuplicateProspect\DuplicateProspectSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\DuplicateProspect\DuplicateProspectSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\DuplicateProspect\DuplicateProspectSqlRepository
 */
final class DuplicateProspectSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DuplicateProspectSqlRepository::class);
    }
}
