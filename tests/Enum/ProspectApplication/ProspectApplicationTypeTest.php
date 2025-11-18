<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\ProspectApplication;

use Ubix\Enum\ProspectApplication\ProspectApplicationType;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\ProspectApplication\ProspectApplicationType
 *
 * @coversDefaultClass \Ubix\Enum\ProspectApplication\ProspectApplicationType
 */
final class ProspectApplicationTypeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectApplicationType::class);
    }
}
