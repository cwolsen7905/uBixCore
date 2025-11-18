<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\FanClubLike;

use Ubix\Repository\FanClubLike\FanClubLikeSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\FanClubLike\FanClubLikeSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\FanClubLike\FanClubLikeSqlRepository
 */
final class FanClubLikeSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FanClubLikeSqlRepository::class);
    }
}
