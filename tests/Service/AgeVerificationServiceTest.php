<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Ubix\Service\AgeVerificationService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\AgeVerificationService
 *
 * @coversDefaultClass \Ubix\Service\AgeVerificationService
 */
final class AgeVerificationServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AgeVerificationService::class);
    }
}
