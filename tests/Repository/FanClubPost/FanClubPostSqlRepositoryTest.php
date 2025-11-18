<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\FanClubPost;

use Ubix\Repository\FanClubPost\FanClubPostSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\FanClubPost\FanClubPostSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\FanClubPost\FanClubPostSqlRepository
 */
final class FanClubPostSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FanClubPostSqlRepository::class);
    }
}
