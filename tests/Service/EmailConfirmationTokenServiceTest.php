<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Ubix\Service\EmailConfirmationTokenService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\EmailConfirmationTokenService
 *
 * @coversDefaultClass \Ubix\Service\EmailConfirmationTokenService
 */
final class EmailConfirmationTokenServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(EmailConfirmationTokenService::class);
    }
}
