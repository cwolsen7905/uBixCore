<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject;

use Ubix\DataTransferObject\CreatorProfileResolution;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\CreatorProfileResolution
 *
 * @coversDefaultClass \Ubix\DataTransferObject\CreatorProfileResolution
 */
final class CreatorProfileResolutionTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CreatorProfileResolution::class);
    }
}
