<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Payment;

use Ubix\DataTransferObject\Payment\ProviderSettlement;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Payment\ProviderSettlement
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Payment\ProviderSettlement
 */
final class ProviderSettlementTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProviderSettlement::class);
    }

    /**
     * The settlement currency is its own field, not assumed to be the payment's
     *
     * A converted charge settles in the account's currency rather than the one
     * the supporter paid in, so a caller that reuses the payment's currency
     * labels the fee wrongly.
     *
     * @return void
     */
    public function testSettlementCurrencyIsCarriedSeparately(): void
    {
        $settlement = new ProviderSettlement(
            providerPaymentReference: 'pi_1',
            providerSettlementId:     'txn_1',
            feeMinorUnits:            103,
            netMinorUnits:            2397,
            currency:                 'gbp',
        );

        $this->assertSame('gbp', $settlement->currency);
        $this->assertSame(103, $settlement->feeMinorUnits);
        $this->assertSame(2397, $settlement->netMinorUnits);
    }
}
