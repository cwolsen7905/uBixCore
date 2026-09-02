<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Creator;

use Ubix\Enum\Creator\CreatorCategory;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Creator\CreatorCategory
 *
 * @coversDefaultClass \Ubix\Enum\Creator\CreatorCategory
 */
final class CreatorCategoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CreatorCategory::class);
    }
}
