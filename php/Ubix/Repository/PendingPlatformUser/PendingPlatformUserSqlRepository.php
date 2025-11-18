<?php

declare(strict_types=1);

namespace Ubix\Repository\PendingPlatformUser;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\PendingPlatformUserOptions;
use Ubix\Model\PendingPlatformUser;
use Ubix\Repository\PendingPlatformUser\PendingPlatformUserReaderInterface as PendingPlatformUserReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading pending platform user data
 *
 * @see \Ubix\Tests\Repository\PendingPlatformUser\PendingPlatformUserSqlRepositoryTest PHPUnit test case
 */
final class PendingPlatformUserSqlRepository implements PendingPlatformUserReader
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
    public function getByUsername(string $username): array
    {
        return $this->query(new PendingPlatformUserOptions(
            username: $username,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param PendingPlatformUserOptions $options DTO of options to generate the query
     *
     * @return PendingPlatformUser[] An array of pending platform user object(s)
     */
    private function query(PendingPlatformUserOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					opc.id,
					opc.username,
					opc.password,
					opc.salt,
					opc.auth_key,
					opc.email,
					opc.firstname,
					opc.lastname,
					opc.country,
					opc.state,
					opc.mp_code,
					opc.sitekey,
					opc.domain,
					opc.product_code,
					opc.service,
					opc.model_id,
					opc.ip_address,
					opc.source_site_id,
					opc.date_created,
					opc.reminder_sent,
					opc.update_cnt,
					opc.iovation_bb,
					opc.language
				FROM ntl_db.optiusers_pending_conf opc
				WHERE 1 = 1';

        if ($options->username !== null) {
            $sql .= ' AND opc.username = :username';

            $parameters['username'] = $options->username;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: ?int,
         *     username: ?string,
         *     password: ?string,
         *     salt: ?string,
         *     auth_key: ?string,
         *     email: ?string,
         *     firstname: ?string,
         *     lastname: ?string,
         *     country: ?string,
         *     state: ?string,
         *     mp_code: ?string,
         *     sitekey: ?string,
         *     domain: ?string,
         *     product_code: ?int,
         *     service: ?string,
         *     model_id: ?int,
         *     ip_address: ?string,
         *     source_site_id: ?int,
         *     date_created: ?string,
         *     reminder_sent: ?int,
         *     update_cnt: ?int,
         *     iovation_bb: ?string,
         *     language: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters, true) as $row) {
            $objects[] = new PendingPlatformUser(
                id:           $row['id'],
                username:     $row['username'],
                password:     $row['password'],
                salt:         $row['salt'],
                authKey:      $row['auth_key'],
                email:        $row['email'],
                firstname:    $row['firstname'],
                lastname:     $row['lastname'],
                country:      $row['country'],
                state:        $row['state'],
                mpCode:       $row['mp_code'],
                sitekey:      $row['sitekey'],
                domain:       $row['domain'],
                productCode:  $row['product_code'],
                service:      $row['service'],
                modelId:      $row['model_id'],
                ipAddress:    $row['ip_address'],
                sourceSiteId: $row['source_site_id'],
                dateCreated:  $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
                reminderSent: $row['reminder_sent'],
                updateCnt:    $row['update_cnt'],
                iovationBb:   $row['iovation_bb'],
                language:     $row['language'],
            );
        }

        return $objects;
    }
}
