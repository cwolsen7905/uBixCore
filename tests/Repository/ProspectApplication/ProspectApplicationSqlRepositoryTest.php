<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\ProspectApplication;

use Ubix\Repository\ProspectApplication\ProspectApplicationSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\ProspectApplication\ProspectApplicationSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\ProspectApplication\ProspectApplicationSqlRepository
 */
final class ProspectApplicationSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectApplicationSqlRepository::class);
    }
}
