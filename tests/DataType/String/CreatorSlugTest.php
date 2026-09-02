<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\String;

use Ubix\DataType\String\CreatorSlug;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\String\CreatorSlug
 *
 * @coversDefaultClass \Ubix\DataType\String\CreatorSlug
 */
final class CreatorSlugTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CreatorSlug::class);
    }
}
