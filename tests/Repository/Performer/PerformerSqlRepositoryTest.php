<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Performer;

use Ubix\Repository\Performer\PerformerSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Performer\PerformerSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Performer\PerformerSqlRepository
 */
final class PerformerSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PerformerSqlRepository::class);
    }
}
