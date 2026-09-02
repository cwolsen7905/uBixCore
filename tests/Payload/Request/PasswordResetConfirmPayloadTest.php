<?php

declare(strict_types=1);

namespace Ubix\Tests\Payload\Request;

use Ubix\Payload\Request\PasswordResetConfirmPayload;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Payload\Request\PasswordResetConfirmPayload
 *
 * @coversDefaultClass \Ubix\Payload\Request\PasswordResetConfirmPayload
 */
final class PasswordResetConfirmPayloadTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PasswordResetConfirmPayload::class);
    }
}
