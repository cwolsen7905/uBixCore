<?php

declare(strict_types=1);

namespace Ubix\Tests\Model;

use Ubix\Model\MachineCodeReview;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Model\MachineCodeReview
 *
 * @coversDefaultClass \Ubix\Model\MachineCodeReview
 */
final class MachineCodeReviewTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MachineCodeReview::class);
    }
}
