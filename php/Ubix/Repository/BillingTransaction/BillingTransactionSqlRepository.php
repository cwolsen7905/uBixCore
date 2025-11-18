<?php

declare(strict_types=1);

namespace Ubix\Repository\BillingTransaction;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\BillingTransactionOptions;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Float\UsdCurrency;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\Int\TransactionId;
use Ubix\DataType\String\MpCode;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\Transaction;
use Ubix\Repository\BillingTransaction\BillingTransactionReaderInterface as BillingTransactionReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading transaction data
 *
 * @see \Ubix\Tests\Repository\Billing\TransactionSqlRepositoryTest PHPUnit test case
 * @see \Ubix\Tests\Repository\Billing\BillingTransactionSqlRepositoryTest PHPUnit test case
 * @see \Ubix\Tests\Repository\BillingTransaction\BillingTransactionSqlRepositoryTest PHPUnit test case
 */
final class BillingTransactionSqlRepository implements BillingTransactionReader
{
    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param SqlService $sqlService SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If no transaction found for the user ID
     */
    public function getMostRecentByUserId(PlatformUserId $userId): Transaction
    {
        $transaction = $this->query(new BillingTransactionOptions(
            userId: $userId,
            limit:  1,
        ));
        if (count($transaction) === 0) {
            throw new Exception('No transaction found for user ID ' . $userId->value, ExceptionCode::TRANSACTION_NOT_FOUND->value);
        }

        return $transaction[0];
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If no transaction found for the user ID
     */
    public function getMostRecentByUserIdBeforeDate(PlatformUserId $userId, UbixDateTime $dateTime): Transaction
    {
        $transaction = $this->query(new BillingTransactionOptions(
            userId:   $userId,
            dateTime: $dateTime->value->format('Y-m-d H:i:s'),
            limit:    1,
        ));
        if (count($transaction) === 0) {
            throw new Exception('No transaction found for user ID ' . $userId->value, ExceptionCode::TRANSACTION_NOT_FOUND->value);
        }

        return $transaction[0];
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param BillingTransactionOptions $options DTO of options to generate the query
     *
     * @return Transaction[] An array of objects
     */
    private function query(BillingTransactionOptions $options): array
    {
        //
        //  Get the SQL query
        //
        $sqlQuery = $this->getSqlQuery($options);

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: int,
         *     user_id: int,
         *     mp_code: string,
         *     amount: float,
         *     datetime: string,
         *     user_mp_code: string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters, true) as $row) {
            $objects[] = new Transaction(
                id:         new TransactionId($row['id']),
                userId:     new PlatformUserId($row['user_id']),
                mpCode:     new MpCode($row['mp_code']),
                amount:     new UsdCurrency($row['amount']),
                datetime:   new UbixDateTime($row['datetime']),
                userMpCode: new MpCode($row['user_mp_code']),
            );
        }

        return $objects;
    }

    /**
     * Get a SQL query DTO ready to be executed
     *
     * @param BillingTransactionOptions $options DTO of options to generate the query
     *
     * @return SqlQuery A SQL query DTO
     */
    private function getSqlQuery(BillingTransactionOptions $options): SqlQuery
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT id,user_id,mp_code,amount,datetime,user_mp_code FROM ntl_db.transact t JOIN ntl_db.transact_ex tex ON t.id = tex.transact_id 
				WHERE 1 = 1';

        if ($options->userId !== null) {
            $sql .= ' AND t.user_id = :userId';

            $parameters['userId'] = $options->userId->value;
        }

        if ($options->dateTime !== null) {
            $sql .= ' AND t.datetime < :dateTime';

            $parameters['dateTime'] = $options->dateTime;
        }

        $sql .= ' ORDER BY t.datetime DESC';

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        //
        //  Return a DTO
        //
        return new SqlQuery(
            sql:        $sql,
            parameters: $parameters,
        );
    }
}
