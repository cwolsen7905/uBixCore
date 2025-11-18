<?php

declare(strict_types=1);

namespace Ubix\Repository\Prospect;

use DateTime;
use DateTimeInterface;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\ProspectOptions;
use Ubix\Enum\Prospect\ProspectStatus;
use Ubix\Model\AdminUser;
use Ubix\Model\Prospect;
use Ubix\Repository\Prospect\ProspectReaderInterface as ProspectReader;
use Ubix\Repository\Prospect\ProspectWriterInterface as ProspectWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing prospect data
 *
 * @see \Ubix\Tests\Repository\Prospect\ProspectSqlRepositoryTest PHPUnit test case
 */
final class ProspectSqlRepository implements ProspectReader, ProspectWriter
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
    public function save(Prospect $prospect): void
    {
        if ($prospect->getId() !== null && $prospect->getId() > 0) { // If there is a valid ID then update the database
            $sql = 'UPDATE STUDIOS.Prospects SET
                    name                     = :name,
                    site_type                = :siteType,
                    password                 = :password,
                    enc_password             = :encPassword,
                    salt                     = :salt,
                    company_name             = :companyName,
                    email                    = :email,
                    telephone                = :telephone,
                    fax                      = :fax,
                    im_address               = :imAddress,
                    country_code             = :countryCode,
                    state                    = :state,
                    geography                = :geography,
                    birthdate                = :birthdate,
                    service                  = :service,
                    allow_text_messages      = :allowTextMessages,
                    preferred_contact_method = :preferredContactMethod,
                    contact_method_info      = :contactMethodInfo,
                    auth_key                 = :authKey,
                    experience               = :experience,
                    on_f4f                   = :onF4f,
                    stage_name               = :stageName,
                    cameras                  = :cameras,
                    performers               = :performers,
                    comments                 = :comments,
                    ip_address               = :ipAddress,
                    signup_tpl               = :signupTpl,
                    tracker                  = :tracker,
                    campaign_id              = :campaignId,
                    ref_affiliate_id         = :refAffiliateId,
                    ref_model_id             = :refModelId,
                    ref_broadcaster_id       = :refBroadcasterId,
                    ref_user_id              = :refUserId,
                    potential                = :potential,
                    status_contract          = :statusContract,
                    status_ipcheck           = :statusIpcheck,
                    status_final             = :statusFinal,
                    signup_origin            = :signupOrigin,
                    email_confirmed          = :emailConfirmed,
                    date_email_confirmed     = :dateEmailConfirmed,
                    date_last_updated        = :dateLastUpdated,
                    date_created             = :dateCreated,
                    date_reminder            = :dateReminder,
                    last_opened              = :lastOpened,
                    salesperson_id           = :salespersonId,
                    studio_admin_id          = :studioAdminId,
                    status                   = :status,
                    studio_website           = :studioWebsite
                    WHERE id = :id';

            $this->sqlService->query($sql, [
                'allowTextMessages'      => $prospect->getAllowTextMessages(),
                'authKey'                => $prospect->getAuthKey(),
                'birthdate'              => $prospect->getBirthdate()?->format('Y-m-d'),
                'cameras'                => $prospect->getCameras(),
                'campaignId'             => $prospect->getCampaignId() ?? 0,
                'comments'               => $prospect->getComments(),
                'companyName'            => $prospect->getCompanyName(),
                'contactMethodInfo'      => $prospect->getContactMethodInfo(),
                'countryCode'            => $prospect->getCountryCode(),
                'dateCreated'            => $prospect->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                'dateEmailConfirmed'     => null,
                'dateLastUpdated'        => $prospect->getDateLastUpdated()?->format(DateTime::ISO8601_EXPANDED),
                'dateReminder'           => '0000-00-00',
                'email'                  => $prospect->getEmailAddress(),
                'emailConfirmed'         => 0,
                'encPassword'            => $prospect->getEncPassword(),
                'experience'             => $prospect->getExperience(),
                'fax'                    => $prospect->getFax(),
                'geography'              => $prospect->getCountryCode(),
                'id'                     => $prospect->getId(),
                'imAddress'              => $prospect->getImAddress(),
                'ipAddress'              => $prospect->getIpAddress(),
                'lastOpened'             => '0000-00-00',
                'name'                   => $prospect->getName(),
                'onF4f'                  => $prospect->getOnF4f(),
                'password'               => $prospect->getPassword(),
                'performers'             => $prospect->getPerformers(),
                'potential'              => 'not_assigned',
                'preferredContactMethod' => $prospect->getPreferredContactMethod(),
                'refAffiliateId'         => $prospect->getRefAffiliateId() ?? 0,
                'refBroadcasterId'       => $prospect->getRefBroadcasterId() ?? 0,
                'refModelId'             => $prospect->getRefModelId() ?? 0,
                'refUserId'              => $prospect->getRefUserId() ?? 0,
                'salespersonId'          => $prospect->getSalespersonId() ?? 0,
                'salt'                   => $prospect->getSalt(),
                'service'                => $prospect->getService(),
                'signupOrigin'           => $prospect->getSignupOrigin(),
                'signupTpl'              => '',
                'siteType'               => 'adult',
                'stageName'              => $prospect->getStageName(),
                'state'                  => $prospect->getState(),
                'status'                 => $prospect->getStatus()?->value,
                'statusContract'         => 'pending',
                'statusFinal'            => $prospect->getStatusFinal(),
                'statusIpcheck'          => 'pending',
                'studioAdminId'          => $prospect->getAdminUserId() ?? 0,
                'studioWebsite'          => $prospect->getStudioWebsite() ?? '',
                'telephone'              => $prospect->getPhoneNumber(),
                'tracker'                => $prospect->getTracker(),
            ]);
        } else { // There is no valid ID so insert into the database
            $sql = 'INSERT INTO STUDIOS.Prospects SET
                    name                     = :name,
                    site_type                = :siteType,
                    password                 = :password,
                    enc_password             = :encPassword,
                    salt                     = :salt,
                    company_name             = :companyName,
                    email                    = :email,
                    telephone                = :telephone,
                    fax                      = :fax,
                    im_address               = :imAddress,
                    country_code             = :countryCode,
                    state                    = :state,
                    geography                = :geography,
                    birthdate                = :birthdate,
                    service                  = :service,
                    allow_text_messages      = :allowTextMessages,
                    preferred_contact_method = :preferredContactMethod,
                    contact_method_info      = :contactMethodInfo,
                    auth_key                 = :authKey,
                    experience               = :experience,
                    on_f4f                   = :onF4f,
                    stage_name               = :stageName,
                    cameras                  = :cameras,
                    performers               = :performers,
                    comments                 = :comments,
                    ip_address               = :ipAddress,
                    signup_tpl               = :signupTpl,
                    tracker                  = :tracker,
                    campaign_id              = :campaignId,
                    ref_affiliate_id         = :refAffiliateId,
                    ref_model_id             = :refModelId,
                    ref_broadcaster_id       = :refBroadcasterId,
                    ref_user_id              = :refUserId,
                    potential                = :potential,
                    status_contract          = :statusContract,
                    status_ipcheck           = :statusIpcheck,
                    status_final             = :statusFinal,
                    signup_origin            = :signupOrigin,
                    email_confirmed          = :emailConfirmed,
                    date_email_confirmed     = :dateEmailConfirmed,
                    date_last_updated        = :dateLastUpdated,
                    date_created             = :dateCreated,
                    date_reminder            = :dateReminder,
                    last_opened              = :lastOpened,
                    salesperson_id           = :salespersonId,
                    studio_admin_id          = :studioAdminId,
                    status                   = :status,
                    studio_website           = :studioWebsite';

            $this->sqlService->query($sql, [
                'allowTextMessages'      => $prospect->getAllowTextMessages(),
                'authKey'                => $prospect->getAuthKey(),
                'birthdate'              => $prospect->getBirthdate()?->format('Y-m-d'),
                'cameras'                => $prospect->getCameras(),
                'campaignId'             => $prospect->getCampaignId() ?? 0,
                'comments'               => $prospect->getComments(),
                'companyName'            => $prospect->getCompanyName(),
                'contactMethodInfo'      => $prospect->getContactMethodInfo(),
                'countryCode'            => $prospect->getCountryCode(),
                'dateCreated'            => $prospect->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                'dateEmailConfirmed'     => null,
                'dateLastUpdated'        => $prospect->getDateLastUpdated()?->format(DateTime::ISO8601_EXPANDED),
                'dateReminder'           => '0000-00-00',
                'email'                  => $prospect->getEmailAddress(),
                'emailConfirmed'         => 0,
                'encPassword'            => $prospect->getEncPassword(),
                'experience'             => $prospect->getExperience(),
                'fax'                    => $prospect->getFax(),
                'geography'              => $prospect->getCountryCode(),
                'imAddress'              => $prospect->getImAddress(),
                'ipAddress'              => $prospect->getIpAddress(),
                'lastOpened'             => '0000-00-00',
                'name'                   => $prospect->getName(),
                'onF4f'                  => $prospect->getOnF4f(),
                'password'               => $prospect->getPassword(),
                'performers'             => $prospect->getPerformers(),
                'potential'              => 'not_assigned',
                'preferredContactMethod' => $prospect->getPreferredContactMethod(),
                'refAffiliateId'         => $prospect->getRefAffiliateId() ?? 0,
                'refBroadcasterId'       => $prospect->getRefBroadcasterId() ?? 0,
                'refModelId'             => $prospect->getRefModelId() ?? 0,
                'refUserId'              => $prospect->getRefUserId() ?? 0,
                'salespersonId'          => $prospect->getSalespersonId() ?? 0,
                'salt'                   => $prospect->getSalt(),
                'service'                => $prospect->getService(),
                'signupOrigin'           => $prospect->getSignupOrigin(),
                'signupTpl'              => '',
                'siteType'               => 'adult',
                'stageName'              => $prospect->getStageName(),
                'state'                  => $prospect->getState(),
                'status'                 => $prospect->getStatus()?->value,
                'statusContract'         => 'pending',
                'statusFinal'            => $prospect->getStatusFinal(),
                'statusIpcheck'          => 'pending',
                'studioAdminId'          => $prospect->getAdminUserId() ?? 0,
                'studioWebsite'          => $prospect->getStudioWebsite() ?? '',
                'telephone'              => $prospect->getPhoneNumber(),
                'tracker'                => $prospect->getTracker(),
            ]);

            //
            //  Update the object
            //
            $prospect->setId((int)$this->sqlService->lastInsertId());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(?string $emailAddress = null, ?string $phoneNumber = null, ?DateTimeInterface $dateOfBirth = null, ?string $name = null, bool $includeAdminEmail = true): array
    {
        return $this->query(new ProspectOptions(
            emailAddress:      $emailAddress,
            phoneNumber:       $phoneNumber,
            dateOfBirth:       $dateOfBirth,
            name:              $name,
            includeAdminEmail: $includeAdminEmail,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getById($id): array
    {
        return $this->query(new ProspectOptions(
            id:    $id,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByStageName(string $stageName): array
    {
        return $this->query(new ProspectOptions(
            stageName: $stageName,
            limit:     1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getPartialMatches(?string $name, ?string $studioName): array
    {
        return $this->query(new ProspectOptions(
            namePartial:       $name,
            studioNamePartial: $studioName,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param ProspectOptions $options DTO of options to generate the query
     *
     * @return Prospect[] An array of prospect objects
     */
    private function query(ProspectOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
                    p.id,
                    p.name,
                    p.password,
                    p.enc_password,
                    p.salt,
                    p.telephone,
                    p.email,
                    p.salesperson_id,
                    p.studio_admin_id as admin_user_id,
                    ' . ($options->includeAdminEmail ? 'au.email as admin_user_email,' : '') . '
                    p.status,
                    p.site_type,
                    p.company_name,
                    p.fax,
                    p.im_address,
                    p.country_code,
                    p.state,
                    p.birthdate,
                    p.service,
                    p.allow_text_messages,
                    p.preferred_contact_method,
                    p.contact_method_info,
                    p.auth_key,
                    p.experience,
                    p.on_f4f,
                    p.stage_name,
                    p.cameras,
                    p.performers,
                    p.comments,
                    p.ip_address,
                    p.tracker,
                    p.campaign_id,
                    p.ref_affiliate_id,
                    p.ref_model_id,
                    p.ref_broadcaster_id,
                    p.ref_user_id,
                    p.status_final,
                    p.signup_origin,
                    p.email_confirmed,
                    p.date_email_confirmed,
                    p.date_last_updated,
                    p.date_created
                FROM STUDIOS.Prospects p
                ' . ($options->includeAdminEmail ? 'LEFT OUTER JOIN STUDIOS.Admin_Users au ON au.email = p.email' : '') . '
                WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND p.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->emailAddress !== null) {
            $sql .= ' AND LOWER(p.email) = :emailAddress';

            $parameters['emailAddress'] = strtolower($options->emailAddress);
        }

        if ($options->phoneNumber !== null) {
            $sql .= ' AND p.telephone = :phoneNumber';

            $parameters['phoneNumber'] = $options->phoneNumber;
        }

        if ($options->dateOfBirth !== null) {
            $sql .= ' AND p.birthdate = :dateOfBirth';

            $parameters['dateOfBirth'] = $options->dateOfBirth->format('Y-m-d');
        }

        if ($options->name !== null) {
            $sql .= ' AND p.name = :name';

            $parameters['name'] = strtolower(trim($options->name));
        }

        if ($options->namePartial !== null || $options->studioNamePartial !== null) {
            $sql .= ' AND (';
            if ($options->namePartial !== null) {
                $sql .= 'LOWER(p.name) = :namePartial';

                $parameters['namePartial'] = strtolower($options->namePartial);
            }
            if ($options->studioNamePartial !== null) {
                if ($options->namePartial !== null) {
                    $sql .= ' OR ';
                }
                $sql .= 'LOWER(p.company_name) = :studioNamePartial';

                $parameters['studioNamePartial'] = strtolower($options->studioNamePartial);
            }
            $sql .= ')';
        }

        if ($options->stageName !== null) {
            $sql .= ' AND p.stage_name = :stageName';

            $parameters['stageName'] = strtolower($options->stageName);
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
         *     password: ?string,
         *     enc_password: ?string,
         *     salt: ?string,
         *     telephone: ?string,
         *     email: ?string,
         *     salesperson_id: ?int,
         *     admin_user_id: ?int,
         *     admin_user_email: ?string,
         *     status: ?int,
         *     site_type: ?string,
         *     company_name: ?string,
         *     fax: ?string,
         *     im_address: ?string,
         *     country_code: ?string,
         *     state: ?string,
         *     birthdate: ?string,
         *     service: ?string,
         *     allow_text_messages: ?int,
         *     preferred_contact_method: ?string,
         *     contact_method_info: ?string,
         *     auth_key: ?string,
         *     experience: ?string,
         *     on_f4f: ?string,
         *     stage_name: ?string,
         *     cameras: ?string,
         *     performers: ?string,
         *     comments: ?string,
         *     ip_address: ?string,
         *     tracker: ?string,
         *     campaign_id: ?int,
         *     ref_affiliate_id: ?int,
         *     ref_model_id: ?int,
         *     ref_broadcaster_id: ?int,
         *     ref_user_id: ?int,
         *     status_final: ?string,
         *     signup_origin: ?string,
         *     email_confirmed: ?int,
         *     date_email_confirmed: ?string,
         *     date_last_updated: ?string,
         *     date_created: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $objects[] = new Prospect(
                id:                     $row['id'],
                name:                   $row['name'],
                password:               $row['password'],
                encPassword:            $row['enc_password'],
                salt:                   $row['salt'],
                phoneNumber:            $row['telephone'],
                emailAddress:           $row['email'],
                salespersonId:          $row['salesperson_id'],
                status:                 ProspectStatus::tryFrom($row['status'] ?? ''),
                siteType:               $row['site_type'],
                companyName:            $row['company_name'],
                fax:                    $row['fax'],
                imAddress:              $row['im_address'],
                countryCode:            $row['country_code'],
                state:                  $row['state'],
                birthdate:              $row['birthdate'] !== null ? new DateTime($row['birthdate']) : null,
                service:                $row['service'],
                allowTextMessages:      $row['allow_text_messages'],
                preferredContactMethod: $row['preferred_contact_method'],
                contactMethodInfo:      $row['contact_method_info'],
                authKey:                $row['auth_key'],
                experience:             $row['experience'],
                onF4f:                  $row['on_f4f'],
                stageName:              $row['stage_name'],
                cameras:                $row['cameras'],
                performers:             $row['performers'],
                comments:               $row['comments'],
                ipAddress:              $row['ip_address'],
                tracker:                $row['tracker'],
                campaignId:             $row['campaign_id'],
                refAffiliateId:         $row['ref_affiliate_id'],
                refModelId:             $row['ref_model_id'],
                refBroadcasterId:       $row['ref_broadcaster_id'],
                refUserId:              $row['ref_user_id'],
                statusFinal:            $row['status_final'],
                signupOrigin:           $row['signup_origin'],
                emailConfirmed:         $row['email_confirmed'],
                dateEmailConfirmed:     $row['date_email_confirmed'] !== null ? new DateTime($row['date_email_confirmed']) : null,
                dateLastUpdated:        $row['date_last_updated'] !== null ? new DateTime($row['date_last_updated']) : null,
                dateCreated:            $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
                adminUserId:            $row['admin_user_id'],
                adminUser:              new AdminUser(
                    id:    $row['admin_user_id'],
                    email: $row['admin_user_email'] ?? '',
                ),
            );
        }

        return $objects;
    }
}
