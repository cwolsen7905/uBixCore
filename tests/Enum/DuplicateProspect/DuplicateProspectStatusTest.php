<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\DuplicateProspect;

use Ubix\Enum\DuplicateProspect\DuplicateProspectStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\DuplicateProspect\DuplicateProspectStatus
 *
 * @coversDefaultClass \Ubix\Enum\DuplicateProspect\DuplicateProspectStatus
 */
final class DuplicateProspectStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DuplicateProspectStatus::class);
    }
}
