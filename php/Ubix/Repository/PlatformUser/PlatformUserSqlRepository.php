<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUser;

use DateTime;
use Exception;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\PlatformUserOptions;
use Ubix\Model\PlatformUser;
use Ubix\Model\PlatformUserTwoFactorAuthentication;
use Ubix\Repository\PlatformUser\PlatformUserReaderInterface as PlatformUserReader;
use Ubix\Repository\PlatformUser\PlatformUserWriterInterface as PlatformUserWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing platform user data
 *
 * @see \Ubix\Tests\Repository\PlatformUser\PlatformUserSqlRepositoryTest PHPUnit test case
 */
final class PlatformUserSqlRepository implements PlatformUserReader, PlatformUserWriter
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
     * @throws LogicException If not yet implemented code is accessed
     */
    public function save(PlatformUser $platformUser): void
    {
        // NOT_IMPLEMENTED: Add more columns to the queries, we only needed to update a small amount at the time so that were the only ones coded
        //
        //  Save data to the ntl_db.optiusers table
        //
        if ($platformUser->getUserId() !== null && $platformUser->getUserId() > 0) { // If there is a valid ID then update the database
            // NOT_IMPLEMENTED: We have commented out the timeleft column updates until that feature rolls out we don't want any unexpected changes to that column (when this changes transactions will need to be factored into database calls to ensure integrity - likely that means separating timeleft changes into a separate query that is wrapped in a $this->sqlService->inTransaction() check)
            $sql = 'UPDATE ntl_db.optiusers SET
                    /*timeleft = :timeleft*/
                    date_last_login = :dateLastLogin
                    WHERE user_id = :userId';

            // phpcs:disable Squiz.PHP.CommentedOutCode.Found -- We have commented out the timeleft column updates until that feature rolls out we don't want any unexpected changes to that column (when this changes transactions will need to be factored into database calls to ensure integrity - likely that means separating timeleft changes into a separate query that is wrapped in a $this->sqlService->inTransaction() check)

            $this->sqlService->query($sql, [
                // 'timeleft'        => $platformUser->getTimeleft(),
                'dateLastLogin' => $platformUser->getDateLastLogin()?->format(DateTime::ISO8601_EXPANDED),
                'userId'        => $platformUser->getUserId(),
            ]);

            // phpcs:enable Squiz.PHP.CommentedOutCode.Found
        } else { // There is no valid ID so insert into the database
            throw new LogicException('INSERTS FOR PLATFORM USERS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        }

        //
        //  Save data to the ntl_db.optiusers table
        //
        $sql = 'SELECT COUNT(*)
                FROM ntl_db.optiusers_ex
                WHERE user_id = :userId
                LIMIT 1';

        $optiusersExRowExists = $this->sqlService->getColumn($sql, [
            'userId' => $platformUser->getUserId(),
        ]) > 0;

        if ($optiusersExRowExists) { // If there is an existing row then update the database
            $sql = 'UPDATE ntl_db.optiusers_ex SET
                    age_verified = :ageVerified
                    WHERE user_id = :userId';

            $this->sqlService->query($sql, [
                'ageVerified' => $platformUser->getAgeVerified(),
                'userId'      => $platformUser->getUserId(),
            ]);
        } else { // There is no existing row so insert into the database
            $sql = 'INSERT INTO ntl_db.optiusers_ex SET
                    age_verified = :ageVerified,
                    user_id      = :userId';

            $this->sqlService->query($sql, [
                'ageVerified' => $platformUser->getAgeVerified(),
                'userId'      => $platformUser->getUserId(),
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getByUsername(string $username, ?string $sitekey = null): array
    {
        return $this->query(new PlatformUserOptions(
            sitekey:  $sitekey,
            username: $username,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByScreenName(string $screenName, ?string $sitekey = null): array
    {
        return $this->query(new PlatformUserOptions(
            screenName: $screenName,
            sitekey:    $sitekey,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getForAutoLogin(string $username, string $passwordMd5, ?string $sitekey = null): array
    {
        return $this->query(new PlatformUserOptions(
            passwordMd5: $passwordMd5,
            sitekey:     $sitekey,
            username:    $username,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $userId, ?string $sitekey = null): array
    {
        return $this->query(new PlatformUserOptions(
            userId:  $userId,
            sitekey: $sitekey,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByOauth(string $oauthProvider, string $oauthGuid): array
    {
        return $this->query(new PlatformUserOptions(
            oauthProvider: $oauthProvider,
            oauthGuid:     $oauthGuid,
        ));
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If no account is found
     */
    public function getEarliestAccountByEmail(PlatformUser $user): PlatformUser
    {
        //
        //  Limit based on sitekey, if = club - only check club sitekey, if != club - exclude club sitekey
        //
        $sql = 'SELECT t.user_id, t.mp_code as transact_mp_code, t.date_out, o.username, o.password, o.bounty_paid, o.salt, o.firstname, o.lastname, 
                       o.zip, o.email, o.mp_code, o.domain, o.sitekey, o.timeleft, o.timeleft_banked, o.total_spent, o.bill_next, o.vs_vip, o.status, 
                       o.date_last_login, o.date_created, o.spending_group
                FROM ntl_db.transact t
                LEFT JOIN ntl_db.transact_ex tx ON tx.transact_id = t.id
                LEFT JOIN ntl_db.optiusers o ON o.user_id = t.user_id
                WHERE o.email = :email
                AND t.date_out >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 6 MONTH)) 
                AND o.mp_code NOT IN ( "xxxx", "yyyy", "zzzz")
                AND o.user_id != :userId
                ' . ($user->getSitekey() === 'flirt4free' ? ' AND o.sitekey = "flirt4free" ' : ' AND o.domain = :domain ') . '   ORDER BY t.id ASC
                    LIMIT 1';

        $parameters = [
            'email'  => $user->getEmail(),
            'userId' => $user->getUserId(),
        ];


        if ($user->getSitekey() !== 'flirt4free') {
            $parameters['domain'] = $user->getDomain();
        }

        /**
         * @var array{
         * user_id: int,
         * username: string,
         * password: string,
         * bounty_paid: string,
         * salt: string,
         * firstname: string,
         * lastname: string,
         * zip: string,
         * email: string,
         * mp_code: string,
         * domain: string,
         * sitekey: string,
         * timeleft: int,
         * timeleft_banked: int,
         * total_spent: float,
         * bill_next: int,
         * vs_vip: string,
         * status: string,
         * date_last_login: ?string,
         * date_created: ?string,
         * spending_group: string} $row
         */
        $row = $this->sqlService->getRow($sql, $parameters);

        if (empty($row)) {
            throw new Exception('No account found');
        }

        return new PlatformUser(
            userId:         $row['user_id'],
            username:       $row['username'],
            password:       $row['password'],
            bountyPaid:     $row['bounty_paid'],
            salt:           $row['salt'],
            firstname:      $row['firstname'],
            lastname:       $row['lastname'],
            zip:            $row['zip'],
            email:          $row['email'],
            mpCode:         $row['mp_code'],
            domain:         $row['domain'],
            sitekey:        $row['sitekey'],
            timeleft:       $row['timeleft'],
            timeleftBanked: $row['timeleft_banked'],
            totalSpent:     $row['total_spent'],
            billNext:       $row['bill_next'],
            vsVip:          $row['vs_vip'],
            status:         $row['status'],
            dateLastLogin:  $row['date_last_login'] !== null ? new DateTime($row['date_last_login']) : null,
            dateCreated:    $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
            spendingGroup:  $row['spending_group'],
            // NOT_IMPLEMENTED: other fields can be added here if needed
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If no account is found
     */
    public function getEarliestAccountByFingerprint(PlatformUser $user): PlatformUser
    {
        $sql = "SELECT DISTINCT(ftu.user_id),o.* FROM BILLING.Fingerprint_to_User ftu 
                INNER JOIN ntl_db.optiusers o ON ftu.user_id = o.user_id 
                INNER JOIN ntl_db.transact t ON t.user_id = o.user_id
                WHERE ftu.visitor_id IN(SELECT DISTINCT(visitor_id) FROM BILLING.Fingerprint_to_User WHERE user_id = :userId) 
                AND t.date_out >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 6 MONTH)) 
                AND o.mp_code NOT IN ('xxxx', 'yyyy', 'zzzz') 
                AND ftu.confidence_score >= 0.8
                AND ftu.user_id != :userId2 
                " . ($user->getSitekey() === 'flirt4free' ? "AND o.sitekey = 'flirt4free' " : 'AND o.domain = :domain ') . '
                ORDER BY t.id ASC
                LIMIT 1';

        $parameters = [
            'userId'  => $user->getUserId(),
            'userId2' => $user->getUserId(),
        ];

        if ($user->getSitekey() !== 'flirt4free') {
            $parameters['domain'] = $user->getDomain();
        }

        /**
         * @var array{
         * user_id: int,
         * username: string,
         * password: string,
         * bounty_paid: string,
         * salt: string,
         * firstname: string,
         * lastname: string,
         * zip: string,
         * email: string,
         * mp_code: string,
         * domain: string,
         * sitekey: string,
         * timeleft: int,
         * timeleft_banked: int,
         * total_spent: float,
         * bill_next: int,
         * vs_vip: string,
         * status: string,
         * date_last_login: ?string,
         * date_created: ?string,
         * spending_group: string} $row
         */
        $row = $this->sqlService->getRow($sql, $parameters);

        if (empty($row)) {
            throw new Exception('No account found');
        }

        return new PlatformUser(
            userId:         $row['user_id'],
            username:       $row['username'],
            password:       $row['password'],
            bountyPaid:     $row['bounty_paid'],
            salt:           $row['salt'],
            firstname:      $row['firstname'],
            lastname:       $row['lastname'],
            zip:            $row['zip'],
            email:          $row['email'],
            mpCode:         $row['mp_code'],
            domain:         $row['domain'],
            sitekey:        $row['sitekey'],
            timeleft:       $row['timeleft'],
            timeleftBanked: $row['timeleft_banked'],
            totalSpent:     $row['total_spent'],
            billNext:       $row['bill_next'],
            vsVip:          $row['vs_vip'],
            status:         $row['status'],
            dateLastLogin:  $row['date_last_login'] !== null ? new DateTime($row['date_last_login']) : null,
            dateCreated:    $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
            spendingGroup:  $row['spending_group'],
            // NOT_IMPLEMENTED: other fields can be added here if needed
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If no account is found
     */
    public function getEarliestAccountByDetails(PlatformUser $user): PlatformUser
    {
        $sql = 'SELECT o.*
                FROM ntl_db.transact t
                LEFT JOIN ntl_db.transact_ex tx ON tx.transact_id = t.id
                LEFT JOIN ntl_db.optiusers o ON o.user_id = t.user_id
                WHERE o.firstname = :firstName
                AND o.lastname = :lastName
                AND o.zip = :zip
                AND o.mp_code NOT IN("xxxx","yyyy","zzzz")
                AND t.date_out >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 6 MONTH))
                AND o.user_id != :userId
                ' . ($user->getSitekey() === 'flirt4free' ? ' AND o.sitekey = "flirt4free" ' : ' AND o.domain = :domain ') . '
                
                ORDER BY t.id ASC LIMIT 1';

        $parameters = [
            'firstName' => $user->getFirstname(),
            'lastName'  => $user->getLastname(),
            'userId'    => $user->getUserId(),
            'zip'       => $user->getZip(),
        ];

        if ($user->getSitekey() !== 'flirt4free') {
            $parameters['domain'] = $user->getDomain();
        }

        /**
         * @var array{
         * user_id: int,
         * username: string,
         * password: string,
         * bounty_paid: string,
         * salt: string,
         * firstname: string,
         * lastname: string,
         * zip: string,
         * email: string,
         * mp_code: string,
         * domain: string,
         * sitekey: string,
         * timeleft: int,
         * timeleft_banked: int,
         * total_spent: float,
         * bill_next: int,
         * vs_vip: string,
         * status: string,
         * date_last_login: ?string,
         * date_created: ?string,
         * spending_group: string} $row
         */
        $row = $this->sqlService->getRow($sql, $parameters);

        if (empty($row)) {
            throw new Exception('No account found');
        }

        return new PlatformUser(
            userId:         $row['user_id'],
            username:       $row['username'],
            password:       $row['password'],
            bountyPaid:     $row['bounty_paid'],
            salt:           $row['salt'],
            firstname:      $row['firstname'],
            lastname:       $row['lastname'],
            zip:            $row['zip'],
            email:          $row['email'],
            mpCode:         $row['mp_code'],
            domain:         $row['domain'],
            sitekey:        $row['sitekey'],
            timeleft:       $row['timeleft'],
            timeleftBanked: $row['timeleft_banked'],
            totalSpent:     $row['total_spent'],
            billNext:       $row['bill_next'],
            vsVip:          $row['vs_vip'],
            status:         $row['status'],
            dateLastLogin:  $row['date_last_login'] !== null ? new DateTime($row['date_last_login']) : null,
            dateCreated:    $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
            spendingGroup:  $row['spending_group'],
            // NOT_IMPLEMENTED: other fields can be added here if needed
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If no account is found
     */
    public function getEarliestAccountByIovation(PlatformUser $user): PlatformUser
    {
        $time = (new DateTime())->getTimestamp();

        $iovationLaunchTime = (new DateTime('2013-08-26'))->getTimestamp();

        if ($time >= $iovationLaunchTime) {
            //
            //  CHECK:  Locate Earliest Account by Iovation Match
            //
            //
            //  Pull the device alias
            //
            $sql = 'SELECT device_alias 
						FROM BILLING.iovation_transact 
						WHERE account_code = :email
						ORDER BY datetime ASC 
						LIMIT 1';

            $parameters = [
                'email' => $user->getEmail(),
            ];

            $deviceAlias = $this->sqlService->getColumn($sql, $parameters);

            if ($deviceAlias) {
                //
                //  Pull the account_code
                //
                $sql = 'SELECT account_code 
							FROM BILLING.iovation_transact 
							WHERE datetime BETWEEN "2013-08-26" 
								AND from_unixtime(:time) 
								AND device_alias = :deviceAlias
							ORDER BY datetime ASC 
							LIMIT 1';

                $parameters = [
                    'deviceAlias' => $deviceAlias,
                    'time'        => $time,
                ];


                $accountCode = $this->sqlService->getColumn($sql, $parameters);

                if (!empty($accountCode)) {
                    $sql = 'SELECT t.user_id, t.mp_code as transact_mp_code, t.date_out, o.*
                            FROM ntl_db.transact t
                            LEFT JOIN ntl_db.transact_ex tx ON tx.transact_id = t.id
                            LEFT JOIN ntl_db.optiusers o ON o.user_id = t.user_id
                            WHERE t.date_out BETWEEN :iovationLaunchTime AND :time
                            AND t.date_out >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 6 MONTH))
                            AND o.mp_code NOT IN ("xxxx", "yyyy", "zzzz")
                            AND t.email = :accountCode
                            AND o.user_id != :userId
                               ' . ($user->getSitekey() === 'flirt4free' ? ' AND o.sitekey = "flirt4free" ' : ' AND o.domain = :domain ') . '
                            ORDER BY t.id ASC 
                            LIMIT 1';

                    $parameters = [
                        'accountCode'        => $accountCode,
                        'iovationLaunchTime' => $iovationLaunchTime,
                        'time'               => $time,
                        'userId'             => $user->getUserId(),
                    ];

                    if ($user->getSitekey() !== 'flirt4free') {
                        $parameters['domain'] = $user->getDomain();
                    }

                    /**
                     * @var array{
                     * user_id: int,
                     * username: string,
                     * password: string,
                     * bounty_paid: string,
                     * salt: string,
                     * firstname: string,
                     * lastname: string,
                     * zip: string,
                     * email: string,
                     * mp_code: string,
                     * domain: string,
                     * sitekey: string,
                     * timeleft: int,
                     * timeleft_banked: int,
                     * total_spent: float,
                     * bill_next: int,
                     * vs_vip: string,
                     * status: string,
                     * date_last_login: ?string,
                     * date_created: ?string,
                     * spending_group: string} $row
                     */
                    $row = $this->sqlService->getRow($sql, $parameters);

                    if (empty($row)) {
                        throw new Exception('No account found');
                    }

                    return new PlatformUser(
                        userId:         $row['user_id'],
                        username:       $row['username'],
                        password:       $row['password'],
                        bountyPaid:     $row['bounty_paid'],
                        salt:           $row['salt'],
                        firstname:      $row['firstname'],
                        lastname:       $row['lastname'],
                        zip:            $row['zip'],
                        email:          $row['email'],
                        mpCode:         $row['mp_code'],
                        domain:         $row['domain'],
                        sitekey:        $row['sitekey'],
                        timeleft:       $row['timeleft'],
                        timeleftBanked: $row['timeleft_banked'],
                        totalSpent:     $row['total_spent'],
                        billNext:       $row['bill_next'],
                        vsVip:          $row['vs_vip'],
                        status:         $row['status'],
                        dateLastLogin:  $row['date_last_login'] !== null ? new DateTime($row['date_last_login']) : null,
                        dateCreated:    $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
                        spendingGroup:  $row['spending_group'],
                        // NOT_IMPLEMENTED: other fields can be added here if needed
                    );
                }
            }
        }

        throw new Exception('No account found');
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If no account is found
     */
    public function getAllPriorAccountsByPaymentAccount(PlatformUser $user): PlatformUser
    {
        // Grab all user accounts that have the same bin and last four and not have the current mp code.
        $sql = 'SELECT bin, last_digits 
                    FROM BILLING.User_Payment
                WHERE user_id = :id';

        $parameters = [
            'id' => $user->getUserId(),
        ];

        $paymentDetails = $this->sqlService->getRow($sql, $parameters);


        if (empty($paymentDetails) || empty($paymentDetails['bin']) || empty($paymentDetails['last_digits'])) {
            throw new Exception('No account found');
        }

        $sql = 'SELECT o.*, ABS(o.history) AS last_purchase_timestamp
                    FROM ntl_db.optiusers o 
                    LEFT JOIN BILLING.User_Payment p on p.user_id = o.user_id
                    WHERE p.bin = :bin
                        AND p.last_digits = :last_digits
                        AND o.user_id != :user_id
                        AND o.mp_code <> :mp_code
                        AND ABS(o.history) >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 180 DAY))
                        AND o.domain = :domain
                        AND o.mp_code NOT IN("xxxx","yyyy","zzzz")
                    GROUP BY o.user_id
                    ORDER BY o.date_created ASC
                    LIMIT 1';

        $parameters = [
            'bin'         => $paymentDetails['bin'],
            'domain'      => $user->getDomain(),
            'last_digits' => $paymentDetails['last_digits'],
            'mp_code'     => $user->getMpCode(),
            'user_id'     => $user->getUserId(),
        ];

        /**
         * @var array{
         * user_id: int,
         * username: string,
         * password: string,
         * bounty_paid: string,
         * salt: string,
         * firstname: string,
         * lastname: string,
         * zip: string,
         * email: string,
         * mp_code: string,
         * domain: string,
         * sitekey: string,
         * timeleft: int,
         * timeleft_banked: int,
         * total_spent: float,
         * bill_next: int,
         * vs_vip: string,
         * status: string,
         * date_last_login: ?string,
         * date_created: ?string,
         * spending_group: string} $row
         */
        $row = $this->sqlService->getRow($sql, $parameters);
        if (empty($row)) {
            throw new Exception('No account found');
        }


        return new PlatformUser(
            userId:         $row['user_id'],
            username:       $row['username'],
            password:       $row['password'],
            bountyPaid:     $row['bounty_paid'],
            salt:           $row['salt'],
            firstname:      $row['firstname'],
            lastname:       $row['lastname'],
            zip:            $row['zip'],
            email:          $row['email'],
            mpCode:         $row['mp_code'],
            domain:         $row['domain'],
            sitekey:        $row['sitekey'],
            timeleft:       $row['timeleft'],
            timeleftBanked: $row['timeleft_banked'],
            totalSpent:     $row['total_spent'],
            billNext:       $row['bill_next'],
            vsVip:          $row['vs_vip'],
            status:         $row['status'],
            dateLastLogin:  $row['date_last_login'] !== null ? new DateTime($row['date_last_login']) : null,
            dateCreated:    $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
            spendingGroup:  $row['spending_group'],
            // NOT_IMPLEMENTED: other fields can be added here if needed
        );
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param PlatformUserOptions $options DTO of options to generate the query
     *
     * @return PlatformUser[] An array of matching platform user(s)
     */
    private function query(PlatformUserOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        // NOT_IMPLEMENTED: review/check if age_verified and payment_record are needed at all (BILLING.User_Payment may not be needed)
        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					o.user_id,
					o.username,
					o.password,
					o.salt,
					o.firstname,
					o.lastname,
                    o.zip,
					o.email,
                    o.mp_code,
					o.domain,
					o.sitekey,
                    o.bounty_paid,
                    IF(LENGTH(o.domain) = 0 OR (o.sitekey = "whitelabel" AND wl.status != "active"), "flirt4free.com", o.domain) AS active_domain,
					o.timeleft,
					o.timeleft_banked,
					o.total_spent,
					o.bill_next,
					o.vs_vip,
					o.status,
                    o.date_last_login,
					o.date_created,
					o.spending_group,
					oe.1click_processor_id AS oneclick_processor_id,
					oe.first_purchase_date,
					IFNULL(oe.age_verified,"N") AS age_verified,
					up.id IS NOT NULL AS payment_record,
					xu.id IS NOT NULL AND o.sitekey = "xvt" AS has_xvt_account,
					2fa.id AS 2fa_id,
					2fa.user_id AS 2fa_user_id,
					2fa.auth_method AS 2fa_auth_method,
					2fa.label AS 2fa_label,
					2fa.auth_key AS 2fa_auth_key,
					2fa.auth_value AS 2fa_auth_value,
					2fa.date_created AS 2fa_date_created
				FROM ntl_db.optiusers o
				LEFT OUTER JOIN VSCASH.White_Label wl ON wl.domain = o.domain
				LEFT OUTER JOIN ntl_db.optiusers_ex oe ON oe.user_id = o.user_id
				LEFT OUTER JOIN ntl_db.xvt_users xu ON xu.user_id = o.user_id AND o.sitekey = "xvt"
				LEFT OUTER JOIN ntl_db.Two_Factor_Auth 2fa ON 2fa.user_id = o.user_id
				LEFT OUTER JOIN BILLING.User_Payment up ON up.user_id = o.user_id';

        if ($options->screenName !== null) {
            $sql .= ' INNER JOIN ntl_db.screen_names sn ON sn.optiusers_id = o.user_id';
        }

        if ($options->oauthProvider !== null || $options->oauthGuid !== null) {
            $sql .= ' INNER JOIN ntl_db.optiusers_oauth oo ON oo.user_id = o.user_id';
        }

        $sql .= ' WHERE 1 = 1';

        if ($options->userId !== null) {
            $sql .= ' AND o.user_id = :userId';

            $parameters['userId'] = $options->userId;
        }

        if ($options->username !== null) {
            $sql .= ' AND (
				o.username = :username
				OR (xu.id IS NOT NULL AND o.email = :username) /* We allow XVT accounts to use their email address as their username */
			)';

            $parameters['username'] = $options->username;
        }

        if ($options->screenName !== null) {
            $sql .= ' AND sn.screen_name = :screenName';

            $parameters['screenName'] = $options->screenName;
        }

        if ($options->passwordMd5 !== null) {
            $sql .= ' AND MD5(o.password) = :passwordMd5';

            $parameters['passwordMd5'] = $options->passwordMd5;
        }

        if ($options->oauthProvider !== null) {
            $sql .= ' AND oo.oauth_type = :oauthProvider';

            $parameters['oauthProvider'] = $options->oauthProvider;
        }

        if ($options->oauthGuid !== null) {
            $sql .= ' AND oo.oauth_identifier = :oauthGuid';

            $parameters['oauthGuid'] = $options->oauthGuid;
        }

        if ($options->sitekey !== null) {
            switch ($options->sitekey) {
                //
                //  The flirt4free and whitelabel sitekeys are interchangeable
                //
                case 'flirt4free':
                case 'whitelabel':
                    $sql .= ' AND o.sitekey IN ("whitelabel", "flirt4free")';
                    break;

                default:
                    $sql .= ' AND o.sitekey = :sitekey';

                    $parameters['sitekey'] = $options->sitekey;
                    break;
            }
        }

        $sql .= ' GROUP BY o.user_id';

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     user_id: ?int,
         *     username: ?string,
         *     password: ?string,
         *     bounty_paid: ?string,
         *     salt: ?string,
         *     firstname: ?string,
         *     lastname: ?string,
         *     zip: ?string,
         *     email: ?string,
         *     mp_code: ?string,
         *     domain: ?string,
         *     sitekey: ?string,
         *     active_domain: ?string,
         *     timeleft: ?int,
         *     timeleft_banked: ?int,
         *     total_spent: ?int,
         *     bill_next: ?float,
         *     vs_vip: ?string,
         *     status: ?string,
         *     date_last_login: ?string,
         *     date_created: ?string,
         *     spending_group: ?string,
         *     oneclick_processor_id: ?int,
         *     first_purchase_date: ?string,
         *     age_verified: ?string,
         *     payment_record: ?int,
         *     has_xvt_account: ?int,
         *     '2fa_id': ?int,
         *     '2fa_user_id': ?int,
         *     '2fa_auth_method': ?string,
         *     '2fa_label': ?string,
         *     '2fa_auth_key': ?string,
         *     '2fa_auth_value': ?string,
         *     '2fa_date_created': ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters, true) as $row) {
            $twoFactorAuthentication = $row['2fa_id'] === null ? null : new PlatformUserTwoFactorAuthentication(
                id:          $row['2fa_id'],
                userId:      $row['2fa_user_id'],
                authMethod:  $row['2fa_auth_method'],
                label:       $row['2fa_label'],
                authKey:     $row['2fa_auth_key'],
                authValue:   $row['2fa_auth_value'],
                dateCreated: $row['2fa_date_created'] !== null ? new DateTime($row['2fa_date_created']) : null,
            );

            $objects[] = new PlatformUser(
                userId:                  $row['user_id'],
                username:                $row['username'],
                password:                $row['password'],
                bountyPaid:              $row['bounty_paid'],
                salt:                    $row['salt'],
                firstname:               $row['firstname'],
                lastname:                $row['lastname'],
                zip:                     $row['zip'],
                email:                   $row['email'],
                mpCode:                  $row['mp_code'],
                domain:                  $row['domain'],
                sitekey:                 $row['sitekey'],
                activeDomain:            $row['active_domain'],
                timeleft:                $row['timeleft'],
                timeleftBanked:          $row['timeleft_banked'],
                totalSpent:              $row['total_spent'],
                billNext:                $row['bill_next'],
                vsVip:                   $row['vs_vip'],
                status:                  $row['status'],
                dateLastLogin:           $row['date_last_login'] !== null ? new DateTime($row['date_last_login']) : null,
                dateCreated:             $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
                spendingGroup:           $row['spending_group'],
                oneclickProcessorId:     $row['oneclick_processor_id'],
                firstPurchaseDate:       $row['first_purchase_date'] !== null ? new DateTime($row['first_purchase_date']) : null,
                ageVerified:             $row['age_verified'],
                paymentRecord:           $row['payment_record'] === 1,
                hasXvtAccount:           $row['has_xvt_account'] === 1,
                twoFactorAuthentication: $twoFactorAuthentication,
            );
        }

        return $objects;
    }
}
