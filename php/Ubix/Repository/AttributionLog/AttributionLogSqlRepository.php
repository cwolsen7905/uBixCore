<?php

declare(strict_types=1);

namespace Ubix\Repository\AttributionLog;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\AttributionLogOptions;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Int\AttributionLogId;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Varchar;
use Ubix\Model\AttributionLog;
use Ubix\Repository\AttributionLog\AttributionLogReaderInterface as AttributionLogReader;
use Ubix\Repository\AttributionLog\AttributionLogWriterInterface as AttributionLogWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing attribution log data
 *
 * @see \Ubix\Tests\Repository\AttributionLog\AttributionLogSqlRepositoryTest PHPUnit test case
 */
final class AttributionLogSqlRepository implements AttributionLogReader, AttributionLogWriter
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
     */
    public function getLogByPlatformUserId(PlatformUserId $userId): array
    {
        return $this->query(new AttributionLogOptions(
            userId: $userId->value,
        ));
    }

     /**
      * {@inheritDoc}
      */
    public function save(AttributionLog $logData): void
    {
        $sql = 'INSERT INTO BILLING.Attribution_V2_Log 
                SET user_id = :userId,
                method = :method, 
                old_mp_code = :oldMpCode, 
                new_mp_code = :newMpCode, 
                reason = :reason,
                bounty_paid = :bountyPaid';

        assert($logData->getBountyPaid() !== null && $logData->getMethod() !== null && $logData->getNewMpCode() !== null && $logData->getOldMpCode() !== null && $logData->getReason() !== null && $logData->getUserId() !== null);
        $parameters = [
            'bountyPaid' => $logData->getBountyPaid()->value,
            'method'     => $logData->getMethod()->value,
            'newMpCode'  => $logData->getNewMpCode()->value,
            'oldMpCode'  => $logData->getOldMpCode()->value,
            'reason'     => $logData->getReason()->value,
            'userId'     => $logData->getUserId()->value,
        ];

        $this->sqlService->query($sql, $parameters);


        $logData->setId(new AttributionLogId(intval($this->sqlService->lastInsertId())));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param AttributionLogOptions $options DTO of options to generate the query
     *
     * @return AttributionLog[] An array of objects
     */
    private function query(AttributionLogOptions $options): array
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
         *   id: int,
         *   user_id: int,
         *   ref_id: string,
         *   method: string,
         *   old_mp_code: string,
         *   new_mp_code: string,
         *   reason: string,
         *   bounty_paid: string,
         *   event_date: string|null
         * } $row
         */
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters, true) as $row) {
            $attributionLog = new AttributionLog(
                id:            new AttributionLogId($row['id']),
                userId:        new PlatformUserId($row['user_id']),
                method:        new Varchar($row['method']),
                oldMpCode:     new MpCode($row['old_mp_code']),
                newMpCode:     new MpCode($row['new_mp_code']),
                reason:        new Varchar($row['reason']),
                bountyPaid:    new Varchar($row['bounty_paid']),
                dateTimeEvent: $row['event_date'] ? new UbixDateTime($row['event_date']) : null,
            );

            $attributionLog->clearChanges();

            $objects[] = $attributionLog;
        }

        return $objects;
    }

     /**
      * Get a SQL query DTO ready to be executed
      *
      * @param AttributionLogOptions $options DTO of options to generate the query
      *
      * @return SqlQuery A SQL query DTO
      */
    private function getSqlQuery(AttributionLogOptions $options): SqlQuery
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT * FROM BILLING.Attribution_V2_Log 
                        WHERE 1 = 1';

        if ($options->userId !== null) {
            $sql .= ' AND user_id = :userId';

            $parameters['userId'] = $options->userId;
        }

        $sql .= ' ORDER BY event_date DESC';

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
