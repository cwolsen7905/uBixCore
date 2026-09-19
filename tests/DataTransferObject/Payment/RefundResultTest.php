<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Payment;

use Ubix\DataTransferObject\Payment\RefundResult;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Payment\RefundResult
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Payment\RefundResult
 */
final class RefundResultTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RefundResult::class);
    }
}
