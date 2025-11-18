<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\FanClubMembership;

use Ubix\Repository\FanClubMembership\FanClubMembershipSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\FanClubMembership\FanClubMembershipSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\FanClubMembership\FanClubMembershipSqlRepository
 */
final class FanClubMembershipSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FanClubMembershipSqlRepository::class);
    }
}
