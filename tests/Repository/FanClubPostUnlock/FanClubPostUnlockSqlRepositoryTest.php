<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\FanClubPostUnlock;

use Ubix\Repository\FanClubPostUnlock\FanClubPostUnlockSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\FanClubPostUnlock\FanClubPostUnlockSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\FanClubPostUnlock\FanClubPostUnlockSqlRepository
 */
final class FanClubPostUnlockSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FanClubPostUnlockSqlRepository::class);
    }
}
