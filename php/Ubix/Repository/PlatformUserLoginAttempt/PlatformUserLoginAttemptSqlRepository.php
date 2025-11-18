<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUserLoginAttempt;

use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\PlatformUserLoginAttempt;
use Ubix\Repository\PlatformUserLoginAttempt\PlatformUserLoginAttemptWriterInterface as PlatformUserLoginAttemptWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for writing platform user login attempt data
 *
 * @see \Ubix\Tests\Repository\PlatformUserLoginAttempt\PlatformUserLoginAttemptSqlRepositoryTest PHPUnit test case
 */
final class PlatformUserLoginAttemptSqlRepository implements PlatformUserLoginAttemptWriter
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
    public function save(PlatformUserLoginAttempt $platformUserLoginAttempt): void
    {
        if ($platformUserLoginAttempt->getId() !== null && $platformUserLoginAttempt->getId() > 0) { // If there is a valid ID then update the database
            throw new LogicException('UPDATES FOR PLATFORM USER LOGIN ATTEMPTS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        } else { // There is no valid ID so insert into the database
            $sql = 'INSERT INTO ntl_db.optiusers_site_logins SET
					username        = :username,
                    ip_address_long = :ipAddressLong,
                    password        = :password,
                    datetime        = :datetime,
                    fail_code       = :failCode,
                    domain          = :domain,
                    sitekey         = :sitekey,
                    login_type      = :loginType';

            $this->sqlService->query($sql, [
                'datetime'      => $platformUserLoginAttempt->getDatetime()?->format('Y-m-d'),
                'domain'        => $platformUserLoginAttempt->getDomain(),
                'failCode'      => $platformUserLoginAttempt->getFailCode(),
                'ipAddressLong' => $platformUserLoginAttempt->getIpAddress(),
                'loginType'     => $platformUserLoginAttempt->getLoginType(),
                'password'      => $platformUserLoginAttempt->getPassword(),
                'sitekey'       => $platformUserLoginAttempt->getSitekey(),
                'username'      => $platformUserLoginAttempt->getUsername(),
            ]);

            //
            //  Update the object
            //
            $platformUserLoginAttempt->setId((int)$this->sqlService->lastInsertId());
        }
    }
}
