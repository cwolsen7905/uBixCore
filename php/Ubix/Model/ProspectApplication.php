<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use Ubix\DataTransferObject\ModelDiff;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\ProspectApplication\ProspectApplicationContactMethod;
use Ubix\Enum\ProspectApplication\ProspectApplicationGender;
use Ubix\Enum\ProspectApplication\ProspectApplicationIdentificationMethod;
use Ubix\Enum\ProspectApplication\ProspectApplicationSectionStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationType;
use Ubix\Enum\ProspectDocument\ProspectDocumentType;
use Ubix\Enum\ProspectImage\ProspectImageType;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a prospect application
 *
 * @see \Ubix\Tests\Model\ProspectApplicationTest PHPUnit test case
 */
final class ProspectApplication extends Model
{
    private const SECTIONS_TO_EXCLUDE_FROM_DATA_DIFF = [
        'is_dupe', // This is a request from Kean, he doesn't want the "is_dupe" section persisting when we write to the database
    ];

    private self $originalObject;

    /**
     * Constructor
     *
     * @param ?int                                     $id                          The ID of the row
     * @param ?string                                  $applicationId               The ID of the application
     * @param ?ProspectApplicationStatus               $applicationStatus           The status of the application
     * @param ?int                                     $adminId                     The admin ID
     * @param ?int                                     $campaignId                  The campaign ID
     * @param ?DateTimeInterface                       $dob                         The date of birth
     * @param ?string                                  $email                       The email address
     * @param ?string                                  $emailConfirmCode            The email confirmation code
     * @param ?DateTimeInterface                       $fakeDob                     The fake date of birth
     * @param ?string                                  $firstName                   The first name
     * @param ?ProspectApplicationGender               $gender                      The gender
     * @param ?string                                  $geography                   The country code
     * @param ?string                                  $idNumber                    The ID number
     * @param ?string                                  $ipAddress                   The IP address
     * @param ?string                                  $issuingAuthority            The issuing authority
     * @param ?string                                  $language                    The language
     * @param ?string                                  $lastName                    The last name
     * @param ?ProspectApplicationContactMethod        $preferredContactMethod      The preferred contact method
     * @param ?string                                  $refAffiliateId              The referring affiliate ID
     * @param ?string                                  $refBroadcasterId            The referring broadcaster ID
     * @param ?string                                  $refModelId                  The referring model ID
     * @param ?string                                  $refScreenName               The referring screen name
     * @param ?string                                  $service                     The service
     * @param ?string                                  $stageName                   The stage name
     * @param ?string                                  $state                       The state code
     * @param ?string                                  $studioName                  The studio name
     * @param ?string                                  $studioWebsite               The studio website
     * @param ?string                                  $telephone                   The telephone number
     * @param ?ProspectApplicationType                 $type                        The type of application
     * @param ?ProspectApplicationIdentificationMethod $typeOfIdentification        The identification method
     * @param ?string                                  $prospectStatusFinal         The final status of the prospect
     * @param ?DateTimeInterface                       $dateCreated                 When the application was created
     * @param ?ProspectApplicationSection[]            $prospectApplicationSections The prospect application's sections
     * @param ?ProspectImage[]                         $prospectImages              The prospect's images
     * @param ?Prospect                                $prospect                    The prospect
     * @param ?ProspectApplicationFile[]               $applicationDocuments        The prospect application's documents
     * @param ?ProspectApplicationFile[]               $applicationImages           The prospect application's images
     */
    public function __construct(
        private ?int $id = null,
        private ?string $applicationId = null,
        private ?ProspectApplicationStatus $applicationStatus = null,
        private ?int $adminId = null,
        private ?int $campaignId = null,
        private ?DateTimeInterface $dob = null,
        private ?string $email = null,
        private ?string $emailConfirmCode = null,
        private ?DateTimeInterface $fakeDob = null,
        private ?string $firstName = null,
        private ?ProspectApplicationGender $gender = null,
        private ?string $geography = null, // Country code
        private ?string $idNumber = null,
        private ?string $ipAddress = null,
        private ?string $issuingAuthority = null,
        private ?string $language = null,
        private ?string $lastName = null,
        private ?ProspectApplicationContactMethod $preferredContactMethod = null,
        private ?string $refAffiliateId = null,
        private ?string $refBroadcasterId = null,
        private ?string $refModelId = null,
        private ?string $refScreenName = null,
        private ?string $service = null,
        private ?string $stageName = null,
        private ?string $state = null, // State code
        private ?string $studioName = null,
        private ?string $studioWebsite = null,
        private ?string $telephone = null,
        private ?ProspectApplicationType $type = null,
        private ?ProspectApplicationIdentificationMethod $typeOfIdentification = null,
        private ?string $prospectStatusFinal = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?array $prospectApplicationSections = null,
        private ?array $prospectImages = null,
        private ?Prospect $prospect = null,
        private ?array $applicationDocuments = null,
        private ?array $applicationImages = null,
    ) {
        //
        //  Clone the original object for diff tracking
        //
        $this->originalObject = clone $this;

        $sections = [];
        foreach ($prospectApplicationSections ?? [] as $section) {
            $sections[] = clone $section;
        }
        $this->originalObject->setProspectApplicationSections($sections);

        $images = [];
        foreach ($prospectImages ?? [] as $image) {
            $images[] = clone $image;
        }
        $this->originalObject->setProspectImages($images);

        $documents = [];
        foreach ($applicationDocuments ?? [] as $document) {
            $documents[] = clone $document;
        }
        $this->originalObject->setApplicationDocuments($documents);

        $images = [];
        foreach ($applicationImages ?? [] as $image) {
            $images[] = clone $image;
        }
        $this->originalObject->setApplicationImages($images);
    }

    /**
     * Validate the application's document upload eligibility
     *
     * @throws Exception If the application is not eligible
     *
     * @return void
     */
    public function validateDocumentUploadEligibility(): void
    {
        if ($this->getProspect() === null) {
            throw new Exception(
                'The application must be associated with a prospect',
                ExceptionCode::PROSPECT_APPLICATION_MISSING_PROSPECT->value,
            );
        }

        if ($this->getSectionStatus(ProspectApplicationSection::NAME_PERSONAL_DETAILS) !== ProspectApplicationSectionStatus::APPROVED) {
            throw new Exception(
                'The application must have its personal details approved before generating a document',
                ExceptionCode::PROSPECT_APPLICATION_PERSONAL_DETAILS_NOT_APPROVED->value,
            );
        }

        if ($this->getSectionStatus(ProspectImageType::PHOTO_ID->value) !== ProspectApplicationSectionStatus::APPROVED) {
            throw new Exception(
                'The application must have its photo ID image approved before generating a document',
                ExceptionCode::PROSPECT_APPLICATION_PHOTO_ID_NOT_APPROVED->value,
            );
        }

        if ($this->getSectionStatus(ProspectImageType::PHOTO_ID_FACE->value) !== ProspectApplicationSectionStatus::APPROVED) {
            throw new Exception(
                'The application must have its photo ID (face) image approved before generating a document',
                ExceptionCode::PROSPECT_APPLICATION_PHOTO_ID_FACE_NOT_APPROVED->value,
            );
        }

        if ($this->getSectionStatus(ProspectImageType::PHOTO_ID_FRONT->value) !== ProspectApplicationSectionStatus::APPROVED) {
            throw new Exception(
                'The application must have its photo ID (front) image approved before generating a document',
                ExceptionCode::PROSPECT_APPLICATION_PHOTO_ID_FRONT_NOT_APPROVED->value,
            );
        }
    }

    /**
     * Calculate the application status based on the current data
     *
     * @return void
     */
    public function calculateStatus(): void
    {
        switch ($this->getApplicationStatus()) {
            case ProspectApplicationStatus::APPROVED:
            case ProspectApplicationStatus::REJECTED:
                return; // No need to recalculate if the application is already approved or rejected
        }

        try {
            $personalDetailsSection = $this->getSection(ProspectApplicationSection::NAME_PERSONAL_DETAILS);
        } catch (Exception $e) {
            $personalDetailsSection = null;
        }

        $stateIsValid = $this->getGeography() !== 'US' || ($this->getState() !== null && $this->getState() !== '');

        if (
            $this->getType() !== null
            && $this->getFirstName() !== null
            && $this->getFirstName() !== ''
            && $this->getLastName() !== null
            && $this->getLastName() !== ''
            && filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL) !== false
            && $this->getTelephone() !== null
            && $this->getTelephone() !== ''
            && $this->getPreferredContactMethod() !== null
            && $this->getGeography() !== null
            && $this->getGeography() !== ''
            && $stateIsValid
            && $this->getProspect()?->getPassword() !== null
            && $this->getProspect()->getPassword() !== ''
            && $this->getGender() !== null
            && $this->getDob() !== null
            && $this->getFakeDob() !== null
            && $this->getStageName() !== null
            && $this->getTypeOfIdentification() !== null
            && $this->getIssuingAuthority() !== null
            && $this->getIdNumber() !== null
            && $personalDetailsSection?->getStatus() !== ProspectApplicationSectionStatus::REJECTED // If the personal details have been rejected that means the application should be set to ProspectApplicationStatus::MISSING_PERSONAL_DATA as the personal data must be updated to proceed
        ) {
            try {
                $stageNameSection = $this->getSection(ProspectApplicationSection::NAME_STAGE_NAME);
            } catch (Exception $e) {
                $stageNameSection = null;
            }

            try {
                $photoIdSection = $this->getSection(ProspectImageType::PHOTO_ID->value);
            } catch (Exception $e) {
                $photoIdSection = null;
            }

            try {
                $photoIdFaceSection = $this->getSection(ProspectImageType::PHOTO_ID_FACE->value);
            } catch (Exception $e) {
                $photoIdFaceSection = null;
            }

            try {
                $photoIdFrontSection = $this->getSection(ProspectImageType::PHOTO_ID_FRONT->value);
            } catch (Exception $e) {
                $photoIdFrontSection = null;
            }

            if (
                $personalDetailsSection !== null
                && $personalDetailsSection->getStatus() === ProspectApplicationSectionStatus::APPROVED
                && $stageNameSection !== null
                && $stageNameSection->getStatus() === ProspectApplicationSectionStatus::APPROVED
                && $photoIdSection !== null
                && $photoIdSection->getStatus() === ProspectApplicationSectionStatus::APPROVED
                && $photoIdFaceSection !== null
                && $photoIdFaceSection->getStatus() === ProspectApplicationSectionStatus::APPROVED
                && $photoIdFrontSection !== null
                && $photoIdFrontSection->getStatus() === ProspectApplicationSectionStatus::APPROVED
            ) {
                try {
                    $document2257 = $this->getFile(ProspectDocumentType::DOCUMENT_2257);
                    $bcAgreement  = $this->getFile(ProspectDocumentType::BROADCASTER_AGREEMENT);
                } catch (Exception $e) {
                    $document2257 = null;
                    $bcAgreement  = null;
                }

                if (
                    $document2257 === null // Document 2257 is missing
                    || $this->getSectionStatus(ProspectDocumentType::DOCUMENT_2257->value) === ProspectApplicationSectionStatus::REJECTED // Document 2257 is rejected
                    || $bcAgreement === null // Broadcaster agreement is missing
                    || $this->getSectionStatus(ProspectDocumentType::BROADCASTER_AGREEMENT->value) === ProspectApplicationSectionStatus::REJECTED // Broadcaster agreement is rejected
                ) {
                    $this->setApplicationStatus(ProspectApplicationStatus::MISSING_DOCUMENTS);
                } elseif (
                    $this->getSectionStatus(ProspectDocumentType::DOCUMENT_2257->value) === ProspectApplicationSectionStatus::APPROVED
                    && $this->getSectionStatus(ProspectDocumentType::BROADCASTER_AGREEMENT->value) === ProspectApplicationSectionStatus::APPROVED
                ) {
                    $this->setApplicationStatus(ProspectApplicationStatus::DOCUMENTS_APPROVED);
                } else {
                    $this->setApplicationStatus(ProspectApplicationStatus::DOCUMENTS_PENDING_REVIEW);
                }
            } else {
                $this->setApplicationStatus(ProspectApplicationStatus::PERSONAL_DATA_PENDING_REVIEW);
            }
        } else {
            $this->setApplicationStatus(ProspectApplicationStatus::MISSING_PERSONAL_DATA);
        }
    }

    /**
     * Return a file based on type
     *
     * @param ProspectDocumentType|ProspectImageType $type The document or image type
     *
     * @throws InvalidArgumentException When the application doesn't have that type of file
     *
     * @return ProspectApplicationFile The file
     */
    public function getFile(ProspectDocumentType|ProspectImageType $type): ProspectApplicationFile
    {
        //
        //  Check for an existing file of the same type
        //
        $files = match (get_class($type)) {
            ProspectDocumentType::class => $this->getApplicationDocuments(),
            ProspectImageType::class    => $this->getApplicationImages(),
        } ?? [];
        foreach ($files as $file) {
            if ($file->getType() === $type) {
                return $file;
            }
        }

        //
        //  Throw an exception is none is found
        //
        throw new InvalidArgumentException('The application does not have a file of type ' . $type->value, ExceptionCode::PROSPECT_APPLICATION_FILE_TYPE_NOT_FOUND->value);
    }

    /**
     * Add a file to the application
     *
     * @param ProspectDocumentType|ProspectImageType $type    The document or image type
     * @param string                                 $url     The file's URL
     * @param string                                 $md5Hash The file's MD5 hash
     *
     * @throws InvalidArgumentException When the application already has that type of file
     *
     * @return void
     */
    public function addFile(ProspectDocumentType|ProspectImageType $type, string $url, string $md5Hash): void
    {
        //
        //  Check for an existing file of the same type
        //
        try {
            $file = $this->getFile($type); // This will throw an exception if one is found
        } catch (Exception $e) {
            $file = null;
        }

        if ($file !== null) {
            throw new InvalidArgumentException('The application already has a file of type ' . $type->value, ExceptionCode::PROSPECT_APPLICATION_FILE_TYPE_ALREADY_EXISTS->value);
        }

        //
        //  Add the new file to the appropriate array
        //
        $newFile = new ProspectApplicationFile(
            type:        $type,
            url:         $url,
            md5Hash:     $md5Hash,
            dateCreated: new DateTime(),
        );
        switch (get_class($type)) {
            case ProspectDocumentType::class:
                $this->setApplicationDocuments(
                    array_merge($this->getApplicationDocuments() ?? [], [$newFile]),
                );
                break;

            case ProspectImageType::class:
                $this->setApplicationImages(
                    array_merge($this->getApplicationImages() ?? [], [$newFile]),
                );
                break;
        }
    }

    /**
     * Remove a file from the application
     *
     * @param ProspectDocumentType|ProspectImageType $type The document or image type
     *
     * @throws InvalidArgumentException When the application doesn't have that type of file
     *
     * @return void
     */
    public function removeFile(ProspectDocumentType|ProspectImageType $type): void
    {
        //
        //  Check for an existing file of the same type
        //
        try {
            $existing = $this->getFile($type);
        } catch (Exception $e) {
            $existing = null;
        }

        if ($existing === null) {
            throw new InvalidArgumentException(
                'The application does not have a file of type ' . $type->value,
                ExceptionCode::PROSPECT_APPLICATION_FILE_TYPE_MISSING->value,
            );
        }

        //
        //  Remove the new file from the appropriate array
        //
        switch (get_class($type)) {
            case ProspectDocumentType::class:
                $filtered = array_filter($this->getApplicationDocuments() ?? [], function ($file) use ($type) {
                    return $file->getType() !== $type;
                });
                $this->setApplicationDocuments(array_values($filtered));
                break;

            case ProspectImageType::class:
                $filtered = array_filter($this->getApplicationImages() ?? [], function ($file) use ($type) {
                    return $file->getType() !== $type;
                });
                $this->setApplicationImages(array_values($filtered));
                break;
        }
    }

    /**
     * Check if the prospect application has a confirmed email address
     *
     * @return bool Whether or not the application has a confirmed email address
     */
    public function hasConfirmedEmail(): bool
    {
        foreach ($this->getProspectApplicationSections() ?? [] as $prospectApplicationSection) {
            if ($prospectApplicationSection->getName() === ProspectApplicationSection::NAME_EMAIL_ADDRESS_CONFIRMATION) {
                return $prospectApplicationSection->getStatus() === ProspectApplicationSectionStatus::APPROVED;
            }
        }

        return false;
    }

    /**
     * Process a section
     *
     * @param string                           $name            The section's name
     * @param ProspectApplicationSectionStatus $status          The section's status
     * @param ?string                          $rejectionReason The rejction reason (default) (optional: null)
     *
     * @return void
     */
    public function processSection(
        string $name,
        ProspectApplicationSectionStatus $status,
        ?string $rejectionReason = null,
    ): void {
        //
        //  Check for an existing section
        //
        foreach ($this->getProspectApplicationSections() ?? [] as $prospectApplicationSection) {
            if ($prospectApplicationSection->getName() === $name) {
                $prospectApplicationSection->setStatus($status);
                $prospectApplicationSection->setApprovalTimestamp($status === ProspectApplicationSectionStatus::APPROVED ? new DateTime() : null);
                $prospectApplicationSection->setRejectionReason($status === ProspectApplicationSectionStatus::REJECTED ? $rejectionReason : null);
                $prospectApplicationSection->setRejectionTimestamp($status === ProspectApplicationSectionStatus::REJECTED ? new DateTime() : null);
                $prospectApplicationSection->setDateCreated(new DateTime());
                return;
            }
        }

        //
        //  If no existing section is found then add a new one
        //
        $this->setProspectApplicationSections(
            array_merge(
                $this->getProspectApplicationSections() ?? [],
                [
                    new ProspectApplicationSection(
                        name:               $name,
                        status:             $status,
                        approvalTimestamp:  $status === ProspectApplicationSectionStatus::APPROVED ? new DateTime() : null,
                        rejectionReason:    $status === ProspectApplicationSectionStatus::REJECTED ? $rejectionReason : null,
                        rejectionTimestamp: $status === ProspectApplicationSectionStatus::REJECTED ? new DateTime() : null,
                        dateCreated:        new DateTime(),
                        dateUpdated:        new DateTime(),
                    ),
                ],
            ),
        );
    }

    /**
     * Get the application data JSON for the prospect application
     *
     * @return array{
     *     data?: mixed,
     *     status?: mixed,
     * } The application data JSON for the prospect application
     */
    public function getApplicationData(): array
    {
        $applicationData = [
            'data'   => [
                'admin_id'                 => $this->getAdminId(),
                'campaign_id'              => $this->getCampaignId(),
                'date_created'             => $this->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                'dob'                      => $this->getDob()?->format('Y-m-d'),
                'documents'                => array_map(function ($item) {
                    return [
                        'date_created' => $item->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                        'md5_hash'     => $item->getMd5Hash(),
                        'type'         => $item->getType()?->value,
                        'url'          => $item->getUrl(),
                    ];
                }, $this->getApplicationDocuments() ?? []),
                'email'                    => $this->getEmail(),
                'email_confirm_code'       => $this->getEmailConfirmCode(),
                'fake_dob'                 => $this->getFakeDob()?->format('Y-m-d'),
                'first_name'               => $this->getFirstName(),
                'gender'                   => $this->getGender()?->value,
                'geography'                => $this->getGeography(),
                'id_number'                => $this->getIdNumber(),
                'images'                   => array_map(function ($item) {
                    return [
                        'date_created' => $item->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                        'md5_hash'     => $item->getMd5Hash(),
                        'type'         => $item->getType()?->value,
                        'url'          => $item->getUrl(),
                    ];
                }, $this->getApplicationImages() ?? []),
                'ip_address'               => $this->getIpAddress(),
                'issuing_authority'        => $this->getIssuingAuthority(),
                'language'                 => $this->getLanguage(),
                'last_name'                => $this->getLastName(),
                'preferred_contact_method' => $this->getPreferredContactMethod()?->value,
                'ref_affiliate_id'         => $this->getRefAffiliateId(),
                'ref_broadcaster_id'       => $this->getRefBroadcasterId(),
                'ref_model_id'             => $this->getRefModelId(),
                'ref_screen_name'          => $this->getRefScreenName(),
                'service'                  => $this->getService(),
                'stage_name'               => $this->getStageName(),
                'state'                    => $this->getState(),
                'studio_name'              => $this->getStudioName(),
                'studio_website'           => $this->getStudioWebsite(),
                'telephone'                => $this->getTelephone(),
                'type'                     => match ($this->getType()) {
                    ProspectApplicationType::PERFORMER => 'performer',
                    ProspectApplicationType::STUDIO    => 'studio',
                    default                            => null,
                },
                'type_of_identification'   => $this->getTypeOfIdentification() !== null ? strtolower($this->getTypeOfIdentification()->value) : null,
            ],
            'status' => [],
        ];

        foreach ($this->getProspectApplicationSections() ?? [] as $prospectApplicationSection) {
            if (
                $prospectApplicationSection->getName() !== null
                && !in_array($prospectApplicationSection->getName(), self::SECTIONS_TO_EXCLUDE_FROM_DATA_DIFF, true)
            ) {
                $applicationData['status'][$prospectApplicationSection->getName()] = [
                    'approval_timestamp'  => $prospectApplicationSection->getApprovalTimestamp()?->format(DateTime::ISO8601_EXPANDED),
                    'date_created'        => $prospectApplicationSection->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                    'date_updated'        => $prospectApplicationSection->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                    'rejection_reason'    => $prospectApplicationSection->getRejectionReason(),
                    'rejection_timestamp' => $prospectApplicationSection->getRejectionTimestamp()?->format(DateTime::ISO8601_EXPANDED),
                    'state'               => match ($prospectApplicationSection->getStatus()) {
                        ProspectApplicationSectionStatus::APPROVED => 'approved',
                        ProspectApplicationSectionStatus::PENDING  => 'pending',
                        ProspectApplicationSectionStatus::REJECTED => 'rejected',
                        default                                    => null,
                    },
                    'suggestion1'         => $prospectApplicationSection->getRejectSuggest1(),
                    'suggestion2'         => $prospectApplicationSection->getRejectSuggest2(),
                    'suggestion3'         => $prospectApplicationSection->getRejectSuggest3(),
                ];
            }
        }

        return $applicationData;
    }

    /**
     * Get an array of diffs that have occurred since the object was instantiated
     *
     * @return ModelDiff[] An array of diffs that have occurred since the object was instantiated
     */
    public function getDiffArray(): array
    {
        $diffArray = [];

        foreach (get_object_vars($this) as $name => $value) { // phpcs:ignore -- Hacky code for a hacky feature
            switch ($name) {
                case 'originalObject':
                case 'prospect':
                    // Don't compare the originalObject or prospect properties to themselves
                    break;

                default:
                    $getter = 'get' . ucwords($name);
                    if (method_exists($this, $getter)) {
                        $original = $this->originalObject->{$getter}();
                        $current  = $this->{$getter}();
                        if (serialize($original) !== serialize($current)) {
                            $diffArray[] = new ModelDiff(
                                method:     's' . substr($getter, 1), // Setter
                                parameters: [$current],
                            );
                        }
                    }
                    break;
            }
        }

        return $diffArray;
    }

    /**
     * Get the status of a section
     *
     * @param string $name The name of the section
     *
     * @return ?ProspectApplicationSectionStatus The status if found or null if not
     */
    public function getSectionStatus(string $name): ?ProspectApplicationSectionStatus
    {
        foreach ($this->getProspectApplicationSections() ?? [] as $prospectApplicationSection) {
            if ($prospectApplicationSection->getName() === $name) {
                return $prospectApplicationSection->getStatus();
            }
        }

        return null;
    }

    /**
     * Get the value of id
     *
     * @return ?int The value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param ?int $id The value for id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of applicationId
     *
     * @return ?string The value of applicationId
     */
    public function getApplicationId(): ?string
    {
        return $this->applicationId;
    }

    /**
     * Set the value of applicationId
     *
     * @param ?string $applicationId The value for applicationId
     *
     * @return void
     */
    public function setApplicationId(?string $applicationId): void
    {
        $this->applicationId = $applicationId;
    }

    /**
     * Get the value of applicationStatus
     *
     * @return ?ProspectApplicationStatus The value of applicationStatus
     */
    public function getApplicationStatus(): ?ProspectApplicationStatus
    {
        return $this->applicationStatus;
    }

    /**
     * Set the value of applicationStatus
     *
     * @param ?ProspectApplicationStatus $applicationStatus The value for applicationStatus
     *
     * @return void
     */
    public function setApplicationStatus(?ProspectApplicationStatus $applicationStatus): void
    {
        $this->applicationStatus = $applicationStatus;
    }

    /**
     * Get the value of adminId
     *
     * @return ?int The value of adminId
     */
    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    /**
     * Set the value of adminId
     *
     * @param ?int $adminId The value for adminId
     *
     * @return void
     */
    public function setAdminId(?int $adminId): void
    {
        $this->adminId = $adminId;
    }

    /**
     * Get the value of campaignId
     *
     * @return ?int The value of campaignId
     */
    public function getCampaignId(): ?int
    {
        return $this->campaignId;
    }

    /**
     * Set the value of campaignId
     *
     * @param ?int $campaignId The value for campaignId
     *
     * @return void
     */
    public function setCampaignId(?int $campaignId): void
    {
        $this->campaignId = $campaignId;
    }

    /**
     * Get the value of dob
     *
     * @return ?DateTimeInterface The value of dob
     */
    public function getDob(): ?DateTimeInterface
    {
        return $this->dob;
    }

    /**
     * Set the value of dob
     *
     * @param ?DateTimeInterface $dob The value for dob
     *
     * @return void
     */
    public function setDob(?DateTimeInterface $dob): void
    {
        $this->dob = $dob;
    }

    /**
     * Get the value of email
     *
     * @return ?string The value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param ?string $email The value for email
     *
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Get the value of emailConfirmCode
     *
     * @return ?string The value of emailConfirmCode
     */
    public function getEmailConfirmCode(): ?string
    {
        return $this->emailConfirmCode;
    }

    /**
     * Set the value of emailConfirmCode
     *
     * @param ?string $emailConfirmCode The value for emailConfirmCode
     *
     * @return void
     */
    public function setEmailConfirmCode(?string $emailConfirmCode): void
    {
        $this->emailConfirmCode = $emailConfirmCode;
    }

    /**
     * Get the value of fakeDob
     *
     * @return ?DateTimeInterface The value of fakeDob
     */
    public function getFakeDob(): ?DateTimeInterface
    {
        return $this->fakeDob;
    }

    /**
     * Set the value of fakeDob
     *
     * @param ?DateTimeInterface $fakeDob The value for fakeDob
     *
     * @return void
     */
    public function setFakeDob(?DateTimeInterface $fakeDob): void
    {
        $this->fakeDob = $fakeDob;
    }

    /**
     * Get the value of firstName
     *
     * @return ?string The value of firstName
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @param ?string $firstName The value for firstName
     *
     * @return void
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * Get the value of gender
     *
     * @return ?ProspectApplicationGender The value of gender
     */
    public function getGender(): ?ProspectApplicationGender
    {
        return $this->gender;
    }

    /**
     * Set the value of gender
     *
     * @param ?ProspectApplicationGender $gender The value for gender
     *
     * @return void
     */
    public function setGender(?ProspectApplicationGender $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * Get the value of geography
     *
     * @return ?string The value of geography
     */
    public function getGeography(): ?string
    {
        return $this->geography;
    }

    /**
     * Set the value of geography
     *
     * @param ?string $geography The value for geography
     *
     * @return void
     */
    public function setGeography(?string $geography): void
    {
        $this->geography = $geography;
    }

    /**
     * Get the value of idNumber
     *
     * @return ?string The value of idNumber
     */
    public function getIdNumber(): ?string
    {
        return $this->idNumber;
    }

    /**
     * Set the value of idNumber
     *
     * @param ?string $idNumber The value for idNumber
     *
     * @return void
     */
    public function setIdNumber(?string $idNumber): void
    {
        $this->idNumber = $idNumber;
    }

    /**
     * Get the value of ipAddress
     *
     * @return ?string The value of ipAddress
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * Set the value of ipAddress
     *
     * @param ?string $ipAddress The value for ipAddress
     *
     * @return void
     */
    public function setIpAddress(?string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get the value of issuingAuthority
     *
     * @return ?string The value of issuingAuthority
     */
    public function getIssuingAuthority(): ?string
    {
        return $this->issuingAuthority;
    }

    /**
     * Set the value of issuingAuthority
     *
     * @param ?string $issuingAuthority The value for issuingAuthority
     *
     * @return void
     */
    public function setIssuingAuthority(?string $issuingAuthority): void
    {
        $this->issuingAuthority = $issuingAuthority;
    }

    /**
     * Get the value of language
     *
     * @return ?string The value of language
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Set the value of language
     *
     * @param ?string $language The value for language
     *
     * @return void
     */
    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    /**
     * Get the value of lastName
     *
     * @return ?string The value of lastName
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @param ?string $lastName The value for lastName
     *
     * @return void
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * Get the value of preferredContactMethod
     *
     * @return ?ProspectApplicationContactMethod The value of preferredContactMethod
     */
    public function getPreferredContactMethod(): ?ProspectApplicationContactMethod
    {
        return $this->preferredContactMethod;
    }

    /**
     * Set the value of preferredContactMethod
     *
     * @param ?ProspectApplicationContactMethod $preferredContactMethod The value for preferredContactMethod
     *
     * @return void
     */
    public function setPreferredContactMethod(?ProspectApplicationContactMethod $preferredContactMethod): void
    {
        $this->preferredContactMethod = $preferredContactMethod;
    }

    /**
     * Get the value of refAffiliateId
     *
     * @return ?string The value of refAffiliateId
     */
    public function getRefAffiliateId(): ?string
    {
        return $this->refAffiliateId;
    }

    /**
     * Set the value of refAffiliateId
     *
     * @param ?string $refAffiliateId The value for refAffiliateId
     *
     * @return void
     */
    public function setRefAffiliateId(?string $refAffiliateId): void
    {
        $this->refAffiliateId = $refAffiliateId;
    }

    /**
     * Get the value of refBroadcasterId
     *
     * @return ?string The value of refBroadcasterId
     */
    public function getRefBroadcasterId(): ?string
    {
        return $this->refBroadcasterId;
    }

    /**
     * Set the value of refBroadcasterId
     *
     * @param ?string $refBroadcasterId The value for refBroadcasterId
     *
     * @return void
     */
    public function setRefBroadcasterId(?string $refBroadcasterId): void
    {
        $this->refBroadcasterId = $refBroadcasterId;
    }

    /**
     * Get the value of refModelId
     *
     * @return ?string The value of refModelId
     */
    public function getRefModelId(): ?string
    {
        return $this->refModelId;
    }

    /**
     * Set the value of refModelId
     *
     * @param ?string $refModelId The value for refModelId
     *
     * @return void
     */
    public function setRefModelId(?string $refModelId): void
    {
        $this->refModelId = $refModelId;
    }

    /**
     * Get the value of refScreenName
     *
     * @return ?string The value of refScreenName
     */
    public function getRefScreenName(): ?string
    {
        return $this->refScreenName;
    }

    /**
     * Set the value of refScreenName
     *
     * @param ?string $refScreenName The value for refScreenName
     *
     * @return void
     */
    public function setRefScreenName(?string $refScreenName): void
    {
        $this->refScreenName = $refScreenName;
    }

    /**
     * Get the value of service
     *
     * @return ?string The value of service
     */
    public function getService(): ?string
    {
        return $this->service;
    }

    /**
     * Set the value of service
     *
     * @param ?string $service The value for service
     *
     * @return void
     */
    public function setService(?string $service): void
    {
        $this->service = $service;
    }

    /**
     * Get the value of stageName
     *
     * @return ?string The value of stageName
     */
    public function getStageName(): ?string
    {
        return $this->stageName;
    }

    /**
     * Set the value of stageName
     *
     * @param ?string $stageName The value for stageName
     *
     * @return void
     */
    public function setStageName(?string $stageName): void
    {
        $this->stageName = $stageName;
    }

    /**
     * Get the value of state
     *
     * @return ?string The value of state
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Set the value of state
     *
     * @param ?string $state The value for state
     *
     * @return void
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * Get the value of studioName
     *
     * @return ?string The value of studioName
     */
    public function getStudioName(): ?string
    {
        return $this->studioName;
    }

    /**
     * Set the value of studioName
     *
     * @param ?string $studioName The value for studioName
     *
     * @return void
     */
    public function setStudioName(?string $studioName): void
    {
        $this->studioName = $studioName;
    }

    /**
     * Get the value of studioWebsite
     *
     * @return ?string The value of studioWebsite
     */
    public function getStudioWebsite(): ?string
    {
        return $this->studioWebsite;
    }

    /**
     * Set the value of studioWebsite
     *
     * @param ?string $studioWebsite The value for studioWebsite
     *
     * @return void
     */
    public function setStudioWebsite(?string $studioWebsite): void
    {
        $this->studioWebsite = $studioWebsite;
    }

    /**
     * Get the value of telephone
     *
     * @return ?string The value of telephone
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     *
     * @param ?string $telephone The value for telephone
     *
     * @return void
     */
    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * Get the value of type
     *
     * @return ?ProspectApplicationType The value of type
     */
    public function getType(): ?ProspectApplicationType
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param ?ProspectApplicationType $type The value for type
     *
     * @return void
     */
    public function setType(?ProspectApplicationType $type): void
    {
        $this->type = $type;
    }

    /**
     * Get the value of typeOfIdentification
     *
     * @return ?ProspectApplicationIdentificationMethod The value of typeOfIdentification
     */
    public function getTypeOfIdentification(): ?ProspectApplicationIdentificationMethod
    {
        return $this->typeOfIdentification;
    }

    /**
     * Set the value of typeOfIdentification
     *
     * @param ?ProspectApplicationIdentificationMethod $typeOfIdentification The value for typeOfIdentification
     *
     * @return void
     */
    public function setTypeOfIdentification(?ProspectApplicationIdentificationMethod $typeOfIdentification): void
    {
        $this->typeOfIdentification = $typeOfIdentification;
    }

    /**
     * Get the value of prospectApplicationSections
     *
     * @return ?ProspectApplicationSection[] The value of prospectApplicationSections
     */
    public function getProspectApplicationSections(): ?array
    {
        return $this->prospectApplicationSections;
    }

    /**
     * Set the value of prospectApplicationSections
     *
     * @param ?ProspectApplicationSection[] $prospectApplicationSections The value for prospectApplicationSections
     *
     * @return void
     */
    public function setProspectApplicationSections(?array $prospectApplicationSections): void
    {
        $this->prospectApplicationSections = $prospectApplicationSections;
    }

    /**
     * Get the value of prospectImages
     *
     * @return ?ProspectImage[] The value of prospectImages
     */
    public function getProspectImages(): ?array
    {
        return $this->prospectImages;
    }

    /**
     * Set the value of prospectImages
     *
     * @param ?ProspectImage[] $prospectImages   The value for prospectImages
     * @param bool             $overrideOriginal Whether or not to override the original object's value
     *
     * @return void
     */
    public function setProspectImages(?array $prospectImages, bool $overrideOriginal = false): void
    {
        $this->prospectImages = $prospectImages;

        if ($overrideOriginal) {
            $this->originalObject->setProspectImages($prospectImages);
        }
    }

    /**
     * Get the value of prospectStatusFinal
     *
     * @return ?string The value of prospectStatusFinal
     */
    public function getProspectStatusFinal(): ?string
    {
        return $this->prospectStatusFinal;
    }

    /**
     * Set the value of prospectStatusFinal
     *
     * @param ?string $prospectStatusFinal The value for prospectStatusFinal
     *
     * @return void
     */
    public function setProspectStatusFinal(?string $prospectStatusFinal): void
    {
        $this->prospectStatusFinal = $prospectStatusFinal;
    }

    /**
     * Get the value of dateCreated
     *
     * @return ?DateTimeInterface The value of dateCreated
     */
    public function getDateCreated(): ?DateTimeInterface
    {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @param ?DateTimeInterface $dateCreated The value for dateCreated
     *
     * @return void
     */
    public function setDateCreated(?DateTimeInterface $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Get the value of originalObject
     *
     * @return self The value of originalObject
     */
    public function getOriginalObject(): self
    {
        return $this->originalObject;
    }

    /**
     * Set the value of originalObject
     *
     * @param self $originalObject The value for originalObject
     *
     * @return void
     */
    public function setOriginalObject(self $originalObject): void
    {
        $this->originalObject = $originalObject;
    }

    /**
     * Get the application's name
     *
     * @return string The application's name
     */
    public function getName(): string
    {
        return trim(($this->getFirstName() ?? '') . ' ' . ($this->getLastName() ?? ''));
    }

    /**
     * Get the value of prospect
     *
     * @return ?Prospect The value of prospect
     */
    public function getProspect(): ?Prospect
    {
        return $this->prospect;
    }

    /**
     * Set the value of prospect
     *
     * @param ?Prospect $prospect         The value for prospect
     * @param bool      $overrideOriginal Whether or not to override the original object's value
     *
     * @return void
     */
    public function setProspect(?Prospect $prospect, bool $overrideOriginal = false): void
    {
        $this->prospect = $prospect;

        if ($overrideOriginal) {
            $this->originalObject->setProspect($prospect);
        }
    }

    /**
     * Get the value of applicationDocuments
     *
     * @return ?ProspectApplicationFile[] The value of applicationDocuments
     */
    public function getApplicationDocuments(): ?array
    {
        return $this->applicationDocuments;
    }

    /**
     * Set the value of applicationDocuments
     *
     * @param ?ProspectApplicationFile[] $applicationDocuments The value for applicationDocuments
     *
     * @return void
     */
    public function setApplicationDocuments(?array $applicationDocuments): void
    {
        $this->applicationDocuments = $applicationDocuments;
    }

    /**
     * Get the value of applicationImages
     *
     * @return ?ProspectApplicationFile[] The value of applicationImages
     */
    public function getApplicationImages(): ?array
    {
        return $this->applicationImages;
    }

    /**
     * Set the value of applicationImages
     *
     * @param ?ProspectApplicationFile[] $applicationImages The value for applicationImages
     *
     * @return void
     */
    public function setApplicationImages(?array $applicationImages): void
    {
        $this->applicationImages = $applicationImages;
    }

    /**
     * Copy all state from another ProspectApplication into this instance
     *
     * @param self $new The source application
     *
     * @return void
     */
    public function copyFrom(self $new): void
    {
        //
        //  Set the scalar, enum and date time properties
        //
        $this->setId($new->getId());
        $this->setApplicationId($new->getApplicationId());
        $this->setApplicationStatus($new->getApplicationStatus());
        $this->setAdminId($new->getAdminId());
        $this->setCampaignId($new->getCampaignId());
        $this->setDob($new->getDob() !== null ? clone $new->getDob() : null);
        $this->setEmail($new->getEmail());
        $this->setEmailConfirmCode($new->getEmailConfirmCode());
        $this->setFakeDob($new->getFakeDob() !== null ? clone $new->getFakeDob() : null);
        $this->setFirstName($new->getFirstName());
        $this->setGender($new->getGender());
        $this->setGeography($new->getGeography());
        $this->setIdNumber($new->getIdNumber());
        $this->setIpAddress($new->getIpAddress());
        $this->setIssuingAuthority($new->getIssuingAuthority());
        $this->setLanguage($new->getLanguage());
        $this->setLastName($new->getLastName());
        $this->setPreferredContactMethod($new->getPreferredContactMethod());
        $this->setRefAffiliateId($new->getRefAffiliateId());
        $this->setRefBroadcasterId($new->getRefBroadcasterId());
        $this->setRefModelId($new->getRefModelId());
        $this->setRefScreenName($new->getRefScreenName());
        $this->setService($new->getService());
        $this->setStageName($new->getStageName());
        $this->setState($new->getState());
        $this->setStudioName($new->getStudioName());
        $this->setStudioWebsite($new->getStudioWebsite());
        $this->setTelephone($new->getTelephone());
        $this->setType($new->getType());
        $this->setTypeOfIdentification($new->getTypeOfIdentification());
        $this->setProspectStatusFinal($new->getProspectStatusFinal());
        $this->setDateCreated($new->getDateCreated() !== null ? clone $new->getDateCreated() : null);

        //
        //  Deep-copy nested arrays
        //
        $sections = null;
        if ($new->getProspectApplicationSections() !== null) {
            $sections = [];
            foreach ($new->getProspectApplicationSections() as $section) {
                $sections[] = clone $section;
            }
        }
        $this->setProspectApplicationSections($sections);

        $prospectImages = null;
        if ($new->getProspectImages() !== null) {
            $prospectImages = [];
            foreach ($new->getProspectImages() as $img) {
                $prospectImages[] = clone $img;
            }
        }
        $this->setProspectImages($prospectImages);

        $documents = null;
        if ($new->getApplicationDocuments() !== null) {
            $documents = [];
            foreach ($new->getApplicationDocuments() as $doc) {
                $documents[] = clone $doc;
            }
        }
        $this->setApplicationDocuments($documents);

        $appImages = null;
        if ($new->getApplicationImages() !== null) {
            $appImages = [];
            foreach ($new->getApplicationImages() as $img) {
                $appImages[] = clone $img;
            }
        }
        $this->setApplicationImages($appImages);

        //
        //  Reset originalObject so further diffs are calculated from this new baseline
        //
        $snapshot = clone $this;

        $snapshotSections = [];
        foreach ($this->getProspectApplicationSections() ?? [] as $section) {
            $snapshotSections[] = clone $section;
        }
        $snapshot->setProspectApplicationSections($snapshotSections);

        $snapshotProspectImages = [];
        foreach ($this->getProspectImages() ?? [] as $image) {
            $snapshotProspectImages[] = clone $image;
        }
        $snapshot->setProspectImages($snapshotProspectImages);

        $snapshotApplicationDocuments = [];
        foreach ($this->getApplicationDocuments() ?? [] as $document) {
            $snapshotApplicationDocuments[] = clone $document;
        }
        $snapshot->setApplicationDocuments($snapshotApplicationDocuments);

        $snapshotApplicationImages = [];
        foreach ($this->getApplicationImages() ?? [] as $image) {
            $snapshotApplicationImages[] = clone $image;
        }
        $snapshot->setApplicationImages($snapshotApplicationImages);

        $snapshot->setProspect($this->getProspect() !== null ? clone $this->getProspect() : null);

        $this->setOriginalObject($snapshot);
    }

    /**
     * Get a section by name
     *
     * @param string $name The section's name
     *
     * @throws InvalidArgumentException When no section is found with that name
     *
     * @return ProspectApplicationSection The section
     */
    private function getSection(string $name): ProspectApplicationSection
    {
        //
        //  Check for an existing section
        //
        foreach ($this->getProspectApplicationSections() ?? [] as $prospectApplicationSection) {
            if ($prospectApplicationSection->getName() === $name) {
                return $prospectApplicationSection;
            }
        }

        //
        //  Throw an exception is none is found
        //
        throw new InvalidArgumentException('The application does not have a section called `' . $name . '`', ExceptionCode::PROSPECT_APPLICATION_SECTION_NOT_FOUND->value);
    }
}
