<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Cookie;

use Ubix\Enum\Cookie\CookieSameSite;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Cookie\CookieSameSite
 *
 * @coversDefaultClass \Ubix\Enum\Cookie\CookieSameSite
 */
final class CookieSameSiteTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CookieSameSite::class);
    }
}
