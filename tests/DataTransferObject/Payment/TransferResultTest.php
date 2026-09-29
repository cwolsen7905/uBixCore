<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Payment;

use DateTimeImmutable;
use Ubix\DataTransferObject\Payment\TransferResult;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Payment\TransferResult
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Payment\TransferResult
 */
final class TransferResultTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TransferResult::class);
    }

    /**
     * Creating a transfer yields no metadata and no creation time
     *
     * The two fields exist for the read side, and their defaults say so: a caller of
     * `createTransfer()` already knows both, so empty here means "not read back",
     * never "the provider had none".
     *
     * @return void
     */
    public function testTheReadOnlyFieldsAreAbsentOnACreatedTransfer(): void
    {
        $created = new TransferResult(
            providerTransferId:          'tr_1',
            destinationAccountReference: 'acct_1',
            amountMinorUnits:            2500,
            currency:                    'usd',
        );

        $this->assertSame([], $created->metadata);
        $this->assertNull($created->createdAt);
    }

    /**
     * A transfer read back carries the metadata the host set, which is its only link home
     *
     * @return void
     */
    public function testAReadTransferCarriesItsMetadata(): void
    {
        $read = new TransferResult(
            providerTransferId:          'tr_2',
            destinationAccountReference: 'acct_2',
            amountMinorUnits:            2500,
            currency:                    'usd',
            metadata:                    ['payoutRunId' => '7'],
            createdAt:                   new DateTimeImmutable('2026-09-29 12:00:00'),
        );

        $this->assertSame('7', $read->metadata['payoutRunId']);
        $this->assertSame('2026-09-29', $read->createdAt?->format('Y-m-d'));
    }
}
