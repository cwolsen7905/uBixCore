<?php

declare(strict_types=1);

namespace Ubix\Tests\Payload\Response;

use Ubix\Payload\Response\RegistrationResponsePayload;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Payload\Response\RegistrationResponsePayload
 *
 * @coversDefaultClass \Ubix\Payload\Response\RegistrationResponsePayload
 */
final class RegistrationResponsePayloadTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RegistrationResponsePayload::class);
    }
}
