<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\Performer;
use Ubix\Repository\Performer\PerformerReaderInterface as PerformerReader;

/**
 * Service to interact with slugs
 *
 * @see \Ubix\Tests\Service\SlugServiceTest PHPUnit test case
 */
final class SlugService
{
    /**
     * Constructor
     *
     * @param Logger          $logger          Logger
     * @param PerformerReader $performerReader Performer reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private PerformerReader $performerReader,
    ) {
    }

    /**
     * Get the object related to a slug
     *
     * @param string $slug The slug
     *
     * @throws Exception If a related object isn't found for the slug
     * @throws InvalidArgumentException If you pass an invalid slug
     *
     * @return Performer The object related to the slug
     */
    public function getObjectFromSlug(string $slug): Performer
    {
        //
        //  Validate parameters
        //
        if (trim($slug) === '') {
            throw new InvalidArgumentException('You must enter a slug.', ExceptionCode::MISSING_SLUG->value);
        }

        //
        //  Check if the slug is connected to a performer
        //
        $performers = $this->performerReader->getBySlug($slug);
        if (count($performers) > 0) {
            return $performers[0];
        }

        //
        //  Throw an exception if we couldn't find a match
        //
        throw new Exception('The URL slug was not found.', ExceptionCode::SLUG_NOT_FOUND->value);
    }
}
