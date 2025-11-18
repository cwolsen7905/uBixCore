<?php

declare(strict_types=1);

namespace Ubix\Repository\PerformerNameSuggestion;

use DateTime;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\PerformerNameSuggestionOptions;
use Ubix\Enum\Performer\PerformerNameSuggestionSex;
use Ubix\Model\PerformerNameSuggestion;
use Ubix\Repository\PerformerNameSuggestion\PerformerNameSuggestionReaderInterface as PerformerNameSuggestionReader;
use Ubix\Repository\PerformerNameSuggestion\PerformerNameSuggestionWriterInterface as PerformerNameSuggestionWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\Service\TransliterationService;

/**
 * Repository for reading and writing performer name suggestion data
 *
 * @see \Ubix\Tests\Repository\PerformerNameSuggestion\PerformerNameSuggestionSqlRepositoryTest PHPUnit test case
 */
final class PerformerNameSuggestionSqlRepository implements PerformerNameSuggestionReader, PerformerNameSuggestionWriter
{
    /**
     * Constructor
     *
     * @param Logger                 $logger                 Logger
     * @param SqlService             $sqlService             SQL service
     * @param TransliterationService $transliterationService Transliterate service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
        private TransliterationService $transliterationService,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If the unimplemented update functionality is invoked
     */
    public function save(PerformerNameSuggestion $performerNameSuggestion): void
    {
        if ($performerNameSuggestion->getId() !== null && $performerNameSuggestion->getId() > 0) { // If there is a valid ID then update the database
            $sql = 'UPDATE STUDIOS.Suggested_Names SET
					name     = :name,
					sex      = :sex,
					origin   = :origin,
					meaning  = :meaning,
					admin_id = :adminId,
					date_in  = :dateIn,
					status   = :status
                    WHERE id = :id';

            $this->sqlService->query($sql, [
                'adminId' => $performerNameSuggestion->getAdminId(),
                'dateIn'  => $performerNameSuggestion->getDateIn()?->format('Y-m-d'),
                'id'      => $performerNameSuggestion->getId(),
                'meaning' => $performerNameSuggestion->getMeaning(),
                'name'    => $performerNameSuggestion->getName(),
                'origin'  => $performerNameSuggestion->getOrigin(),
                'sex'     => $performerNameSuggestion->getSex()?->value,
                'status'  => $performerNameSuggestion->getStatus(),
            ]);
        } else { // There is no valid ID so insert into the database
            throw new LogicException('INSERTS FOR PERFORMER NAME SUGGESTIONS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(
        PerformerNameSuggestionSex $sex,
        ?string $searchQuery = null,
        int $maximumResults = PHP_INT_MAX,
        int $resultsOffset = 0,
    ): array {
        return $this->query(new PerformerNameSuggestionOptions(
            limit:       $maximumResults,
            limitOffset: $resultsOffset,
            orderBy:     'id DESC',
            sex:         $sex,
            searchQuery: $searchQuery,
            status:      PerformerNameSuggestion::STATUS_ACTIVE,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByName(string $name): array
    {
        return $this->query(new PerformerNameSuggestionOptions(
            limit:  1,
            name:   $name,
            status: PerformerNameSuggestion::STATUS_ACTIVE,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getCount(
        PerformerNameSuggestionSex $sex,
        ?string $searchQuery = null,
    ): int {
        return $this->queryForCount(new PerformerNameSuggestionOptions(
            sex:         $sex,
            searchQuery: $searchQuery,
            status:      PerformerNameSuggestion::STATUS_ACTIVE,
        ));
    }

    /**
     * Query the database and transform the result(s) into object(s)
     *
     * @param PerformerNameSuggestionOptions $options DTO of options to generate the query
     *
     * @return PerformerNameSuggestion[] Array of performer name suggestions object(s)
     */
    private function query(PerformerNameSuggestionOptions $options): array
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
         *     sex: ?string,
         *     origin: ?string,
         *     meaning: ?string,
         *     admin_id: ?int,
         *     date_in: ?string,
         *     status: ?int,
         * } $row
         */
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters) as $row) {
            $objects[] = new PerformerNameSuggestion(
                id:      $row['id'],
                name:    is_string($row['name']) ? $this->transliterationService->transliterate($row['name']) : null,
                sex:     PerformerNameSuggestionSex::tryFrom($row['sex'] ?? ''),
                origin:  $row['origin'],
                meaning: $row['meaning'],
                adminId: $row['admin_id'],
                dateIn:  $row['date_in'] !== null ? new DateTime($row['date_in']) : null,
                status:  $row['status'],
            );
        }

        return $objects;
    }

    /**
     * Query the database for a count
     *
     * @param PerformerNameSuggestionOptions $options DTO of options to generate the query
     *
     * @return int The count
     */
    private function queryForCount(PerformerNameSuggestionOptions $options): int
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
     * @param PerformerNameSuggestionOptions $options  DTO of options to generate the query
     * @param bool                           $getCount Whether or not the query should return a row count (optional) (default: false)
     *
     * @return SqlQuery A SQL query DTO
     */
    private function getSqlQuery(PerformerNameSuggestionOptions $options, bool $getCount = false): SqlQuery
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT ' . ($getCount ? 'COUNT(*)' : '
                    sn.id,
                    sn.name,
                    sn.sex,
                    sn.origin,
                    sn.meaning,
                    sn.admin_id,
                    sn.date_in,
                    sn.status') . '
                FROM STUDIOS.Suggested_Names sn
				WHERE 1 = 1';

        if ($options->sex !== null && $options->sex !== PerformerNameSuggestionSex::EITHER) {
            $sql .= ' AND sn.sex IN (:sex, "E")';

            $parameters['sex'] = $options->sex->value;
        }

        if ($options->name !== null) {
            $sql .= ' AND sn.name = :name';

            $parameters['name'] = $options->name;
        }

        if ($options->searchQuery !== null) {
            $sql .= ' AND sn.name LIKE :searchQuery';

            $parameters['searchQuery'] = '%' . $options->searchQuery . '%';
        }

        if ($options->status !== null) {
            $sql .= ' AND sn.status = :status';

            $parameters['status'] = $options->status;
        }

        if ($options->orderBy !== null) {
            $sql .= ' ORDER BY ' . $options->orderBy;
        }

        if ($options->limit !== null || $options->limitOffset !== null) {
            $sql .= ' LIMIT ' . ($options->limitOffset ?? 0) . ', ' . ($options->limit ?? PHP_INT_MAX);
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
