<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Payment;

use Ubix\DataTransferObject\Payment\CardSummary;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Payment\CardSummary
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Payment\CardSummary
 */
final class CardSummaryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CardSummary::class);
    }
}
