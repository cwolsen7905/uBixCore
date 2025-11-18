<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\PagedObjects;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Performer\PerformerNameSuggestionSex;
use Ubix\Model\Performer;
use Ubix\Model\PerformerNameSuggestion;
use Ubix\Model\PerformerStatistics;
use Ubix\Repository\Performer\PerformerReaderInterface as PerformerReader;
use Ubix\Repository\PerformerNameSuggestion\PerformerNameSuggestionReaderInterface as PerformerNameSuggestionReader;
use Ubix\Repository\PerformerStatistics\PerformerStatisticsReaderInterface as PerformerStatisticsReader;

/**
 * Service to access performers
 *
 * @see \Ubix\Tests\Service\PerformerServiceTest PHPUnit test case
 */
final class PerformerService
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * Constructor
     *
     * @param Logger                        $logger                        Logger
     * @param PerformerNameSuggestionReader $performerNameSuggestionReader Performer name suggestion reader
     * @param PerformerReader               $performerReader               Performer reader
     * @param PerformerStatisticsReader     $performerStatisticsReader     Performer statistics reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private PerformerNameSuggestionReader $performerNameSuggestionReader,
        private PerformerReader $performerReader,
        private PerformerStatisticsReader $performerStatisticsReader,
    ) {
    }

    /**
     * Get a performer by ID
     *
     * @param int $id The performer ID
     *
     * @throws Exception If a performer is not found with a matching ID
     *
     * @return Performer The performer with matching ID
     */
    public function getById(int $id): Performer
    {
        $performers = $this->performerReader->getById($id);
        if (count($performers) > 0) {
            return $performers[0];
        } else {
            throw new Exception('No model found with the ID `' . $id . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_PERFORMER_ID->value);
        }
    }

    /**
     * Get performers by email address
     *
     * @param string $emailAddress The performer's email address
     *
     * @return Performer[] An array of performers with a matching email address
     */
    public function getByEmailAddress(string $emailAddress): array
    {
        return $this->performerReader->getByEmailAddress($emailAddress);
    }

    /**
     * Get performers by name
     *
     * @param string $name The performer's name
     *
     * @return Performer[] An array of performers with a matching name
     */
    public function getByName(string $name): array
    {
        return $this->performerReader->getByName($name);
    }

    /**
     * Get performers by partial match
     *
     * @param string $name The performer's name
     *
     * @return Performer[] An array of partially matching performers
     */
    public function getPartialMatches(string $name): array
    {
        return $this->performerReader->getPartialMatches($name);
    }

    /**
     * Get statistics for a performer by performer ID
     *
     * @param int $performerId The performer's ID
     *
     * @throws Exception If no statistics are found
     *
     * @return PerformerStatistics Performer statistics object
     */
    public function getStatisticsByPerformerId(int $performerId): PerformerStatistics
    {
        $performerStatistics = $this->performerStatisticsReader->getByPerformerId($performerId);
        if (count($performerStatistics) > 0) {
            return $performerStatistics[0];
        } else {
            throw new Exception('No performer statistics found for performer ID `' . $performerId . '`', ExceptionCode::NO_STATISTICS_MATCHES_FOUND_FOR_PERFORMER_ID->value);
        }
    }

    /**
     * Get performer name suggestions including metadata
     *
     * @param string $name The name
     *
     * @throws Exception If no name suggestions are found
     *
     * @return PerformerNameSuggestion A performer name suggestion
     */
    public function getNameSuggestion(string $name): PerformerNameSuggestion
    {
        $performerNameSuggestions = $this->performerNameSuggestionReader->getByName($name);
        if (count($performerNameSuggestions) > 0) {
            return $performerNameSuggestions[0];
        } else {
            throw new Exception('No name suggestion found with the name `' . $name . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_PERFORMER_NAME_SUGGESTION->value);
        }
    }

    /**
     * Get performer name suggestions including metadata
     *
     * @param int                        $page        The page of the results (optional) (default: 1)
     * @param int                        $pageSize    The number of results (optional) (default: self::DEFAULT_PAGE_SIZE)
     * @param PerformerNameSuggestionSex $sex         The sex (optional) (default: PerformerNameSuggestionSex::EITHER)
     * @param ?string                    $searchQuery The search query (optional) (default: null)
     *
     * @throws InvalidArgumentException If a parameter value is invalid
     *
     * @return PagedObjects A paged result of performer name suggestions
     */
    public function getNameSuggestions(
        int $page = 1,
        int $pageSize = self::DEFAULT_PAGE_SIZE,
        PerformerNameSuggestionSex $sex = PerformerNameSuggestionSex::EITHER,
        ?string $searchQuery = null,
    ): PagedObjects {
        //
        //  Validate parameters
        //
        if ($page <= 0) {
            throw new InvalidArgumentException(
                'The page must be a non-negative integer',
                ExceptionCode::INVALID_PAGE_FOR_PERFORMER_NAME_SUGGESTIONS->value,
            );
        }

        if ($pageSize <= 0) {
            throw new InvalidArgumentException(
                'The page size must be a non-negative integer',
                ExceptionCode::INVALID_PAGE_SIZE_FOR_PERFORMER_NAME_SUGGESTIONS->value,
            );
        }

        //
        //  Calculate the values for the PagedObjects DTO
        //
        $total = $this->performerNameSuggestionReader->getCount($sex, $searchQuery);

        $offset = ($page - 1) * $pageSize;
        if ($offset >= $total) {
            throw new InvalidArgumentException(
                'The requested page is beyond the range of the collection',
                ExceptionCode::OUT_OF_RANGE_PAGE_FOR_PERFORMER_NAME_SUGGESTIONS->value,
            );
        }
        $objects = $this->performerNameSuggestionReader->get($sex, $searchQuery, $pageSize, $offset);

        //
        //  Return the results as a PagedObjects DTO
        //
        return new PagedObjects(
            objects: $objects,
            offset:  $offset,
            total:   $total,
        );
    }
}
