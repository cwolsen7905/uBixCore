<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\ProspectApplication;

use Ubix\Enum\ProspectApplication\ProspectApplicationGender;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\ProspectApplication\ProspectApplicationGender
 *
 * @coversDefaultClass \Ubix\Enum\ProspectApplication\ProspectApplicationGender
 */
final class ProspectApplicationGenderTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectApplicationGender::class);
    }
}
