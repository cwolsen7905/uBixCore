<?php

declare(strict_types=1);

namespace Ubix\Tests\Model;

use Ubix\Model\DuplicateProspect;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Model\DuplicateProspect
 *
 * @coversDefaultClass \Ubix\Model\DuplicateProspect
 */
final class DuplicateProspectTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DuplicateProspect::class);
    }
}
