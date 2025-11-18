<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\ProspectApplication;

use Ubix\Enum\ProspectApplication\ProspectApplicationIdentificationMethod;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\ProspectApplication\ProspectApplicationIdentificationMethod
 *
 * @coversDefaultClass \Ubix\Enum\ProspectApplication\ProspectApplicationIdentificationMethod
 */
final class ProspectApplicationIdentificationMethodTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectApplicationIdentificationMethod::class);
    }
}
