<?php

declare(strict_types=1);

namespace Ubix\Repository\Broadcaster;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\BroadcasterOptions;
use Ubix\Enum\Broadcaster\BroadcasterStatus;
use Ubix\Model\Broadcaster;
use Ubix\Repository\Broadcaster\BroadcasterReaderInterface as BroadcasterReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading solo acquisition campaign data
 *
 * @see \Ubix\Tests\Repository\Broadcaster\BroadcasterSqlRepositoryTest PHPUnit test case
 */
final class BroadcasterSqlRepository implements BroadcasterReader
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
        return $this->query(new BroadcasterOptions(
            id:    $id,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getPartialMatches(string $name, string $studioName): array
    {
        return $this->query(new BroadcasterOptions(
            namePartial:       $name,
            studioNamePartial: $studioName,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getCountById(int $id): int
    {
        return $this->queryForCount(new BroadcasterOptions(
            id: $id,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param BroadcasterOptions $options DTO of options to generate the query
     *
     * @return Broadcaster[] An array of objects
     */
    private function query(BroadcasterOptions $options): array
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
         *     status: ?string,
         *     check_name: ?string,
         *     can_recruit: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters, true) as $row) {
            $objects[] = new Broadcaster(
                id:         $row['id'],
                name:       $row['name'],
                status:     BroadcasterStatus::tryFrom($row['status'] ?? ''),
                checkName:  $row['check_name'],
                canRecruit: $row['can_recruit'],
            );
        }

        return $objects;
    }

    /**
     * Query the database for a count
     *
     * @param BroadcasterOptions $options DTO of options to generate the query
     *
     * @return int The count
     */
    private function queryForCount(BroadcasterOptions $options): int
    {
        //
        //  Get the SQL query
        //
        $sqlQuery = $this->getSqlQuery($options, true);

        //
        //  Query the database and return the count
        //
        $count = $this->sqlService->getColumn($sqlQuery->sql, $sqlQuery->parameters, true);
        assert(is_int($count));
        return $count;
    }

    /**
     * Get a SQL query DTO ready to be executed
     *
     * @param BroadcasterOptions $options  DTO of options to generate the query
     * @param bool               $getCount Whether or not the query should return a row count (optional) (default: false)
     *
     * @return SqlQuery A SQL query DTO
     */
    private function getSqlQuery(BroadcasterOptions $options, bool $getCount = false): SqlQuery
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT ' . ($getCount ? 'COUNT(*)' : '
                    b.id,
                    b.name,
                    b.status,
                    bd.check_name,
                    b.can_recruit') . '
                FROM STUDIOS.Broadcasters b
                LEFT OUTER JOIN STUDIOS.Broadcasters_Details bd ON bd.broadcaster_id
				WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND b.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->namePartial !== null || $options->studioNamePartial !== null) {
            $sql .= ' (
                        SELECT COUNT(*)
                        FROM STUDIOS.Broadcasters_Details bd
                        WHERE bd.broadcaster_id = b.id AND (';
            if ($options->namePartial !== null) {
                $sql .= 'LOWER(check_name) LIKE :namePartial OR LOWER(mail_name) LIKE :namePartial';

                $parameters['namePartial'] = strtolower($options->namePartial);
            }
            if ($options->studioNamePartial !== null) {
                if ($options->namePartial !== null) {
                    $sql .= ' OR ';
                }
                $sql .= 'LOWER(bd.check_name) LIKE :studioNamePartial OR LOWER(mail_name) LIKE :studioNamePartial';

                $parameters['studioNamePartial'] = strtolower($options->studioNamePartial);
            }
            $sql .= ' GROUP BY bd.broadcaster_id)>0';
        }

        $sql .= ' ORDER BY bd.id DESC';
        $sql .= ' GROUP BY b.id';

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
