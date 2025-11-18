<?php

declare(strict_types=1);

namespace Ubix\Repository\DuplicateProspect;

use DateTime;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\DuplicateProspect;
use Ubix\Repository\DuplicateProspect\DuplicateProspectWriterInterface as DuplicateProspectWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for writing duplicate prospect data
 *
 * @see \Ubix\Tests\Repository\DuplicateProspect\DuplicateProspectSqlRepositoryTest PHPUnit test case
 */
final class DuplicateProspectSqlRepository implements DuplicateProspectWriter
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
     * @throws LogicException If the unimplemented update functionality is invoked
     */
    public function save(DuplicateProspect $duplicateProspect): void
    {
        if ($duplicateProspect->getId() !== null && $duplicateProspect->getId() > 0) { // If there is a valid ID then update the database
            throw new LogicException('UPDATES FOR DUPLICATE PROSPECTS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        } else { // There is no valid ID so insert into the database
            $sql = 'INSERT INTO STUDIOS.Prospects_Duplicates SET
                    review_status   = :reviewStatus,
                    review_admin_id = :reviewAdminId,
                    review_comments = :reviewComments,
                    review_date     = :reviewDate,
                    date_created    = :dateCreated,
                    submission_json = :submissionJson';

            $this->sqlService->query($sql, [
                'dateCreated'    => $duplicateProspect->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                'reviewAdminId'  => $duplicateProspect->getReviewAdminId(),
                'reviewComments' => $duplicateProspect->getReviewComments(),
                'reviewDate'     => $duplicateProspect->getReviewDate()?->format(DateTime::ISO8601_EXPANDED),
                'reviewStatus'   => $duplicateProspect->getReviewStatus()?->value,
                'submissionJson' => $duplicateProspect->getSubmissionJson(),
            ]);

            //
            //  Update the object
            //
            $duplicateProspect->setId((int)$this->sqlService->lastInsertId());
        }
    }
}
