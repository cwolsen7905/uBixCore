<?php

declare(strict_types=1);

namespace Ubix\Tests\Model;

use Ubix\Model\ProspectApplicationSection;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Model\ProspectApplicationSection
 *
 * @coversDefaultClass \Ubix\Model\ProspectApplicationSection
 */
final class ProspectApplicationSectionTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectApplicationSection::class);
    }
}
