<?php

declare(strict_types=1);

namespace Ubix\Repository\Studio;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\StudioOptions;
use Ubix\Enum\Studio\StudioStatus;
use Ubix\Model\Studio;
use Ubix\Repository\Studio\StudioReaderInterface as StudioReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading solo acquisition campaign data
 *
 * @see \Ubix\Tests\Repository\Studio\StudioSqlRepositoryTest PHPUnit test case
 */
final class StudioSqlRepository implements StudioReader
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
    public function getByAdminUserId(int $adminUserId): array
    {
        return $this->query(new StudioOptions(
            adminUserId: $adminUserId,
            limit:       1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getCountByAdminUserId(int $adminUserId): int
    {
        return $this->queryForCount(new StudioOptions(
            adminUserId: $adminUserId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param StudioOptions $options DTO of options to generate the query
     *
     * @return Studio[] An array of objects
     */
    private function query(StudioOptions $options): array
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
         *     studio: ?string,
         *     name: ?string,
         *     status: ?string,
         *     contact_details: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters) as $row) {
            $objects[] = new Studio(
                code:           $row['studio'],
                name:           $row['name'],
                status:         StudioStatus::tryFrom($row['status'] ?? ''),
                contactDetails: $row['contact_details'],
            );
        }

        return $objects;
    }

    /**
     * Query the database for a count
     *
     * @param StudioOptions $options DTO of options to generate the query
     *
     * @return int The count
     */
    private function queryForCount(StudioOptions $options): int
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
     * @param StudioOptions $options  DTO of options to generate the query
     * @param bool          $getCount Whether or not the query should return a row count (optional) (default: false)
     *
     * @return SqlQuery A SQL query DTO
     */
    private function getSqlQuery(StudioOptions $options, bool $getCount = false): SqlQuery
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT ' . ($getCount ? 'COUNT(*)' : '
                    s.studio,
                    s.name,
                    s.status,
                    s.contact_details') . '
                FROM ntl_db.studios s';

        if ($options->adminUserId !== null) {
            $sql .= ' INNER JOIN STUDIOS.Studios_Users su ON su.studio = s.studio';
        }

        $sql .= ' WHERE 1 = 1';

        if ($options->adminUserId !== null) {
            $sql .= ' AND su.admin_id = :adminUserId';

            $parameters['adminUserId'] = $options->adminUserId;
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
