<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUserAgeVerification;

use DateTime;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\PlatformUserAgeVerification;
use Ubix\Repository\PlatformUserAgeVerification\PlatformUserAgeVerificationWriterInterface as PlatformUserAgeVerificationWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing platform user age verification data
 *
 * @see \Ubix\Tests\Repository\PlatformUserAgeVerification\PlatformUserAgeVerificationSqlRepositoryTest PHPUnit test case
 */
final class PlatformUserAgeVerificationSqlRepository implements PlatformUserAgeVerificationWriter
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
     * @throws LogicException If an unimplemented feature is accessed
     */
    public function save(PlatformUserAgeVerification $platformUserAgeVerification): void
    {
        if ($platformUserAgeVerification->getId() !== null && $platformUserAgeVerification->getId() > 0) { // If there is a valid ID then update the database
            throw new LogicException('UPDATES FOR PLATFORM USER AGE VERIFICATIONS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        } else { // There is no valid ID so insert into the database
            //
            //  INSERT into the database
            //
            $sql = 'INSERT INTO ntl_db.Age_Verification SET
                        user_id            = :userId,
                        country            = :country,
                        state              = :state,
                        date_verified      = :dateVerified,
                        method             = :method,
                        avs_regulation     = :avsRegulation,
                        verification_token = :verificationToken';

            $this->sqlService->query($sql, [
                'avsRegulation'     => $platformUserAgeVerification->getAvsRegulation(),
                'country'           => $platformUserAgeVerification->getCountry(),
                'dateVerified'      => $platformUserAgeVerification->getDateVerified()?->format(DateTime::ISO8601_EXPANDED),
                'method'            => $platformUserAgeVerification->getMethod(),
                'state'             => $platformUserAgeVerification->getState(),
                'userId'            => $platformUserAgeVerification->getUserId(),
                'verificationToken' => $platformUserAgeVerification->getVerificationToken(),
            ]);

            //
            //  Update the object
            //
            $platformUserAgeVerification->setId((int)$this->sqlService->lastInsertId());
        }
    }
}
