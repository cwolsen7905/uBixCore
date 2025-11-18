<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\MachineCodeReview;

use Ubix\Enum\MachineCodeReview\MachineCodeReviewTool;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\MachineCodeReview\MachineCodeReviewTool
 *
 * @coversDefaultClass \Ubix\Enum\MachineCodeReview\MachineCodeReviewTool
 */
final class MachineCodeReviewToolTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MachineCodeReviewTool::class);
    }
}
