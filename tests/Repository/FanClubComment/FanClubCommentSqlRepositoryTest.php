<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\FanClubComment;

use Ubix\Repository\FanClubComment\FanClubCommentSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\FanClubComment\FanClubCommentSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\FanClubComment\FanClubCommentSqlRepository
 */
final class FanClubCommentSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FanClubCommentSqlRepository::class);
    }
}
