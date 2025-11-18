<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Ubix\Service\AccountAuthenticationService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\AccountAuthenticationService
 *
 * @coversDefaultClass \Ubix\Service\AccountAuthenticationService
 */
final class AccountAuthenticationServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AccountAuthenticationService::class);
    }
}
