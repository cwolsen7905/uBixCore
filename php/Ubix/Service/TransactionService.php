<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\FanClubTransaction;
use Ubix\Model\PlatformUser;
use Ubix\Repository\FanClubTransaction\FanClubTransactionWriterInterface as FanClubTransactionWriter;
use Ubix\Repository\PlatformUser\PlatformUserWriterInterface as PlatformUserWriter;

/**
 * Service to process all transactions
 *
 * @see \Ubix\Tests\Service\TransactionServiceTest PHPUnit test case
 */
final class TransactionService
{
    /**
     * Constructor
     *
     * @param Logger                   $logger                   Logger
     * @param FanClubTransactionWriter $fanClubTransactionWriter Fan club transaction writer
     * @param PlatformUserWriter       $platformUserWriter       Platform user writer
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private FanClubTransactionWriter $fanClubTransactionWriter,
        private PlatformUserWriter $platformUserWriter,
    ) {
    }

    /**
     * Process a new transaction
     *
     * @param PlatformUser       $payer       The platform user that will pay for the transaction
     * @param FanClubTransaction $transaction The unprocessed/unsaved transaction (NOTE: this method currently only supports fan club transactions but should be extended to allow other types too)
     *
     * @throws Exception If the transaction fails to process
     *
     * @return void
     */
    public function newTransaction(PlatformUser $payer, FanClubTransaction $transaction): void
    {
        //
        //  Ensure the transaction hasn't been processed yet (no auto increment ID value)
        //
        switch (get_class($transaction)) {
            case FanClubTransaction::class:
                if ($transaction->getId() === null || $transaction->getId() <= 0) {
                    throw new Exception('This transaction has already been processed.', ExceptionCode::TRANSACTION_ALREADY_PROCESSED->value);
                }
                break;
        }

        //
        //  Ensure the user has enough credits to fund the transaction
        //
        $transactionCost = null;
        switch (get_class($transaction)) {
            case FanClubTransaction::class:
                $transactionCost = $transaction->getAmountCredits();
                break;
        }

        if ($transactionCost === null) {
            throw new Exception('There was an error calculating the transaction code.', ExceptionCode::TRANSACTION_COST_IS_NULL->value);
        } elseif ($payer->getTimeleft() < $transactionCost) {
            throw new Exception('The user does not have enough credits.', ExceptionCode::NOT_ENOUGH_CREDITS->value);
        }

        //
        //  Save the transaction object
        //
        switch (get_class($transaction)) {
            case FanClubTransaction::class:
                $this->fanClubTransactionWriter->save($transaction);
                break;
        }

        //
        //  Deduct the credits from the user
        //
        $payer->setTimeleft($payer->getTimeleft() - $transactionCost);

        $this->platformUserWriter->save($payer);
    }
}
