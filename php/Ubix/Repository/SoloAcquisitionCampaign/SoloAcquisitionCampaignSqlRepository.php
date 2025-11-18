<?php

declare(strict_types=1);

namespace Ubix\Repository\SoloAcquisitionCampaign;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\SoloAcquisitionCampaignOptions;
use Ubix\Enum\SoloAcquisitionCampaign\SoloAcquisitionCampaignStatus;
use Ubix\Model\SoloAcquisitionCampaign;
use Ubix\Repository\SoloAcquisitionCampaign\SoloAcquisitionCampaignReaderInterface as SoloAcquisitionCampaignReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading solo acquisition campaign data
 *
 * @see \Ubix\Tests\Repository\SoloAcquisitionCampaign\SoloAcquisitionCampaignSqlRepositoryTest PHPUnit test case
 */
final class SoloAcquisitionCampaignSqlRepository implements SoloAcquisitionCampaignReader
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
    public function getById(int $id): array
    {
        return $this->query(new SoloAcquisitionCampaignOptions(
            id:    $id,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getCountById(int $id): int
    {
        return $this->queryForCount(new SoloAcquisitionCampaignOptions(
            id: $id,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param SoloAcquisitionCampaignOptions $options DTO of options to generate the query
     *
     * @return SoloAcquisitionCampaign[] An array of application objects
     */
    private function query(SoloAcquisitionCampaignOptions $options): array
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
         *     id: ?int,
         *     name: ?string,
         *     description: ?string,
         *     contact_info: ?string,
         *     status: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters) as $row) {
            $objects[] = new SoloAcquisitionCampaign(
                id:          $row['id'],
                name:        $row['name'],
                description: $row['description'],
                contactInfo: $row['contact_info'],
                status:      SoloAcquisitionCampaignStatus::tryFrom($row['status'] ?? ''),
            );
        }

        return $objects;
    }

    /**
     * Query the database for a count
     *
     * @param SoloAcquisitionCampaignOptions $options DTO of options to generate the query
     *
     * @return int The count
     */
    private function queryForCount(SoloAcquisitionCampaignOptions $options): int
    {
        //
        //  Get the SQL query
        //
        $sqlQuery = $this->getSqlQuery($options, true);

        //
        //  Query the database and return the count
        //
        $count = $this->sqlService->getColumn($sqlQuery->sql, $sqlQuery->parameters);
        assert(is_int($count));
        return $count;
    }

    /**
     * Get a SQL query DTO ready to be executed
     *
     * @param SoloAcquisitionCampaignOptions $options  DTO of options to generate the query
     * @param bool                           $getCount Whether or not the query should return a row count (optional) (default: false)
     *
     * @return SqlQuery A SQL query DTO
     */
    private function getSqlQuery(SoloAcquisitionCampaignOptions $options, bool $getCount = false): SqlQuery
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT ' . ($getCount ? 'COUNT(*)' : '
                    sac.id,
                    sac.name,
                    sac.description,
                    sac.contact_info,
                    sac.status') . '
                FROM STUDIOS.Solo_Acquisition_Campaigns sac
				WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND sac.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->status !== null) {
            $sql .= ' AND sac.status = :status';

            $parameters['status'] = $options->status->value;
        }

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
