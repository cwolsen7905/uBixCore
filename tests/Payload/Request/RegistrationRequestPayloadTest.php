<?php

declare(strict_types=1);

namespace Ubix\Tests\Payload\Request;

use Ubix\Payload\Request\RegistrationRequestPayload;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Payload\Request\RegistrationRequestPayload
 *
 * @coversDefaultClass \Ubix\Payload\Request\RegistrationRequestPayload
 */
final class RegistrationRequestPayloadTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RegistrationRequestPayload::class);
    }
}
