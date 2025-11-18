<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\ScreenName;

use Ubix\Repository\ScreenName\ScreenNameSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\ScreenName\ScreenNameSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\ScreenName\ScreenNameSqlRepository
 */
final class ScreenNameSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ScreenNameSqlRepository::class);
    }
}
