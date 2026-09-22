<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Payment;

use Ubix\DataTransferObject\Payment\CustomerRequest;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Payment\CustomerRequest
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Payment\CustomerRequest
 */
final class CustomerRequestTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CustomerRequest::class);
    }
}
