<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PlatformWhiteLabel;

use Ubix\Repository\PlatformWhiteLabel\PlatformWhiteLabelSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PlatformWhiteLabel\PlatformWhiteLabelSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PlatformWhiteLabel\PlatformWhiteLabelSqlRepository
 */
final class PlatformWhiteLabelSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PlatformWhiteLabelSqlRepository::class);
    }
}
