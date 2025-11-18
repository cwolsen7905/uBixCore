<?php

declare(strict_types=1);

namespace Ubix\Repository\AdminUser;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\AdminUserOptions;
use Ubix\Enum\AdminUser\AdminUserStatus;
use Ubix\Model\AdminUser;
use Ubix\Repository\AdminUser\AdminUserReaderInterface as AdminUserReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading admin user data
 *
 * @see \Ubix\Tests\Repository\AdminUser\AdminUserSqlRepositoryTest PHPUnit test case
 */
final class AdminUserSqlRepository implements AdminUserReader
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
        return $this->query(new AdminUserOptions(
            id:    $id,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByEmailAddress(string $emailAddress): array
    {
        return $this->query(new AdminUserOptions(
            emailAddress: $emailAddress,
            limit:        1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByNotificationType(string $notificationType): array
    {
        return $this->query(new AdminUserOptions(
            notificationType: $notificationType,
        ));
    }

    /**
     * Query the database and transform the result(s) into object(s)
     *
     * @param AdminUserOptions $options DTO of options to generate the query
     *
     * @return AdminUser[] Array of object(s)
     */
    private function query(AdminUserOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
                    au.id,
                    au.name,
                    au.username,
                    au.forum_username,
                    au.password,
                    au.enc_password,
                    au.salt,
                    au.2fa_secret,
                    au.email,
                    au.telephone,
                    au.mail_address,
                    au.twitter_handle,
                    au.date,
                    au.can_monitor,
                    au.vs_monitor,
                    au.monitor_username,
                    au.editable,
                    au.solo,
                    au.adult,
                    au.vs_acct_mgr,
                    au.psychic,
                    au.date_last_login,
                    au.last_opened,
                    au.last_clicked,
                    au.status,
                    au.cookie_policy_acceptance_datetime,
                    au.cookie_policy_acceptance_ip,
                    au.cookie_policy_acceptance_version
				FROM STUDIOS.Admin_Users au';

        if ($options->notificationType !== null) {
            $sql .= ' INNER JOIN VSCASH.Admin_Notification_Users nu ON nu.admin_id = au.id
                      INNER JOIN VSCASH.Admin_Notification_Types nt ON nt.id = nu.notification_type_id';
        }

        $sql .= ' WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND au.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->emailAddress !== null) {
            $sql .= ' AND LOWER(au.email) = :emailAddress';

            $parameters['emailAddress'] = strtolower($options->emailAddress);
        }

        if ($options->notificationType !== null) {
            if (is_string($options->notificationType)) {
                $sql .= ' AND nt.name = :notificationType';
            } else {
                $sql .= ' AND nt.id = :notificationType';
            }

            $parameters['notificationType'] = $options->notificationType;
        }

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: ?int,
         *     name: ?string,
         *     username: ?string,
         *     forum_username: ?string,
         *     password: ?string,
         *     enc_password: ?string,
         *     salt: ?string,
         *     '2fa_secret': ?string,
         *     email: ?string,
         *     telephone: ?string,
         *     mail_address: ?string,
         *     twitter_handle: ?string,
         *     date: ?string,
         *     can_monitor: ?int,
         *     vs_monitor: ?int,
         *     monitor_username: ?string,
         *     editable: ?int,
         *     solo: ?int,
         *     adult: ?string,
         *     vs_acct_mgr: ?string,
         *     psychic: ?string,
         *     date_last_login: ?string,
         *     last_opened: ?string,
         *     last_clicked: ?string,
         *     status: ?int,
         *     cookie_policy_acceptance_datetime: ?string,
         *     cookie_policy_acceptance_ip: ?string,
         *     cookie_policy_acceptance_version: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $objects[] = new AdminUser(
                id:                             $row['id'],
                name:                           $row['name'],
                username:                       $row['username'],
                forumUsername:                  $row['forum_username'],
                password:                       $row['password'],
                encPassword:                    $row['enc_password'],
                salt:                           $row['salt'],
                twofaSecret:                    $row['2fa_secret'],
                email:                          $row['email'],
                telephone:                      $row['telephone'],
                mailAddress:                    $row['mail_address'],
                twitterHandle:                  $row['twitter_handle'],
                date:                           $row['date'] !== null ? new DateTime($row['date']) : null,
                canMonitor:                     $row['can_monitor'],
                vsMonitor:                      $row['vs_monitor'],
                monitorUsername:                $row['monitor_username'],
                editable:                       $row['editable'],
                solo:                           $row['solo'],
                adult:                          $row['adult'],
                vsAccountManager:               $row['vs_acct_mgr'],
                psychic:                        $row['psychic'],
                dateLastLogin:                  $row['date_last_login'] !== null ? new DateTime($row['date_last_login']) : null,
                lastOpened:                     $row['last_opened'] !== null ? new DateTime($row['last_opened']) : null,
                lastClicked:                    $row['last_clicked'] !== null ? new DateTime($row['last_clicked']) : null,
                status:                         AdminUserStatus::tryFrom($row['status'] ?? ''),
                cookiePolicyAcceptanceDatetime: $row['cookie_policy_acceptance_datetime'] !== null ? new DateTime($row['cookie_policy_acceptance_datetime']) : null,
                cookiePolicyAcceptanceIp:       $row['cookie_policy_acceptance_ip'],
                cookiePolicyAcceptanceVersion:  $row['cookie_policy_acceptance_version'],
            );
        }

        return $objects;
    }
}
