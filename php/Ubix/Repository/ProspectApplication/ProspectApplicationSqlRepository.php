<?php

declare(strict_types=1);

namespace Ubix\Repository\ProspectApplication;

use DateTime;
use DateTimeInterface;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\ProspectApplicationOptions;
use Ubix\Enum\ProspectApplication\ProspectApplicationContactMethod;
use Ubix\Enum\ProspectApplication\ProspectApplicationGender;
use Ubix\Enum\ProspectApplication\ProspectApplicationIdentificationMethod;
use Ubix\Enum\ProspectApplication\ProspectApplicationSectionStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationType;
use Ubix\Enum\ProspectDocument\ProspectDocumentType;
use Ubix\Enum\ProspectImage\ProspectImageType;
use Ubix\Model\ProspectApplication;
use Ubix\Model\ProspectApplicationFile;
use Ubix\Model\ProspectApplicationSection;
use Ubix\Repository\ProspectApplication\ProspectApplicationReaderInterface as ProspectApplicationReader;
use Ubix\Repository\ProspectApplication\ProspectApplicationWriterInterface as ProspectApplicationWriter;
use Ubix\Service\JsonService;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing fan prospect application data
 *
 * @see \Ubix\Tests\Repository\ProspectApplication\ProspectApplicationSqlRepositoryTest PHPUnit test case
 */
final class ProspectApplicationSqlRepository implements ProspectApplicationReader, ProspectApplicationWriter
{
    /**
     * Constructor
     *
     * @param Logger      $logger      Logger
     * @param JsonService $jsonService JSON service
     * @param SqlService  $sqlService  SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private JsonService $jsonService,
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function save(ProspectApplication $prospectApplication): void
    {
        //
        //  Validate the object // NOT_IMPLEMENTED: this will need to change once Chris and Andrew complete the Ubix\DataType classes
        //
        $prospectApplication->calculateStatus();
        $prospectApplication->setDateCreated(new DateTime());

        //
        //  INSERT a new row into the database
        //
        $sql = 'INSERT INTO STUDIOS.Prospects_Applications SET
                application_id     = :applicationId,
                application_status = :applicationStatus,
                application_data   = :applicationData,
                date_created       = :dateCreated';


        $this->sqlService->query($sql, [
            'applicationData'   => $this->jsonService->encode($prospectApplication->getApplicationData()),
            'applicationId'     => $prospectApplication->getApplicationId(),
            'applicationStatus' => $prospectApplication->getApplicationStatus() === null ? null : strtoupper($prospectApplication->getApplicationStatus()->value),
            'dateCreated'       => $prospectApplication->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
        ]);

        //
        //  Update the object
        //
        $prospectApplication->setId((int)$this->sqlService->lastInsertId());
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $id): array
    {
        return $this->query(new ProspectApplicationOptions(
            id:    $id,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByApplicationId(string $applicationId, ?int $id = null): array
    {
        return $this->query(new ProspectApplicationOptions(
            applicationId: $applicationId,
            id:            $id,
            limit:         1,
            orderBy:       'pa.id DESC',
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param ProspectApplicationOptions $options DTO of options to generate the query
     *
     * @return ProspectApplication[] An array of application objects
     */
    private function query(ProspectApplicationOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
                    pa.id,
                    pa.application_id,
                    pa.application_status,
                    pa.application_data,
                    pa.date_created
                FROM STUDIOS.Prospects_Applications pa
                WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND pa.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->applicationId !== null) {
            $sql .= ' AND pa.application_id = :applicationId';

            $parameters['applicationId'] = $options->applicationId;
        }

        if ($options->orderBy !== null) {
            $sql .= ' ORDER BY ' . $options->orderBy;
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
         *     application_id: ?string,
         *     application_status: ?string,
         *     application_data: ?string,
         *     date_created: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            //
            //  The application is stored in as a single JSON dump in the application_data column so decode it
            //
            /**
             * @var array{
             *     status?: ?array<string, ?array{
             *         state?: ?string,
             *         approval_timestamp?: ?string,
             *         rejection_reason?: ?string,
             *         rejection_timestamp?: ?string,
             *         date_created?: ?string,
             *         date_updated?: ?string,
             *     }>,
             *     data?: ?array{
             *         campaign_id?: ?int,
             *         date_created?: ?string,
             *         dob?: ?string,
             *         documents?: ?array<string, ?array{
             *             type?: ?string,
             *             url?: ?string,
             *             md5Hash?: ?string,
             *             date_created?: ?string,
             *         }>,
             *         email?: ?string,
             *         email_confirm_code?: ?string,
             *         fake_dob?: ?string,
             *         first_name?: ?string,
             *         images?: ?array<string, ?array{
             *             type?: ?string,
             *             url?: ?string,
             *             md5Hash?: ?string,
             *             date_created?: ?string,
             *         }>,
             *         gender?: ?string,
             *         geography?: ?string,
             *         id_number?: ?string,
             *         ip_address?: ?string,
             *         issuing_authority?: ?string,
             *         language?: ?string,
             *         last_name?: ?string,
             *         preferred_contact_method?: ?string,
             *         ref_affiliate_id?: ?string,
             *         ref_broadcaster_id?: ?string,
             *         ref_model_id?: ?string,
             *         ref_screen_name?: ?string,
             *         service?: ?string,
             *         stage_name?: ?string,
             *         state?: ?string,
             *         studio_name?: ?string,
             *         studio_website?: ?string,
             *         telephone?: ?string,
             *         type?: ?string,
             *         type_of_identification?: ?string,
             *     },
             * } $applicationData
             */
            $applicationData = $this->jsonService->decode($row['application_data'] ?? '');

            //
            //  Using the decoded application data we can determine the prospect application's sections
            //
            $prospectApplicationSections = [];
            if (isset($applicationData['status']) && is_array($applicationData['status'])) {
                foreach ($applicationData['status'] as $name => $status) {
                    $prospectApplicationSections[] = new ProspectApplicationSection(
                        name:               $name,
                        status:             match (strtolower($status['state'] ?? '')) {
                            'approved' => ProspectApplicationSectionStatus::APPROVED,
                            'pending'  => ProspectApplicationSectionStatus::PENDING,
                            'rejected' => ProspectApplicationSectionStatus::REJECTED,
                            default    => null,
                        },
                        approvalTimestamp:  $this->valueToDateTime($status['approval_timestamp'] ?? ''),
                        rejectionReason:    $status['rejection_reason'] ?? '',
                        rejectionTimestamp: $this->valueToDateTime($status['rejection_timestamp'] ?? ''),
                        dateCreated:        $this->valueToDateTime($status['date_created'] ?? ''),
                        dateUpdated:        $this->valueToDateTime($status['date_updated'] ?? ''),
                        rejectSuggest1:     $status['reject_suggest1'] ?? '',
                        rejectSuggest2:     $status['reject_suggest2'] ?? '',
                        rejectSuggest3:     $status['reject_suggest3'] ?? '',
                    );
                }
            }

            //
            //  Using the decoded application data we can determine the prospect application's documents
            //
            $applicationDocuments = [];
            if (isset($applicationData['data']['documents']) && is_array($applicationData['data']['documents'])) {
                foreach ($applicationData['data']['documents'] as $name => $document) {
                    $applicationDocuments[] = new ProspectApplicationFile(
                        type:        ProspectDocumentType::tryFrom(strtolower($document['type'] ?? '')),
                        url:         $document['url'] ?? '',
                        md5Hash:     $document['md5Hash'] ?? '',
                        dateCreated: $this->valueToDateTime($document['date_created'] ?? ''),
                    );
                }
            }

            //
            //  Using the decoded application data we can determine the prospect application's documents
            //
            $applicationImages = [];
            if (isset($applicationData['data']['images']) && is_array($applicationData['data']['images'])) {
                foreach ($applicationData['data']['images'] as $name => $image) {
                    $applicationImages[] = new ProspectApplicationFile(
                        type:        ProspectImageType::tryFrom(strtolower($image['type'] ?? '')),
                        url:         $image['url'] ?? '',
                        md5Hash:     $image['md5Hash'] ?? '',
                        dateCreated: $this->valueToDateTime($image['date_created'] ?? ''),
                    );
                }
            }

            //
            //  Generate the prospect application object
            //
            $objects[] = new ProspectApplication(
                id:                          $row['id'],
                applicationDocuments:        $applicationDocuments,
                applicationId:               $row['application_id'],
                applicationImages:           $applicationImages,
                applicationStatus:           ProspectApplicationStatus::tryFrom(strtolower($row['application_status'] ?? '')),
                adminId:                     $applicationData['data']['admin_id'] ?? null,
                campaignId:                  $applicationData['data']['campaign_id'] ?? null,
                dob:                         $this->valueToDateTime($applicationData['data']['dob'] ?? ''),
                email:                       $applicationData['data']['email'] ?? null,
                emailConfirmCode:            $applicationData['data']['email_confirm_code'] ?? null,
                fakeDob:                     $this->valueToDateTime($applicationData['data']['fake_dob'] ?? ''),
                firstName:                   $applicationData['data']['first_name'] ?? null,
                gender:                      ProspectApplicationGender::tryFrom(strtolower($applicationData['data']['gender'] ?? '')),
                geography:                   $applicationData['data']['geography'] ?? null,
                idNumber:                    $applicationData['data']['id_number'] ?? null,
                ipAddress:                   $applicationData['data']['ip_address'] ?? null,
                issuingAuthority:            $applicationData['data']['issuing_authority'] ?? null,
                language:                    $applicationData['data']['language'] ?? null,
                lastName:                    $applicationData['data']['last_name'] ?? null,
                preferredContactMethod:      ProspectApplicationContactMethod::tryFrom(strtolower($applicationData['data']['preferred_contact_method'] ?? '')),
                refAffiliateId:              $applicationData['data']['ref_affiliate_id'] ?? null,
                refBroadcasterId:            $applicationData['data']['ref_broadcaster_id'] ?? null,
                refModelId:                  $applicationData['data']['ref_model_id'] ?? null,
                refScreenName:               $applicationData['data']['ref_screen_name'] ?? null,
                service:                     $applicationData['data']['service'] ?? null,
                stageName:                   $applicationData['data']['stage_name'] ?? null,
                state:                       $applicationData['data']['state'] ?? null,
                studioName:                  $applicationData['data']['studio_name'] ?? null,
                studioWebsite:               $applicationData['data']['studio_website'] ?? null,
                telephone:                   $applicationData['data']['telephone'] ?? null,
                type:                        match (strtolower($applicationData['data']['type'] ?? '')) {
                    'performer' => ProspectApplicationType::PERFORMER,
                    'studio'    => ProspectApplicationType::STUDIO,
                    default     => null,
                },
                typeOfIdentification:        match (strtolower($applicationData['data']['type_of_identification'] ?? '')) {
                    'drivers license' => ProspectApplicationIdentificationMethod::DRIVERS_LICENSE,
                    'passport'        => ProspectApplicationIdentificationMethod::PASSPORT,
                    'state id'        => ProspectApplicationIdentificationMethod::STATE_ID,
                    default           => null,
                },
                dateCreated:                 $this->valueToDateTime($applicationData['data']['date_created'] ?? ''),
                prospectApplicationSections: $prospectApplicationSections,
            );
        }

        return $objects;
    }

    /**
     * Transform a database value
     *
     * @param string $databaseValue The value
     *
     * @return ?DateTimeInterface If a valid date string has been passed in return an object that implements the DateTimeInterface otherwise return null
     */
    private function valueToDateTime(string $databaseValue): ?DateTimeInterface
    {
        return $databaseValue !== '' ? new DateTime($databaseValue) : null;
    }
}
