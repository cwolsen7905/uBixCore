<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Identity;

use Ubix\DataTransferObject\Identity\FacebookSignedRequest;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Identity\FacebookSignedRequest
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Identity\FacebookSignedRequest
 */
final class FacebookSignedRequestTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FacebookSignedRequest::class);
    }
}
