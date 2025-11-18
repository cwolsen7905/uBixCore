<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Ubix\Service\MachineCodeReviewService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\MachineCodeReviewService
 *
 * @coversDefaultClass \Ubix\Service\MachineCodeReviewService
 */
final class MachineCodeReviewServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MachineCodeReviewService::class);
    }
}
