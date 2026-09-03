<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Country;

use Ubix\Repository\Country\CountryHardCodedRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Country\CountryHardCodedRepository
 *
 * @coversDefaultClass \Ubix\Repository\Country\CountryHardCodedRepository
 */
final class CountryHardCodedRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CountryHardCodedRepository::class);
    }
}
