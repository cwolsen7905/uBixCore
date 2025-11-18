<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\FanClub;

use Ubix\Repository\FanClub\FanClubSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\FanClub\FanClubSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\FanClub\FanClubSqlRepository
 */
final class FanClubSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FanClubSqlRepository::class);
    }
}
