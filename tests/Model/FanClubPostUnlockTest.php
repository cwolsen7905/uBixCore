<?php

declare(strict_types=1);

namespace Ubix\Tests\Model;

use Ubix\Model\FanClubPostUnlock;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Model\FanClubPostUnlock
 *
 * @coversDefaultClass \Ubix\Model\FanClubPostUnlock
 */
final class FanClubPostUnlockTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FanClubPostUnlock::class);
    }
}
