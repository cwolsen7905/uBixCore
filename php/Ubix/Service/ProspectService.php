<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use TypeError;
use Ubix\DataTransferObject\Document2257UbixApiRequestData;
use Ubix\DataTransferObject\GeolocationLookup;
use Ubix\DataTransferObject\ImageCropInstructions;
use Ubix\DataTransferObject\ProspectApplicationSubmissionData;
use Ubix\Enum\AdminUser\AdminUserStatus;
use Ubix\Enum\Broadcaster\BroadcasterStatus;
use Ubix\Enum\DuplicateProspect\DuplicateProspectStatus;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Prospect\ProspectStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationContactMethod;
use Ubix\Enum\ProspectApplication\ProspectApplicationGender;
use Ubix\Enum\ProspectApplication\ProspectApplicationIdentificationMethod;
use Ubix\Enum\ProspectApplication\ProspectApplicationSectionStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationType;
use Ubix\Enum\ProspectDocument\ProspectDocumentType;
use Ubix\Enum\ProspectImage\ProspectImageType;
use Ubix\Enum\Studio\StudioStatus;
use Ubix\Model\AdminUser;
use Ubix\Model\DuplicateProspect;
use Ubix\Model\PerformerNameSuggestion;
use Ubix\Model\PlatformUser;
use Ubix\Model\Prospect;
use Ubix\Model\ProspectApplication;
use Ubix\Model\ProspectApplicationSection;
use Ubix\Model\Studio;
use Ubix\Repository\DuplicateProspect\DuplicateProspectWriterInterface as DuplicateProspectWriter;
use Ubix\Repository\PerformerNameSuggestion\PerformerNameSuggestionWriterInterface as PerformerNameSuggestionWriter;
use Ubix\Repository\Prospect\ProspectReaderInterface as ProspectReader;
use Ubix\Repository\Prospect\ProspectWriterInterface as ProspectWriter;
use Ubix\Repository\ProspectApplication\ProspectApplicationReaderInterface as ProspectApplicationReader;
use Ubix\Repository\ProspectApplication\ProspectApplicationWriterInterface as ProspectApplicationWriter;
use Ubix\Repository\ProspectImage\ProspectImageReaderInterface as ProspectImageReader;
use Ubix\Repository\SoloAcquisitionCampaign\SoloAcquisitionCampaignReaderInterface as SoloAcquisitionCampaignReader;
use Ubix\Service\Blob\S3BlobService;
use Ubix\Service\Geolocation\GeolocationServiceInterface as GeolocationService;

/**
 * Service to handle prospects
 *
 * @see \Ubix\Tests\Service\ProspectServiceTest PHPUnit test case
 */
final class ProspectService
{
    public const CDN77_BUCKET_NAME = 'model-signup';

    private const APPLICATION_ID_SALT = 'njhcmu';

    private const BLOCKED_IP_ADDRESSES = [ // NOT_IMPLEMENTED: why are ProspectService::BLOCKED_IP_ADDRESSES different than NetworkingService::BLOCKED_IP_ADDRESSES?
        '77.40.107.18',
        '77.40.60.190',
        '77.40.82.196',
        '77.40.95.200',
        '77.40.67.232',
        '77.40.106.199',
        '46.250.82.202',
        '77.40.85.223',
        '77.40.66.239',
        '115.69.245.202',
        '112.79.202.10',
        '115.69.244.13',
        '5.188.210.3',
        '111.119.185.60',
        '181.33.130.60',
        '46.72.115.202',
    ];

    private const BLOCKED_TRACKERS = ['com'];

    private const CAMPAIGN_ID_INVALID = 10052;

    private const PHONE_NUMBER_MINIMUM_DIGITS = 7;

    private const SECRET_PHRASE = '.gnorw si meht fo eno taht dnif lliw uoY .sesimerp ruoy kcehc ,noitcidartnoc a gnicaf era uoy taht kniht uoy revenehW .tsixe ton od snoitcidartnoC || b1M2Irbin2R8gXBgejlu || Llrn39rkUavCPOSmU0jc';

    /**
     * Constructor
     *
     * @param Logger                        $logger                              Logger
     * @param HttpClient                    $httpClient                          HTTP client
     * @param DuplicateProspectWriter       $duplicateProspectWriter             Duplicate prospect writer
     * @param PerformerNameSuggestionWriter $performerNameSuggestionWriter       Performer name suggestion writer
     * @param ProspectApplicationReader     $prospectApplicationReader           Prospect application reader
     * @param ProspectApplicationWriter     $prospectApplicationWriter           Prospect application writer
     * @param ProspectImageReader           $prospectImageReader                 Prospect image reader
     * @param ProspectReader                $prospectReader                      Prospect reader
     * @param ProspectWriter                $prospectWriter                      Prospect writer
     * @param SoloAcquisitionCampaignReader $soloAcquisitionCampaignReader       Solo acquisition campaign reader
     * @param AdminUserService              $adminUserService                    Admin user service
     * @param BroadcasterService            $broadcasterService                  Broadcaster service
     * @param Base64Service                 $base64Service                       Base64 service
     * @param Document2257Service           $document2257Service                 Document 2257 service
     * @param GeolocationService            $geolocationService                  Geolocation service
     * @param ImageService                  $imageService                        Image service
     * @param JsonService                   $jsonService                         Json service
     * @param LocationService               $locationService                     Location service
     * @param PerformerService              $performerService                    Performer service
     * @param PlatformUserService           $platformUserService                 Platform user service
     * @param RecaptchaService              $recaptchaService                    ReCAPTCHA service
     * @param RequestFactory                $requestFactory                      Request factory
     * @param S3BlobService                 $s3BlobService                       S3 blob service
     * @param StreamFactory                 $streamFactory                       Stream factory
     * @param StudioService                 $studioService                       Studio service
     * @param UuidService                   $uuidService                         UUID service
     * @param string                        $legacyWebServiceProtocolAndHostname Legacy web service protocol and hostname
     * @param string                        $legacyWebServiceBearerToken         Legacy web service bearer token
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private HttpClient $httpClient,
        private DuplicateProspectWriter $duplicateProspectWriter,
        private PerformerNameSuggestionWriter $performerNameSuggestionWriter,
        private ProspectApplicationReader $prospectApplicationReader,
        private ProspectApplicationWriter $prospectApplicationWriter,
        private ProspectImageReader $prospectImageReader,
        private ProspectReader $prospectReader,
        private ProspectWriter $prospectWriter,
        private SoloAcquisitionCampaignReader $soloAcquisitionCampaignReader,
        private AdminUserService $adminUserService,
        private BroadcasterService $broadcasterService,
        private Base64Service $base64Service,
        private Document2257Service $document2257Service,
        private GeolocationService $geolocationService,
        private ImageService $imageService,
        private JsonService $jsonService,
        private LocationService $locationService,
        private PerformerService $performerService,
        private PlatformUserService $platformUserService,
        private RecaptchaService $recaptchaService,
        private RequestFactory $requestFactory,
        private S3BlobService $s3BlobService,
        private StreamFactory $streamFactory,
        private StudioService $studioService,
        private UuidService $uuidService,
        private string $legacyWebServiceProtocolAndHostname,
        private string $legacyWebServiceBearerToken,
    ) {
    }

    /**
     * Get a prospect by ID
     *
     * @param int $id The prospect's ID
     *
     * @throws Exception If the prospect isn't found
     *
     * @return Prospect The prospect
     */
    public function getById(int $id): Prospect
    {
        $prospects = $this->prospectReader->getById($id);
        if (count($prospects) > 0) {
            return $prospects[0];
        } else {
            throw new Exception(
                'No prospect found with the ID `' . $id . '`',
                ExceptionCode::NO_MATCHES_FOUND_FOR_PROSPECT->value,
            );
        }
    }

    /**
     * Get prospects by stage name
     *
     * @param string $stageName The prospect's stage name
     *
     * @return Prospect[] An array of prospects with a matching stage name
     */
    public function getByStageName(string $stageName): array
    {
        return $this->prospectReader->getByStageName($stageName);
    }

    /**
     * Get prospects by partial match
     *
     * @param ?string $name       The prospect's name
     * @param ?string $studioName The prospect's studio name
     *
     * @return Prospect[] An array of partially matching prospects
     */
    public function getPartialMatches(?string $name, ?string $studioName): array
    {
        return $this->prospectReader->getPartialMatches($name, $studioName);
    }

    /**
     * Get matching prospects
     *
     * @param string             $emailAddress      The prospect's email address (optional) (default: null)
     * @param ?string            $phoneNumber       The prospect's phone number (optional) (default: null)
     * @param ?DateTimeInterface $dateOfBirth       The prospect's date of birthday (optional) (default: null)
     * @param ?string            $name              The prospect's name (optional) (default: null)
     * @param bool               $includeAdminEmail Whether to include admin email addresses (optional) (default: true)
     *
     * @return Prospect[] An array of matching prospects
     */
    public function getMatching(
        ?string $emailAddress = null,
        ?string $phoneNumber = null,
        ?DateTimeInterface $dateOfBirth = null,
        ?string $name = null,
        bool $includeAdminEmail = true,
    ): array {
        return $this->prospectReader->get($emailAddress, $phoneNumber, $dateOfBirth, $name, $includeAdminEmail);
    }

    /**
     * Get a prospect application by ID
     *
     * @param int $id The prospect application's ID
     *
     * @throws Exception If the prospect application isn't found
     *
     * @return ProspectApplication The prospect application
     */
    public function getApplicationById(int $id): ProspectApplication
    {
        $prospectApplications = $this->prospectApplicationReader->getById($id);
        if (count($prospectApplications) > 0) {
            $this->loadProspect($prospectApplications[0], true);
            return $prospectApplications[0];
        } else {
            throw new Exception(
                'No prospect application found with the ID `' . $id . '`',
                ExceptionCode::NO_MATCHES_FOUND_FOR_PROSPECT_APPLICATION_ID->value,
            );
        }
    }

    /**
     * Generate a document 2257 for a prospect application
     *
     * @param Request             $request     PSR request
     * @param ProspectApplication $application The prospect application
     *
     * @return void
     */
    public function generateDocument2257(Request $request, ProspectApplication $application): void
    {
        $application->validateDocumentUploadEligibility();

        //
        //  Generate the document 2257 image
        //
        $image = $this->getDocument2257Image(
            application: $application,
            signature:   $application->getName(),
        );

        //
        //  Generate a UUID, the S3 path and the document URL
        //
        $uuid        = $this->uuidService->getUuid4();
        $s3Path      = $this->getS3Path(ProspectDocumentType::DOCUMENT_2257, $uuid);
        $documentUrl = $this->transformUrl($request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . ($request->getUri()->getPort() !== null ? ':' . $request->getUri()->getPort() : '') . '/document/2257/image/' . $uuid);

        //
        //  Upload the image to S3
        //
        $this->s3BlobService->put($s3Path, $image);

        //
        //  Update the application and save it
        //
        try {
            $application->getFile(ProspectDocumentType::DOCUMENT_2257);
            $application->removeFile(ProspectDocumentType::DOCUMENT_2257);
        } catch (Exception $e) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            // The file doesn't exist, so we don't need to remove it
        }

        $application->addFile(ProspectDocumentType::DOCUMENT_2257, $documentUrl, md5($image));
        $application->processSection(
            'document_2257',
            ProspectApplicationSectionStatus::PENDING,
        );
        $application->setAdminId(0);
        $application->calculateStatus();

        $this->prospectApplicationWriter->save($application);
    }

    /**
     * Approve a document 2257 for a prospect application
     *
     * @param ProspectApplication $application The prospect application
     *
     * @return void
     */
    public function approveDocument2257(ProspectApplication $application): void
    {
        $application->getFile(ProspectDocumentType::DOCUMENT_2257); // We do this to induce an exception in the event the document doesn't exist

        //
        //  Update the application and save it
        //
        $application->processSection(
            'document_2257',
            ProspectApplicationSectionStatus::APPROVED,
        );
        $application->setAdminId(0);
        $application->calculateStatus();

        $this->prospectApplicationWriter->save($application);
    }

    /**
     * Upload an image for a prospect application
     *
     * @param Request               $request               PSR request
     * @param ProspectApplication   $application           The prospect application
     * @param ProspectImageType     $imageType             The image type
     * @param string                $imageBinary           The image binary
     * @param ImageCropInstructions $imageCropInstructions The image crop instructions
     *
     * @throws InvalidArgumentException If the image type has already been uploaded for this application
     *
     * @return void
     */
    public function uploadImage(
        Request $request,
        ProspectApplication $application,
        ProspectImageType $imageType,
        string $imageBinary,
        ImageCropInstructions $imageCropInstructions,
    ): void {
        //
        //  Validate the image
        //
        if (getimagesizefromstring($imageBinary) === false) {
            throw new InvalidArgumentException('The image submitted is not valid', ExceptionCode::PROSPECT_APPLICATION_IMAGE_INVALID->value);
        }

        //
        //  Crop the image
        //
        $imageBinary = $this->imageService->cropImage($imageBinary, $imageCropInstructions);

        //
        //  Check if the image type has already been uploaded for this application
        //
        try {
            $existing = $application->getFile($imageType);
        } catch (Exception $e) {
            $existing = null;
        }

        if ($existing !== null && $application->getSectionStatus($imageType->value) !== ProspectApplicationSectionStatus::REJECTED) {
            throw new InvalidArgumentException(
                'The image of type `' . $imageType->value . '` has already been uploaded for this application',
                ExceptionCode::PROSPECT_APPLICATION_IMAGE_TYPE_EXISTS->value,
            );
        }

        //
        //  Generate a UUID, the S3 path and the document URL
        //
        $uuid     = $this->uuidService->getUuid4();
        $s3Path   = $this->getS3Path(ProspectImageType::PHOTO_ID, $uuid);
        $imageUrl = $this->transformUrl($request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . ($request->getUri()->getPort() !== null ? ':' . $request->getUri()->getPort() : '') . '/image/' . $uuid);

        //
        //  Upload the image to S3
        //
        $this->s3BlobService->put($s3Path, $imageBinary);

        //
        //  Update the application and save it
        //
        try {
            $application->getFile($imageType);
            $application->removeFile($imageType);
        } catch (Exception $e) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            // The file doesn't exist, so we don't need to remove it
        }

        $application->addFile($imageType, $imageUrl, md5($imageBinary));
        $application->processSection(
            $imageType->value,
            ProspectApplicationSectionStatus::PENDING,
        );
        $application->setAdminId(0);
        $application->calculateStatus();

        $this->prospectApplicationWriter->save($application);
    }

    /**
     * Generate a broadcaster agreement for a prospect application
     *
     * @param Request             $request     PSR request
     * @param ProspectApplication $application The prospect application
     *
     * @return void
     */
    public function generateBcAgreement(Request $request, ProspectApplication $application): void
    {
        $application->validateDocumentUploadEligibility();

        //
        //  Generate the broadcaster agreement image
        //
        $image = $this->base64Service->decode('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'); // This is an image of the flirt4free logo (a placeholder)

        //
        //  Generate a UUID, the S3 path and the document URL
        //
        $uuid        = $this->uuidService->getUuid4();
        $s3Path      = $this->getS3Path(ProspectDocumentType::BROADCASTER_AGREEMENT, $uuid);
        $documentUrl = $this->transformUrl($request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . ($request->getUri()->getPort() !== null ? ':' . $request->getUri()->getPort() : '') . '/document/bc-agreement/image/' . $uuid);

        //
        //  Upload the image to S3
        //
        $this->s3BlobService->put($s3Path, $image);

        //
        //  Update the application and save it
        //
        try {
            $application->getFile(ProspectDocumentType::BROADCASTER_AGREEMENT);
            $application->removeFile(ProspectDocumentType::BROADCASTER_AGREEMENT);
        } catch (Exception $e) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            // The file doesn't exist, so we don't need to remove it
        }

        $application->addFile(ProspectDocumentType::BROADCASTER_AGREEMENT, $documentUrl, md5($image));
        $application->processSection(
            'bc_agreement',
            ProspectApplicationSectionStatus::PENDING,
        );
        $application->setAdminId(0);
        $application->calculateStatus();

        $this->prospectApplicationWriter->save($application);
    }

    /**
     * Approve a broadcaster agreement for a prospect application
     *
     * @param ProspectApplication $application The prospect application
     *
     * @return void
     */
    public function approveBcAgreement(ProspectApplication $application): void
    {
        $application->getFile(ProspectDocumentType::BROADCASTER_AGREEMENT); // We do this to induce an exception in the event the document doesn't exist

        //
        //  Update the application and save it
        //
        $application->processSection(
            'bc_agreement',
            ProspectApplicationSectionStatus::APPROVED,
        );
        $application->setAdminId(0);
        $application->calculateStatus();

        $this->prospectApplicationWriter->save($application);
    }

    /**
     * Get a prospect application by application ID
     *
     * @param string $applicationId The prospect application's application ID
     * @param int    $id            The prospect application's ID (optional) (default: null)
     *
     * @throws Exception If the prospect application isn't found
     *
     * @return ProspectApplication The prospect application
     */
    public function getApplicationByApplicationId(string $applicationId, ?int $id = null): ProspectApplication
    {
        $prospectApplications = $this->prospectApplicationReader->getByApplicationId(
            $applicationId,
            $id,
        );
        if (count($prospectApplications) > 0) {
            $this->loadProspect($prospectApplications[0], true);
            return $prospectApplications[0];
        } else {
            throw new Exception(
                'No prospect application found with the application ID `' . $applicationId . '`' . ($id !== null ? ' and revision `' . $id . '`' : ''),
                ExceptionCode::NO_MATCHES_FOUND_FOR_PROSPECT_APPLICATION_APPLICATION_ID->value,
            );
        }
    }

    /**
     * Return an S3 path for a prospect application file
     *
     * @param ProspectDocumentType|ProspectImageType $type     The document or image type
     * @param string                                 $fileUuid The file UUID
     *
     * @return string The S3 path
     */
    public function getS3Path(ProspectDocumentType|ProspectImageType $type, string $fileUuid): string
    {
        return self::CDN77_BUCKET_NAME . match ($type) {
            ProspectDocumentType::BROADCASTER_AGREEMENT => '/documents/bc-agreement/',
            ProspectDocumentType::DOCUMENT_2257         => '/documents/2257/',
            ProspectImageType::PHOTO_ID_FRONT            => '/images/',
            ProspectImageType::PHOTO_ID                 => '/images/',
            ProspectImageType::PHOTO_ID_BACK             => '/images/',
            ProspectImageType::PHOTO_ID_FACE             => '/images/',
            ProspectImageType::IMAGES                  => '/images/',
        } . $fileUuid . '.jpg';
    }

    /**
     * XXX
     *
     * @param ?ProspectApplication $existingApplication    XXX
     * @param string               $type                   XXX
     * @param string               $firstName              XXX
     * @param string               $lastName               XXX
     * @param string               $studioName             XXX
     * @param string               $studioWebsite          XXX
     * @param string               $emailAddress           XXX
     * @param string               $phoneNumber            XXX
     * @param string               $preferredContactMethod XXX
     * @param string               $countryCode            XXX
     * @param string               $stateCode              XXX
     * @param string               $password               XXX
     * @param bool                 $omitPassword           XXX
     * @param string               $dateOfBirth            XXX
     * @param string               $gender                 XXX
     * @param string               $tracker                XXX
     * @param string               $campaignId             XXX
     * @param string               $refAffiliateId         XXX
     * @param string               $refModelId             XXX
     * @param string               $refBroadcasterId       XXX
     * @param string               $refScreenName          XXX
     * @param string               $language               XXX
     * @param string               $ipAddress              XXX
     * @param string               $captcha                XXX
     * @param string               $userAgent              XXX
     *
     * @throws Exception If an existing duplicate account is detected
     * @throws InvalidArgumentException If an invalid parameter is passed in
     *
     * @return ProspectApplication The prospect application that was submitted
     */
    public function submit(
        ?ProspectApplication $existingApplication,
        string $type,
        string $firstName,
        string $lastName,
        string $studioName,
        string $studioWebsite,
        string $emailAddress,
        string $phoneNumber,
        string $preferredContactMethod,
        string $countryCode,
        string $stateCode,
        string $password,
        bool $omitPassword,
        string $dateOfBirth,
        string $gender,
        string $tracker,
        string $campaignId,
        string $refAffiliateId,
        string $refModelId,
        string $refBroadcasterId,
        string $refScreenName,
        string $language,
        string $ipAddress,
        string $captcha,
        string $userAgent,
    ): ProspectApplication {
        //
        //  Validate parameters
        //
        if ($existingApplication === null && !$this->recaptchaService->isValid($captcha)) {
            throw new InvalidArgumentException('Invalid recaptcha.', ExceptionCode::INVALID_RECAPTCHA->value);
        }

        if ($firstName === '') {
            throw new InvalidArgumentException('Please fill out your first name.', ExceptionCode::MISSING_FIRST_NAME_FOR_PROSPECT_APPLICATION->value);
        }

        if ($lastName === '') {
            throw new InvalidArgumentException('Please fill out your last name.', ExceptionCode::MISSING_LAST_NAME_FOR_PROSPECT_APPLICATION->value);
        }

        if ($emailAddress === '' || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please provide a valid email address.', ExceptionCode::MISSING_EMAIL_ADDRESS_FOR_PROSPECT_APPLICATION->value);
        }

        if ($phoneNumber === '') {
            throw new InvalidArgumentException('Please fill out your phone number.', ExceptionCode::MISSING_PHONE_NUMBER_FOR_PROSPECT_APPLICATION->value);
        } elseif (!preg_match('/^[0-9+\-() ]+$/', $phoneNumber)) {
            throw new InvalidArgumentException(
                'Phone number contains invalid characters. Only numbers, spaces, parentheses, hyphens and plus signs are allowed.',
                ExceptionCode::INVALID_PHONE_NUMBER_FOR_PROSPECT_APPLICATION->value,
            );
        } else {
            $digitCount = preg_match_all('/\d/', $phoneNumber);
            if ($digitCount !== false && $digitCount < self::PHONE_NUMBER_MINIMUM_DIGITS) {
                throw new InvalidArgumentException(
                    'Phone number must contain at least ' . self::PHONE_NUMBER_MINIMUM_DIGITS . ' digits.',
                    ExceptionCode::INVALID_PHONE_NUMBER_FOR_PROSPECT_APPLICATION->value,
                );
            }
        }

        if (ProspectApplicationContactMethod::tryFrom($preferredContactMethod) === null) {
            throw new InvalidArgumentException('Preferred contact method can be one of: phone, email, text.', ExceptionCode::INVALID_PREFERRED_CONTACT_METHOD_FOR_PROSPECT_APPLICATION->value);
        }

        try {
            $this->locationService->getCountryByIso31661Alpha2($countryCode);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Country code must be a valid ISO code.', ExceptionCode::INVALID_COUNTRY_CODE_FOR_PROSPECT_APPLICATION->value);
        }

        if ($countryCode === 'US') {
            try {
                $this->locationService->getStateByIso31662('US-' . $stateCode);
            } catch (Exception $e) {
                throw new InvalidArgumentException('When country is US, you must provide a valid state ISO code.', ExceptionCode::INVALID_STATE_CODE_FOR_PROSPECT_APPLICATION->value);
            }
        }

        switch ($type) {
            case 'performer':
                if (!$omitPassword && $password === '') {
                    throw new InvalidArgumentException('Please fill out your password.', ExceptionCode::MISSING_PASSWORD_FOR_PROSPECT_APPLICATION->value);
                }

                try {
                    new DateTime($dateOfBirth);
                } catch (Exception $e) {
                    throw new InvalidArgumentException('Please provide a valid date of birth.', ExceptionCode::INVALID_DATE_OF_BIRTH_FOR_PROSPECT_APPLICATION->value);
                }

                if (ProspectApplicationGender::tryFrom($gender) === null) {
                    throw new InvalidArgumentException('Gender must be either male, female or trans.', ExceptionCode::INVALID_GENDER_FOR_PROSPECT_APPLICATION->value);
                }

                break;

            case 'studio':
                if ($studioName === '') {
                    throw new InvalidArgumentException('Please fill out studio name.', ExceptionCode::MISSING_STUDIO_NAME_FOR_PROSPECT_APPLICATION->value);
                }
                break;

            default:
                throw new InvalidArgumentException('Type must be either performer or studio.', ExceptionCode::INVALID_TYPE_FOR_PROSPECT_APPLICATION_SUBMIT->value);
        }

        //
        //  Ensure the IP address is not blocked
        //
        if (in_array($ipAddress, self::BLOCKED_IP_ADDRESSES, true)) {
            throw new InvalidArgumentException('Your application cannot be processed at this time.', ExceptionCode::BLOCKED_IP_ADDRESS->value);
        }

        //
        //  Ensure the tracker is not blocked
        //
        if (in_array($tracker, self::BLOCKED_TRACKERS, true)) {
            throw new InvalidArgumentException('Your application cannot be processed at this time.', ExceptionCode::BLOCKED_TRACKER->value);
        }

        //
        //  Reset referral data if the tracker indciates an already paid campaign
        //
        $trackerPrefixes = ['google_', 'backpage', 'bing', 'sexyjobs', 'craigslist'];
        foreach ($trackerPrefixes as $trackerPrefix) {
            if (strpos($tracker, $trackerPrefix) === 0) {
                $refAffiliateId   = 0;
                $refModelId       = 0;
                $refBroadcasterId = 0;
                $refScreenName    = '';
            }
        }

        //
        //  Check to see if the provided campaign_id contains a valid campaign id
        //
        if ($campaignId !== '' && is_numeric($campaignId)) {
            $count = $this->soloAcquisitionCampaignReader->getCountById((int)$campaignId);
            if ($count === 0) {
                $campaignId = self::CAMPAIGN_ID_INVALID;
            }

            //
            //  Since this user is being attributed to a paid campaign ensure any other referral data is cleared to prevent double paying
            //
            $refAffiliateId   = 0;
            $refModelId       = 0;
            $refBroadcasterId = 0;
            $refScreenName    = '';
        }

        //
        //  Strip tags from all values
        //
        $type                   = strip_tags($type);
        $firstName              = strip_tags($firstName);
        $lastName               = strip_tags($lastName);
        $studioName             = strip_tags($studioName);
        $studioWebsite          = strip_tags($studioWebsite);
        $emailAddress           = strip_tags($emailAddress);
        $phoneNumber            = strip_tags($phoneNumber);
        $preferredContactMethod = strip_tags($preferredContactMethod);
        $countryCode            = strip_tags($countryCode);
        $stateCode              = strip_tags($stateCode);
        $password               = strip_tags($password);
        $dateOfBirth            = strip_tags($dateOfBirth);
        $gender                 = strip_tags($gender);
        $tracker                = strip_tags($tracker);
        $campaignId             = strip_tags((string)$campaignId);
        $refAffiliateId         = strip_tags((string)$refAffiliateId);
        $refModelId             = strip_tags((string)$refModelId);
        $refBroadcasterId       = strip_tags((string)$refBroadcasterId);
        $refScreenName          = strip_tags($refScreenName);
        $language               = strip_tags($language);
        $ipAddress              = strip_tags($ipAddress);

        //
        //  Trim values
        //
        $phoneNumber  = trim($phoneNumber);
        $emailAddress = trim($emailAddress);
        $studioName   = trim($studioName);

        //
        //  Determine service from gender
        //
        $service = ProspectApplicationGender::tryFrom($gender) === ProspectApplicationGender::MALE ? 'guys' : 'girls';

        //
        //  Validate referring model ID
        //
        if (!empty($refModelId) && is_numeric($refModelId)) {
            try {
                $performer = $this->performerService->getById((int)$refModelId);
                if ($performer->getActive() !== 'Y') {
                    $refModelId = 0;
                }
            } catch (Exception $e) {
                $refModelId = 0;
            }
        } else {
            $refModelId = 0;
        }

        //
        //  Validate referring broadcaster ID
        //
        if (!empty($refBroadcasterId) && is_numeric($refBroadcasterId)) {
            try {
                $broadcaster = $this->broadcasterService->getById((int)$refBroadcasterId);
                if ($broadcaster->getStatus() !== BroadcasterStatus::ENABLED || $broadcaster->getCanRecruit() !== 'Y') {
                    $refBroadcasterId = 0;
                }
            } catch (Exception $e) {
                $refBroadcasterId = 0;
            }
        } else {
            $refBroadcasterId = 0;
        }

        //
        //  Validate referring screen name
        //
        $platformUser = null;
        if (!empty($refScreenName)) {
            try {
                $platformUser = $this->platformUserService->getByScreenName($refScreenName);
                if ($platformUser->getStatus() !== PlatformUser::STATUS_ACTIVE) {
                    $refScreenName = '';
                }
            } catch (Exception $e) {
                $refScreenName = '';
            }
        } else {
            $refScreenName = '';
        }

        //
        //  Perform a geolocation lookup
        //
        $geolocationLookup = $this->geolocationService->getLookupByIpAddress($ipAddress);

        //
        //  Verify the date of birth
        //
        try {
            $dateOfBirth = new DateTime($dateOfBirth);

            if ($type !== 'studio' && $dateOfBirth > new DateTime('-18 years')) {
                throw new InvalidArgumentException('You must be at least 18 years of age or older.', ExceptionCode::DATE_OF_BIRTH_IS_UNDERAGE->value);
            }
        } catch (Exception $e) {
            throw new InvalidArgumentException('The date of birth is not valid.', ExceptionCode::DATE_OF_BIRTH_IS_NOT_VALID->value, $e);
        }

        //
        //  Verify the password
        //
        if (!$omitPassword && !$this->passwordIsValid($password)) {
            throw new InvalidArgumentException('Password does not satisfy all of the requirements.', ExceptionCode::INVALID_PASSWORD_FOR_PROSPECT_APPLICATION->value);
        }

        //
        //  Handle special tracker case
        //
        if ($tracker === 'caramie') {
            $tracker = 'cara';
        }

        //
        //  Put the validated data into a DTO
        //
        $dto = new ProspectApplicationSubmissionData(
            type:                   $type,
            firstName:              $firstName,
            lastName:               $lastName,
            studioName:             $studioName,
            studioWebsite:          $studioWebsite,
            emailAddress:           $emailAddress,
            phoneNumber:            $phoneNumber,
            preferredContactMethod: $preferredContactMethod,
            countryCode:            $countryCode,
            stateCode:              $stateCode,
            password:               $password,
            omitPassword:           $omitPassword,
            dateOfBirth:            $dateOfBirth,
            gender:                 $gender,
            tracker:                $tracker,
            campaignId:             $this->legacyDetermineCampaignId($campaignId, $tracker, $service),
            refAffiliateId:         (string)$refAffiliateId,
            refModelId:             (string)$refModelId,
            refBroadcasterId:       (string)$refBroadcasterId,
            refScreenName:          $refScreenName,
            language:               $language,
            ipAddress:              $ipAddress,
            userAgent:              $userAgent,
        );

        //
        //  Match existing accounts
        //
        if ($existingApplication === null) {
            $matchingProspects = $this->getMatching($emailAddress, $phoneNumber);
            foreach ($matchingProspects as $matchingProspect) {
                $pushThrough = false; // Used to determine if we are going to push through the exact match for prospect.

                try {
                    $studio = $matchingProspect->getAdminUserId() === null ? null : $this->legacyFindStudioByStudioAdminId($matchingProspect->getAdminUserId());
                } catch (Exception $e) {
                    $studio = null;
                }

                //
                //  Only push through if they belong to an inactive studio
                //
                if ($studio !== null && $studio->getStatus() === StudioStatus::DISABLED) {
                    $count = count($this->performerService->getByEmailAddress($matchingProspect->getEmailAddress() ?? ''));
                    if ($count > 0) {
                        $pushThrough = true;
                    }
                }

                //
                //  Only need to do the part below if prospect is not being pushed through.
                //
                if (!$pushThrough) {
                    $this->legacyErrorOnDuplicateApplication($studio, $matchingProspect);
                }
            }
        }

        //
        //  Check Prospects name and birthday
        //
        if ($existingApplication === null) {
            $matchingBirthdayErrorMessage = $this->legacyVerifyExactMatchBasedOnNameBirthdayPair($dto, $dateOfBirth);
            if ($matchingBirthdayErrorMessage !== null) {
                throw new Exception($matchingBirthdayErrorMessage, ExceptionCode::EXISTING_EXACT_DATE_OF_BIRTH_MATCH_FOR_PROSPECT_APPLICATION->value);
            }
        }

        //
        //  Assign the prospect to a saleperson and determine if the prospect is from an exclusive region
        //
        $studioAdmin = $this->legacyDetermineStudioAdministrator($dto->tracker);
        if ($studioAdmin === null) {
            $studioAdminId = $this->legacyAssignStudioAdminWhenInExclusiveRegion($dto, $geolocationLookup);
            if ($studioAdminId !== null) {
                $studioAdmin = $this->adminUserService->getById($studioAdminId);
            }
        }

        //
        //  Now that validation is complete we can do our insert
        //
        $statusFinal = $studioAdmin !== null ? ($studioAdmin->getStatus() === AdminUserStatus::ACTIVE ? 'sent_exclusive' : 'in_progress') : 'in_progress';

        if ($existingApplication !== null) {
            $emailAddressChanged = $existingApplication->getEmail() !== $dto->emailAddress;

            $prospectApplication = clone $existingApplication;

            if ($emailAddressChanged) {
                $prospectApplication->setEmail($dto->emailAddress);
                $prospectApplication->setEmailConfirmCode($this->uuidService->getUuid4());
                $prospectApplication->processSection(ProspectApplicationSection::NAME_EMAIL_ADDRESS_CONFIRMATION, ProspectApplicationSectionStatus::PENDING);

                $this->sendConfirmationEmail($prospectApplication);
            }

            $prospectApplication->setFirstName($dto->firstName);
            $prospectApplication->setLastName($dto->lastName);
            $prospectApplication->setTelephone($dto->phoneNumber);
            $prospectApplication->setPreferredContactMethod(ProspectApplicationContactMethod::tryFrom($dto->preferredContactMethod));
            $prospectApplication->setGeography($dto->countryCode);
            $prospectApplication->setState($dto->stateCode);
            $prospectApplication->setGender(ProspectApplicationGender::tryFrom($dto->gender));
            $prospectApplication->setDob(is_string($dto->dateOfBirth) ? new DateTime($dto->dateOfBirth) : $dto->dateOfBirth);

            $prospectApplication->getProspect()?->setName(trim($dto->firstName . ' ' . $dto->lastName));
            $prospectApplication->getProspect()?->setEmailAddress($dto->emailAddress);
            $prospectApplication->getProspect()?->setBirthdate(is_string($dto->dateOfBirth) ? new DateTime($dto->dateOfBirth) : $dto->dateOfBirth);
            $prospectApplication->getProspect()?->setCompanyName($dto->studioName);
            if (!empty($dto->password)) {
                $salt = hash('sha256', rand() . self::SECRET_PHRASE);

                $prospectApplication->getProspect()?->setSalt($salt);
                $prospectApplication->getProspect()?->setPassword(sha1($salt . sha1($dto->password)));
                $prospectApplication->getProspect()?->setEncPassword(hash('sha256', $salt . $dto->password));
            }
            $prospectApplication->getProspect()?->setPhoneNumber($dto->phoneNumber);
            $prospectApplication->getProspect()?->setCountryCode($dto->countryCode);
            $prospectApplication->getProspect()?->setState($dto->stateCode);
            $prospectApplication->getProspect()?->setCameras($this->getOperatingSystemFromUserAgent($dto->userAgent));
            $prospectApplication->getProspect()?->setPerformers(ucfirst($dto->type));
            $prospectApplication->getProspect()?->setIpAddress($dto->ipAddress);
            $prospectApplication->getProspect()?->setTracker($dto->tracker);
            $prospectApplication->getProspect()?->setCampaignId($dto->campaignId);
            $prospectApplication->getProspect()?->setPreferredContactMethod($dto->preferredContactMethod);
            $prospectApplication->getProspect()?->setStudioWebsite($dto->studioWebsite);
            $prospectApplication->getProspect()?->setRefAffiliateId((int)$dto->refAffiliateId);
            $prospectApplication->getProspect()?->setRefModelId((int)$dto->refModelId);
            $prospectApplication->getProspect()?->setRefBroadcasterId((int)$dto->refBroadcasterId);
            $prospectApplication->getProspect()?->setService($service);
            $prospectApplication->getProspect()?->setDateLastUpdated(new DateTime());
            $prospectApplication->getProspect()?->setSalespersonId($this->legacyGetSalesPersonIdBasedOnTrackerAssociation($dto->tracker));
            $prospectApplication->getProspect()?->setAdminUserId($studioAdmin?->getId());
            $prospectApplication->getProspect()?->setSignupOrigin($dto->type === 'studio ' ? 'legacy' : '2019-a');
            $prospectApplication->getProspect()?->setStatus(ProspectStatus::ACTIVE);
            $prospectApplication->getProspect()?->setStatusFinal($statusFinal);

            $this->applyDiffsThenSave($prospectApplication);
        } else {
            try {
                $prospectApplication = $this->legacyInsertToDatabase($dto, $statusFinal, $service, $studioAdmin, $platformUser);
            } catch (Exception $e) {
                throw new Exception('An error occurred while trying to save your application.');
            }

            if (!$prospectApplication->hasConfirmedEmail()) {
                $this->sendConfirmationEmail($prospectApplication);
            }
        }

        $this->confirmPersonalDetailsStatus($prospectApplication);

        return $prospectApplication;
    }

    /**
     * Confirm the personal detail status of a prospect application
     *
     * @param ProspectApplication $prospectApplication The prospect application
     *
     * @return void
     */
    public function confirmPersonalDetailsStatus(ProspectApplication $prospectApplication): void
    {
        //
        //  If the personal details section is already approved there's nothing to do so return
        //
        if ($prospectApplication->getSectionStatus(ProspectApplicationSection::NAME_PERSONAL_DETAILS) === ProspectApplicationSectionStatus::APPROVED) {
            return;
        }

        //
        //  Calculate the application status and set the personal details section to pending if appropriate
        //
        $prospectApplication->calculateStatus();
        switch ($prospectApplication->getApplicationStatus()) {
            case ProspectApplicationStatus::MISSING_PERSONAL_DATA:
            case ProspectApplicationStatus::PERSONAL_DATA_PENDING_REVIEW:
                $prospectApplication->processSection(
                    ProspectApplicationSection::NAME_PERSONAL_DETAILS,
                    ProspectApplicationSectionStatus::PENDING,
                );

                $this->applyDiffsThenSave($prospectApplication);
                break;
        }
    }

    /**
     * XXX
     *
     * @param ProspectApplication $application   The prospect application
     * @param string              $applicationId The prospect's application ID
     * @param string              $fakeDob       The prospect's fake date of birth
     * @param string              $stageName     The prospect's stage name
     *
     * @throws Exception                If the prospect application has a problem
     * @throws InvalidArgumentException When a parameter is invalid
     *
     * @return void
     */
    public function createProfile(
        ProspectApplication $application,
        string $applicationId,
        string $fakeDob,
        string $stageName,
    ): void {
        //
        //  Validate parameters
        //
        if ($applicationId === '') {
            throw new InvalidArgumentException('Invalid application_id provided.', ExceptionCode::MISSING_APPLICATION_ID->value);
        }

        if (trim($application->getApplicationId() ?? '') !== trim($applicationId)) {
            throw new InvalidArgumentException('The application_id provided does not match the prospect application.', ExceptionCode::MISSING_APPLICATION_ID->value);
        }

        try {
            new DateTime($fakeDob);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Please provide a valid date of birth.', ExceptionCode::INVALID_FAKE_DOB_FOR_PROSPECT_CREATE_PROFILE->value);
        }

        if ($stageName === '') {
            throw new InvalidArgumentException('Please provide a valid stage name.', ExceptionCode::MISSING_STAGE_NAME_FOR_PROSPECT_CREATE_PROFILE->value);
        }

        //
        //  Ensure the application is pending
        //
        switch ($application->getApplicationStatus()) {
            case ProspectApplicationStatus::APPROVED:
            case ProspectApplicationStatus::REJECTED:
                throw new Exception('Application is not pending, and can no longer be modified.', ExceptionCode::PROSPECT_APPLICATION_STATUS_NOT_PENDING->value);
        }

        //
        //  Ensure the stage name isn't already been approved
        //
        if ($application->getSectionStatus(ProspectApplicationSection::NAME_STAGE_NAME) === ProspectApplicationSectionStatus::APPROVED) {
            throw new Exception('The Stage Name has already been approved and can not be changed.', ExceptionCode::PROSPECT_APPLICATION_STAGE_NAME_ALREADY_APPROVED->value);
        }

        //
        //  Format and validate the stage name
        //
        $stageName = $this->formatStageName($stageName);

        $this->validateStageName($stageName);

        //
        //  Set the application's stage name then auto-approve it if it was a suggestion
        //
        if ($application->getStageName() !== $stageName) {
            $application->setStageName($stageName);
            $application->getProspect()?->setStageName($stageName);

            try {
                $nameSuggestion = $this->performerService->getNameSuggestion($stageName);
                $nameSuggestion->setStatus(PerformerNameSuggestion::STATUS_INACTIVE);

                $this->performerNameSuggestionWriter->save($nameSuggestion);

                $application->processSection(
                    ProspectApplicationSection::NAME_STAGE_NAME,
                    ProspectApplicationSectionStatus::APPROVED,
                );
            } catch (Exception $e) {
                $application->processSection(
                    ProspectApplicationSection::NAME_STAGE_NAME,
                    ProspectApplicationSectionStatus::PENDING,
                );
            }
        }

        //
        //  Set the application's fake date of birth
        //
        try {
            $fakeDob = new DateTime($fakeDob);
            if ($fakeDob > new DateTime('-18 years')) {
                throw new Exception('Model must be at least 18 years of age or older.', ExceptionCode::FAKE_DATE_OF_BIRTH_IS_UNDERAGE->value);
            }
        } catch (Exception $e) {
            throw new Exception('The fake date of birth is not valid.', ExceptionCode::FAKE_DATE_OF_BIRTH_IS_NOT_VALID->value, $e);
        }

        if ($application->getFakeDob() !== $fakeDob) {
            $application->setFakeDob($fakeDob);
        }

        //
        //  Save any changes to the application
        //
        $this->applyDiffsThenSave($application);

        //
        //  Confirm the personal details status
        //
        $this->confirmPersonalDetailsStatus($application);
    }

    /**
     * XXX
     *
     * @param ProspectApplication $application          The prospect application
     * @param string              $typeOfIdentification The prospect's type of identification
     * @param string              $issuingAuthority     The prospect's issuing authority
     * @param string              $idNumber             The prospect's ID number
     *
     * @throws Exception                If the prospect application has a problem
     * @throws InvalidArgumentException When a parameter is invalid
     *
     * @return void
     */
    public function setIdentification(
        ProspectApplication $application,
        string $typeOfIdentification,
        string $issuingAuthority,
        string $idNumber,
    ): void {
        //
        //  Validate parameters
        //
        try {
            $typeOfIdentification = ProspectApplicationIdentificationMethod::from($typeOfIdentification);
        } catch (TypeError $e) {
            throw new InvalidArgumentException('Please provide a valid type of identification.', ExceptionCode::INVALID_TYPE_OF_IDENTIFICATION_FOR_PROSPECT_CREATE_PROFILE->value);
        }

        if (trim($issuingAuthority) === '') {
            throw new InvalidArgumentException('Please provide a valid issuing authority.', ExceptionCode::MISSING_ISSUING_AUTHORITY_FOR_PROSPECT_CREATE_PROFILE->value);
        }

        if (trim($idNumber) === '') {
            throw new InvalidArgumentException('Please provide a valid ID number.', ExceptionCode::MISSING_ID_NUMBER_FOR_PROSPECT_CREATE_PROFILE->value);
        }

        //
        //  Ensure the application is pending
        //
        switch ($application->getApplicationStatus()) {
            case ProspectApplicationStatus::APPROVED:
            case ProspectApplicationStatus::REJECTED:
                throw new Exception('Application is not pending, and can no longer be modified.', ExceptionCode::PROSPECT_APPLICATION_STATUS_NOT_PENDING->value);
        }

        //
        //  Set the application's type of identification
        //
        if ($application->getTypeOfIdentification() !== $typeOfIdentification) {
            $application->setTypeOfIdentification($typeOfIdentification);

            $application->processSection(
                ProspectApplicationSection::NAME_PERSONAL_DETAILS,
                ProspectApplicationSectionStatus::PENDING,
            );
        }

        //
        //  Set the application's issuing authority
        //
        if ($application->getIssuingAuthority() !== $issuingAuthority) {
            $application->setIssuingAuthority($issuingAuthority);

            $application->processSection(
                ProspectApplicationSection::NAME_PERSONAL_DETAILS,
                ProspectApplicationSectionStatus::PENDING,
            );
        }


        //
        //  Set the application's ID number
        //
        if ($application->getIdNumber() !== $idNumber) {
            $application->setIdNumber($idNumber);

            $application->processSection(
                ProspectApplicationSection::NAME_PERSONAL_DETAILS,
                ProspectApplicationSectionStatus::PENDING,
            );
        }

        //
        //  Save any changes to the application
        //
        $this->applyDiffsThenSave($application);

        //
        //  Confirm the personal details status
        //
        $this->confirmPersonalDetailsStatus($application);
    }

    /**
     * Transform a prospect application ID into a prospect ID
     *
     * @param string $applicationId The prospect application's ID
     *
     * @return int The prospect ID
     */
    public function applicationIdToProspectId(string $applicationId): int
    {
        try {
            $base64Decoded = $this->base64Service->decode($applicationId);
        } catch (Exception $e) { // If the first Base64 decode attempt fails then try again with the $applicationId being run through urldecode() first
            $base64Decoded = $this->base64Service->decode(urldecode($applicationId));
        }

        return (int)urldecode(str_replace(self::APPLICATION_ID_SALT, '', $base64Decoded));
    }

    /**
     * Transform a prospect ID into a prospect application ID
     *
     * @param int $prospectId The prospect's ID
     *
     * @return string The prospect application ID
     */
    public function prospectIdToApplicationId(int $prospectId): string
    {
        return urlencode(base64_encode((string)$prospectId . self::APPLICATION_ID_SALT));
    }

    /**
     * Send a prospect application confirmation email
     *
     * @param ProspectApplication $prospectApplication The prospect application
     *
     * @return void
     */
    public function sendConfirmationEmail(ProspectApplication $prospectApplication): void // NOT_IMPLEMENTED: This can be run without any limits and we should throttle it to prevent abuse
    {
        $confirmUrl = 'http://';
        if (getenv('IS_SANDBOX') === 'true' || getenv('IS_DEV') === 'true') {
            $confirmUrl .= 'studios-flirt4free-com.dev.vsmedia.net';
        } elseif (getenv('IS_STAGING') === 'true') {
            $confirmUrl .= 'studios-flirt4free-com.staging.vsmedia.net';
        } else {
            $confirmUrl .= 'studios.flirt4free.com';
        }
        $confirmUrl .= '/modelsignup/?application-id=' . ($prospectApplication->getApplicationId() ?? '') . '&email-confirm-code=' . ($prospectApplication->getEmailConfirmCode() ?? '');

        $apiRequest = $this->requestFactory
            ->createRequest('POST', $this->legacyWebServiceProtocolAndHostname . '?to_email=' . urlencode($prospectApplication->getEmail() ?? ''))
            ->withHeader('Content-Type', 'Content-Type: application/json')
            ->withHeader('Authorization', 'Bearer ' . $this->legacyWebServiceBearerToken)
            ->withBody($this->streamFactory->createStream(
                $this->jsonService->encode([
                    'subject'      => 'Welcome to Flirt4Free! Please Confirm Your Email Address',
                    'template'     => 'welcome_email',
                    'templateData' => [
                        'confirmUrl'   => $confirmUrl,
                        'emailAddress' => $prospectApplication->getEmail() ?? '',
                    ],
                    'toAddress'    => [$prospectApplication->getEmail() ?? ''],
                ]),
            ));

        $this->httpClient->sendRequest($apiRequest);
    }

    /**
     * Confirm a prospect application's email address
     *
     * @param ProspectApplication $application The prospect application
     * @param string              $confirmCode The email confirmation code
     *
     * @throws InvalidArgumentException If the email address has already been confirmed
     *
     * @return void
     */
    public function confirmEmailAddress(ProspectApplication $application, string $confirmCode): void
    {
        //
        //  Do not allow confirming an already confirmed email address
        //
        if ($application->hasConfirmedEmail()) {
            throw new InvalidArgumentException('This email address has already been confirmed.', ExceptionCode::PROSPECT_APPLICATION_EMAIL_ALREADY_CONFIRMED->value);
        }

        //
        //  Validate the confirmation code
        //
        if ($application->getEmailConfirmCode() !== $confirmCode) {
            throw new InvalidArgumentException('That confirmation code is invalid.', ExceptionCode::INVALID_EMAIL_CONFIRMATION_CODE->value);
        }

        //
        //  Update the application to mark the email as confirmed then save it
        //
        $application->processSection(
            ProspectApplicationSection::NAME_EMAIL_ADDRESS_CONFIRMATION,
            ProspectApplicationSectionStatus::APPROVED,
        );

        if ($application->getProspect() !== null) {
            $application->getProspect()->setEmailConfirmed(1);
            $application->getProspect()->setDateEmailConfirmed(new DateTime());
        }

        //
        //  Save any changes to the application
        //
        $this->applyDiffsThenSave($application);
    }

    /**
     * Load a prospect application's images
     *
     * @param ProspectApplication $prospectApplication The prospect application
     * @param bool                $overrideOriginal    The flag for overriding original (optional) (default: false)
     *
     * @return void
     */
    public function loadImages(ProspectApplication $prospectApplication, bool $overrideOriginal = false): void
    {
        $prospectApplication->setProspectImages(
            $this->prospectImageReader->getByProspectId(
                $this->applicationIdToProspectId($prospectApplication->getApplicationId() ?? ''),
            ),
            $overrideOriginal,
        );
    }

    /**
     * Validate a stage name
     *
     * @param string $stageName The stage name
     *
     * @throws Exception If the stage name is invalid or already in use
     *
     * @return void
     */
    public function validateStageName(string $stageName): void
    {
        if (!preg_match('/^[A-Za-z_ ]+$/', $stageName)) {
            throw new Exception(
                'The stage name `' . $stageName . '` contains invalid characters. Only letters, spaces and underscores are allowed.',
                ExceptionCode::INVALID_PERFORMER_STAGE_NAME->value,
            );
        } elseif (
            count($this->performerService->getByName($stageName)) > 0 // Check for existing performers with this stage name
            || count($this->getByStageName($stageName)) > 0 // Check for existing prospects with this stage name
        ) {
            throw new Exception('The stage name `' . $stageName . '` is already in use by another performer.', ExceptionCode::STAGE_NAME_ALREADY_IN_USE->value);
        }
    }

    /**
     * Format a stage name
     *
     * @param string $stageName The stage name
     *
     * @return string The formatted stage name
     */
    public function formatStageName(string $stageName): string
    {
        $stageName = preg_replace('/\s{2,}/', ' ', $stageName); // Replace multiple spaces with a single space
        assert(is_string($stageName));

        $stageName = str_replace(' ', '_', $stageName); // Replace spaces with underscores

        return strtoupper(trim($stageName)); // Trim to remove leading/trailing whitespace
    }

    /**
     * Confirm the status of a prospect application
     *
     * @param ProspectApplication $application The prospect application
     *
     * @return void
     */
    public function confirmStatus(ProspectApplication $application): void
    {
        $currentStatus = $application->getApplicationStatus();

        $application->calculateStatus();

        if ($currentStatus !== $application->getApplicationStatus()) {
            $this->prospectApplicationWriter->save($application);
        }
    }

    /**
     * Transform a URL to handle any HAProxy edge cases
     *
     * @param string $url The URL
     *
     * @return string The transformed URL
     */
    private function transformUrl(string $url): string
    {
        //
        //  Match any 10.x.x.x IP address with optional port), the match starts at the beginning of the string or right after '//' (e.g. http:// or https://)
        //
        $url = preg_replace(
            '~(^|//)10(?:\.(?:\d{1,3})){3}(?::\d+)?/~',
            '$1studios-flirt4free-com.dev.vsmedia.net/msapi/',
            $url,
        );
        assert(is_string($url));

        //
        //  Match static hostnames
        //
        $url = str_replace('modelsignup-api.dev.vsmedia.net/', 'studios-flirt4free-com.dev.vsmedia.net/msapi/', $url);
        $url = str_replace('modelsignup-api.dev.vsmedia.net:8080/', 'studios-flirt4free-com.dev.vsmedia.net/msapi/', $url);
        $url = str_replace('modelsignup-api.staging.vsmedia.net/', 'studios-flirt4free-com.staging.vsmedia.net/msapi/', $url);
        $url = str_replace('modelsignup-api.staging.vsmedia.net:8080/', 'studios-flirt4free-com.staging.vsmedia.net/msapi/', $url);
        $url = str_replace('modelsignup-api.lan.vsmedia.net/', 'studios.flirt4free.com/msapi/', $url);
        $url = str_replace('modelsignup-api.lan.vsmedia.net:8080/', 'studios.flirt4free.com/msapi/', $url);

        //
        //  Return the transformed URL
        //
        return $url;
    }

    /**
     * Apply diffs to a prospect application then save it
     *
     * @param ProspectApplication $prospectApplication The prospect application
     *
     * @return void
     */
    private function applyDiffsThenSave(ProspectApplication $prospectApplication): void
    {
        //
        //  Apply the diffs if applicable then save the application
        //
        if ($prospectApplication->getId() !== null) {
            $newestVersion = $this->getApplicationByApplicationId($prospectApplication->getApplicationId() ?? '');

            foreach ($prospectApplication->getDiffArray() as $diff) {
                $newestVersion->{$diff->method}(...$diff->parameters);
            }

            $prospectApplication->copyFrom($newestVersion);
        }

        $prospectApplication->setAdminId(0);
        $prospectApplication->calculateStatus();

        $this->prospectApplicationWriter->save($prospectApplication);

        //
        //  Save the prospect
        //
        if ($prospectApplication->getProspect() !== null) {
            $this->prospectWriter->save($prospectApplication->getProspect());
        }
    }

    /**
     * Load a prospect application's prospect
     *
     * @param ProspectApplication $prospectApplication The prospect application
     * @param bool                $overrideOriginal    The flag for overriding original (optional) (default: false)
     *
     * @return void
     */
    private function loadProspect(ProspectApplication $prospectApplication, bool $overrideOriginal = false): void
    {
        $prospects = $this->prospectReader->getById(
            $this->applicationIdToProspectId($prospectApplication->getApplicationId() ?? ''),
        );
        if (count($prospects) > 0) {
            $prospectApplication->setProspect($prospects[0], $overrideOriginal);
        }
    }

    /**
     * Get the operating system from a user agent (copy/paste job from model-signup.vsm-api.com/root/class/Prospects.cl)
     *
     * @param string $userAgent The HTTP user agent
     *
     * @return string The operating system if found or null if not
     */
    private function getOperatingSystemFromUserAgent(string $userAgent): ?string
    {
        //
        //  Define operating systems
        //
        $operatingSystems = [
            'Intel Mac OS X'      => 'Mac OS X',
            'Linux'               => 'Linux',
            'Linux i686'          => 'Linux',
            'Macintosh'           => 'Mac OS X',
            'PPC Mac OS X'        => 'Mac OS X',
            'PPC Mac OS X Mach-O' => 'Mac OS X',
            'Windows 9x 4.90'     => 'Windows ME',
            'Windows 95'          => 'Windows 95',
            'Windows 98'          => 'Windows 98',
            'Windows CE'          => 'Windows CE',
            'Windows NT 4.0'      => 'Windows NT 4.0',
            'Windows NT 5.0'      => 'Windows 2000',
            'Windows NT 5.01'     => 'Windows 2000, Service Pack 1(SP1)',
            'Windows NT 5.1'      => 'Windows XP',
            'Windows NT 5.2'      => 'Windows Server 2003/XP x64 Edition',
            'Windows NT 6.0'      => 'Windows Vista',
            'Windows NT 6.1'      => 'Windows 7',
            'X11'                 => 'Linux',
        ];

        //
        //  Browser and operating system info: based on the user agent get the values within the parenthesis
        //
        $userAgentArray = explode(';', substr($userAgent, strpos($userAgent, '(') + 1, strpos(trim($userAgent), ')') - strpos($userAgent, '(') - 1));

        $operatingSystemPosition = !empty($userAgentArray[2]) && strstr($userAgent, 'Mozilla') ? (strstr(strtolower($userAgentArray[2]), 'aol') ? 3 : 2) : 0; // We have to test for aol because it is a special case
        return $operatingSystems[trim($userAgentArray[$operatingSystemPosition])] ?? null;
    }

    /**
     * Check if a password is valid
     *
     * @param string $password The password
     *
     * @return bool Whether or not the password is valid
     */
    private function passwordIsValid(string $password): bool
    {
        // Password must be at least 6 characters long.
        // Password must be no more than 32 characters long.
        // Password must contain at least 1 lower case letter.
        // Password must contain at least 1 upper case letter.
        // Password must contain at least 1 number.
        // Password can only contain characters A-Z, a-z, 0-9, -, and _
        return (bool)preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\-_]{6,32}$/', $password);
    }

    /**
     * Port of the legacy sendMailToRepresentatives method
     *
     * @param Prospect                          $prospect The prospect
     * @param ProspectApplicationSubmissionData $dto      The submission DTO
     *
     * @return string The rep's email address
     */
    private function legacySendMailToRepresentatives(Prospect $prospect, ProspectApplicationSubmissionData $dto): string
    {
        $repEmail = $prospect->getAdminUser()?->getStatus() === AdminUserStatus::ACTIVE && filter_var($prospect->getEmailAddress() ?? '', FILTER_VALIDATE_EMAIL) ? $prospect->getEmailAddress() : '';

        $this->legacySendAdminDuplicateEmail($repEmail, $prospect, $dto);

        return $repEmail ?? '';
    }

    /**
     * Port of the legacy SendAdminDuplicateEmail method
     *
     * @param ?string                           $repEmail            The rep's email address (optional) (default: null)
     * @param AdminUser|Prospect                $adminUserOrProspect The admin user or prospect
     * @param ProspectApplicationSubmissionData $dto                 The submission DTO
     *
     * @return void
     */
    private function legacySendAdminDuplicateEmail(?string $repEmail, AdminUser|Prospect $adminUserOrProspect, ProspectApplicationSubmissionData $dto): void
    {
        if (!filter_var($repEmail, FILTER_VALIDATE_EMAIL)) {
            $this->legacyInsertDupesTable($adminUserOrProspect, $dto);
        }
    }

    /**
     * Port of the legacy insertDupesTable method
     *
     * @param AdminUser|Prospect                $adminUserOrProspect The admin user or prospect
     * @param ProspectApplicationSubmissionData $dto                 The submission DTO
     *
     * @return void
     */
    private function legacyInsertDupesTable(AdminUser|Prospect $adminUserOrProspect, ProspectApplicationSubmissionData $dto): void
    {
        switch (gettype($adminUserOrProspect)) {
            case AdminUser::class:
                assert($adminUserOrProspect instanceof AdminUser);

                $match = [
                    'adult'                          => $adminUserOrProspect->getAdult(),
                    'canMonitor'                     => $adminUserOrProspect->getCanMonitor(),
                    'cookiePolicyAcceptanceDatetime' => $adminUserOrProspect->getCookiePolicyAcceptanceDatetime(),
                    'cookiePolicyAcceptanceIp'       => $adminUserOrProspect->getCookiePolicyAcceptanceIp(),
                    'cookiePolicyAcceptanceVersion'  => $adminUserOrProspect->getCookiePolicyAcceptanceVersion(),
                    'date'                           => $adminUserOrProspect->getDate(),
                    'dateLastLogin'                  => $adminUserOrProspect->getDateLastLogin(),
                    'editable'                       => $adminUserOrProspect->getEditable(),
                    'email'                          => $adminUserOrProspect->getEmail(),
                    'encPassword'                    => $adminUserOrProspect->getEncPassword(),
                    'forumUsername'                  => $adminUserOrProspect->getForumUsername(),
                    'id'                             => $adminUserOrProspect->getId(),
                    'lastClicked'                    => $adminUserOrProspect->getLastClicked(),
                    'lastOpened'                     => $adminUserOrProspect->getLastOpened(),
                    'mailAddress'                    => $adminUserOrProspect->getMailAddress(),
                    'monitorUsername'                => $adminUserOrProspect->getMonitorUsername(),
                    'name'                           => $adminUserOrProspect->getName(),
                    'password'                       => $adminUserOrProspect->getPassword(),
                    'psychic'                        => $adminUserOrProspect->getPsychic(),
                    'salt'                           => $adminUserOrProspect->getSalt(),
                    'solo'                           => $adminUserOrProspect->getSolo(),
                    'status'                         => $adminUserOrProspect->getStatus()?->value,
                    'telephone'                      => $adminUserOrProspect->getTelephone(),
                    'twitterHandle'                  => $adminUserOrProspect->getTwitterHandle(),
                    'twofaSecret'                    => $adminUserOrProspect->getTwofaSecret(),
                    'username'                       => $adminUserOrProspect->getUsername(),
                    'vsAccountManager'               => $adminUserOrProspect->getVsAccountManager(),
                    'vsMonitor'                      => $adminUserOrProspect->getVsMonitor(),
                ];

                break;

            case Prospect::class:
                assert($adminUserOrProspect instanceof Prospect);

                $match = [
                    'adminUserId'   => $adminUserOrProspect->getAdminUserId(),
                    'emailAddress'  => $adminUserOrProspect->getEmailAddress(),
                    'encPassword'   => $adminUserOrProspect->getEncPassword(),
                    'id'            => $adminUserOrProspect->getId(),
                    'name'          => $adminUserOrProspect->getName(),
                    'password'      => $adminUserOrProspect->getPassword(),
                    'phoneNumber'   => $adminUserOrProspect->getPhoneNumber(),
                    'salespersonId' => $adminUserOrProspect->getSalespersonId(),
                    'salt'          => $adminUserOrProspect->getSalt(),
                    'status'        => $adminUserOrProspect->getStatus()?->value,
                ];

                break;

            default:
                $match = null;
                break;
        }

        $duplicateProspect = new DuplicateProspect(
            reviewStatus:   DuplicateProspectStatus::PENDING,
            reviewAdminId:  0,
            reviewDate:     new DateTime('0000-00-00 00:00:00'),
            dateCreated:    new DateTime(),
            submissionJson: $this->jsonService->encode([
                'application' => [
                    'campaignId'             => $dto->campaignId,
                    'countryCode'            => $dto->countryCode,
                    'dateOfBirth'            => $dto->dateOfBirth,
                    'emailAddress'           => $dto->emailAddress,
                    'firstName'              => $dto->firstName,
                    'gender'                 => $dto->gender,
                    'ipAddress'              => $dto->ipAddress,
                    'language'               => $dto->language,
                    'lastName'               => $dto->lastName,
                    'password'               => $dto->password,
                    'phoneNumber'            => $dto->phoneNumber,
                    'preferredContactMethod' => $dto->preferredContactMethod,
                    'refAffiliateId'         => $dto->refAffiliateId,
                    'refBroadcasterId'       => $dto->refBroadcasterId,
                    'refModelId'             => $dto->refModelId,
                    'refScreenName'          => $dto->refScreenName,
                    'stateCode'              => $dto->stateCode,
                    'studioName'             => $dto->studioName,
                    'studioWebsite'          => $dto->studioWebsite,
                    'tracker'                => $dto->tracker,
                    'type'                   => $dto->type,
                    'userAgent'              => $dto->userAgent,
                ],
                'matches'     => $match,
            ]),
        );

        $this->duplicateProspectWriter->save($duplicateProspect);
    }

    /**
     * Port of the legacy findStudioByStudioAdminId method
     *
     * @param int $studioAdminId The studio admin ID
     *
     * @return ?Studio The studio is a match is found or null i no match is found
     */
    private function legacyFindStudioByStudioAdminId(int $studioAdminId): ?Studio
    {
        $studios = $this->studioService->getByAdminUserId($studioAdminId);
        return count($studios) > 0 ? $studios[0] : null;
    }

    /**
     * Port of the legacy errorOnDuplicateApplication method
     *
     * @param ?Studio  $studio   The studio
     * @param Prospect $prospect The prospect
     *
     * @throws Exception Throw an exception detailing the duplicate entry with steps for how to resolve
     *
     * @return void
     */
    private function legacyErrorOnDuplicateApplication(?Studio $studio, Prospect $prospect): void
    {
        if ($studio !== null && $studio->getStatus() === StudioStatus::ENABLED) {
            throw new Exception('Your person of contact is ' . ($studio->getContactDetails() !== null && $studio->getContactDetails() !== '' ? $studio->getContactDetails() : 'support@example.com'), ExceptionCode::PROSPECT_REAPPLYING->value);
        } elseif ($prospect->getStatus() === ProspectStatus::ACTIVE) {
            throw new Exception('You have an active application on file. Please contact support@example.com.', ExceptionCode::PROSPECT_REAPPLYING->value);
        } else {
            throw new Exception('You have an inactive application on file. Please contact support@example.com.', ExceptionCode::PROSPECT_REAPPLYING->value);
        }
    }

    /**
     * Port of the legacy verifyExactMatchBasedOnNameBirthdayPair method
     *
     * @param ProspectApplicationSubmissionData $dto         The submission DTO
     * @param DateTimeInterface                 $dateOfBirth The date of birth
     *
     * @return ?string The error message as a string if an exact match is found or null if no match is found
     */
    private function legacyVerifyExactMatchBasedOnNameBirthdayPair(ProspectApplicationSubmissionData $dto, DateTimeInterface $dateOfBirth): ?string
    {
        $matchingProspects = $this->getMatching(
            dateOfBirth:       $dateOfBirth,
            name:              trim($dto->firstName . ' ' . $dto->lastName),
            includeAdminEmail: false,
        );

        if (count($matchingProspects) === 0) {
            return null;
        }

        $this->legacySendMailToRepresentatives($matchingProspects[0], $dto);

        return $dto->type === 'performer' ? 'You already have an application on file. If you need your login, please check your email for a link.' : 'You already have an application on file. Please contact support@example.com.';
    }

    /**
     * Port of the legacy determineStudioAdministrator method
     *
     * @param string $tracker The tracker
     *
     * @return ?AdminUser The admin user is a match is found or null if no match is found
     */
    private function legacyDetermineStudioAdministrator(string $tracker): ?AdminUser
    {
        if (preg_match('/studio_/', trim($tracker))) {
            $trackerArray = explode('_', trim($tracker));
            if (!empty($trackerArray[1]) && is_numeric($trackerArray[1])) {
                return $this->adminUserService->getById((int)$trackerArray[1]);
            }
        }

        return null;
    }

    /**
     * Get the broadcast sales team
     *
     * @return array<array<string, int|string>> An associate array of the broadcast sales team
     */
    private function getBroadcastSalesTeam(): array
    {
        return [
            [
                'admin_id'        => 87,
                'aim'             => '',
                'email'           => 'jamie@flirt4free.com',
                'extension'       => '219',
                'icq'             => '',
                'msn'             => '',
                'name'            => 'Jamie Rodriguez',
                'skype'           => '',
                'studio_admin_id' => 1024,
                'title'           => 'Broadcast Manager',
                'tracker'         => 'jamie',
                'user_type'       => 'manager',
                'yahoo'           => '',
            ],
            [
                'admin_id'        => 124,
                'aim'             => '',
                'chart_color'     => 'black',
                'email'           => 'miguel@flirt4free.com',
                'extension'       => '243',
                'icq'             => '609-015-843',
                'msn'             => 'miguel@vs.com',
                'name'            => 'Miguel Arriaga',
                'skype'           => 'vs.miguel',
                'studio_admin_id' => 2792,
                'title'           => 'Senior Account Executive',
                'tracker'         => 'miguel',
                'user_type'       => 'senior',
                'yahoo'           => 'vsmiguel',
            ],
            [
                'admin_id'        => 1309,
                'aim'             => '',
                'chart_color'     => 'blue',
                'email'           => 'test3@example.com',
                'extension'       => '223',
                'icq'             => '',
                'msn'             => '',
                'name'            => 'Emily Agari',
                'skype'           => '',
                'studio_admin_id' => 826626,
                'title'           => 'Key Account Manager',
                'tracker'         => 'EAgari',
                'user_type'       => 'senior',
                'yahoo'           => '',
            ],
            [
                'admin_id'        => 443,
                'aim'             => '',
                'chart_color'     => 'green',
                'email'           => 'Carlos@flirt4free.com',
                'extension'       => '222',
                'icq'             => '',
                'msn'             => '',
                'name'            => 'Carlos Diaz',
                'skype'           => 'carlos.diazflirt4free',
                'studio_admin_id' => 240487,
                'title'           => 'BC Training Manager',
                'tracker'         => 'CDiaz',
                'user_type'       => 'senior',
                'yahoo'           => '',
            ],
            [
                'admin_id'        => 1124,
                'aim'             => '',
                'chart_color'     => 'green',
                'email'           => 'test2@example.com',
                'extension'       => '203',
                'icq'             => '',
                'msn'             => '',
                'name'            => 'Alisha Castillo',
                'skype'           => 'Alisha.F4F',
                'studio_admin_id' => 688985,
                'title'           => 'Junior Account Manager',
                'tracker'         => 'Alisha',
                'user_type'       => 'senior',
                'yahoo'           => '',
            ],
            [
                'admin_id'        => 1327,
                'aim'             => '',
                'chart_color'     => 'green',
                'email'           => 'ares.fayer@flirt4free.com',
                'extension'       => '',
                'icq'             => '',
                'msn'             => '',
                'name'            => 'Ares Fayer',
                'skype'           => '',
                'studio_admin_id' => 777917,
                'title'           => 'Key Account Manager',
                'tracker'         => 'AFayer',
                'user_type'       => 'senior',
                'yahoo'           => '',
            ],
        ];
    }

    /**
     * Port of the legacy getSalesPersonIdBasedOnTrackerAssociation method
     *
     * @param string $tracker The tracker
     *
     * @return int The studio admin ID if a match is found or 0 if no match is found
     */
    private function legacyGetSalesPersonIdBasedOnTrackerAssociation(string $tracker): int
    {
        if ($tracker !== '') {
            foreach ($this->getBroadcastSalesTeam() as $salesperson) {
                if (isset($salesperson['tracker']) && $salesperson['tracker'] === strtolower(trim($tracker))) {
                    return (int)$salesperson['admin_id'];
                }
            }
        }

        return 0;
    }

    /**
     * Get the exclusive regions
     *
     * @return array<string, array<string, int|string>> An array associative array of exclusive regions
     */
    private function getExclusiveRegions(): array
    {
        return [
            'BG'  => [ // Bulgaria
                'auto-assign'    => 0,
                'email'          => 'mirek@mrna.cz',
                'name'           => 'Mirek Mrna',
                'status'         => 1,
                'studio_user_id' => 7,
            ],
            'CS'  => [ // Czechoslovakia
                'auto-assign'    => 1,
                'email'          => 'mirek@mrna.cz',
                'name'           => 'spellMirek Mrna',
                'status'         => 1,
                'studio_user_id' => 7,
            ],
            'CZ'  => [ // Czech Republic
                'auto-assign'    => 1,
                'email'          => 'mirek@mrna.cz',
                'name'           => 'Mirek Mrna',
                'status'         => 1,
                'studio_user_id' => 7,
            ],
            'RO'  => [ // Romania
                'auto-assign'    => 1,
                'email'          => 'mirek@mrna.cz',
                'name'           => 'Mirek Mrna',
                'status'         => 1,
                'studio_user_id' => 7,
            ],
            'RU1' => [ // Russian Federation - St. Petersburg
                'auto-assign'    => 1,
                'email'          => 'dmitry@flirt4free.ru',
                'name'           => 'Dmitry Sokolov',
                'status'         => 1,
                'studio_user_id' => 43,
            ],
            'SK'  => [ // Slovakia
                'auto-assign'    => 1,
                'email'          => 'mirek@mrna.cz',
                'name'           => 'Mirek Mrna',
                'status'         => 1,
                'studio_user_id' => 7,
            ],
        ];
    }

    /**
     * Port of the legacy assignStudioAdminWhenInExclusiveRegion method
     *
     * @param ProspectApplicationSubmissionData $dto               The submission DTO
     * @param GeolocationLookup                 $geolocationLookup The geolocation lookup
     *
     * @return ?int The studio admin ID if a match is found or null if no match is found
     */
    private function legacyAssignStudioAdminWhenInExclusiveRegion(ProspectApplicationSubmissionData $dto, GeolocationLookup $geolocationLookup): ?int
    {
        //
        //  If prospect is in the exclusive region, assign studio admin
        //
        $repInfo = null;

        $exclusiveRegions = $this->getExclusiveRegions();
        if (!empty($exclusiveRegions[$dto->countryCode])) {
            /**
             * @var array<string, int|string> $repInfo
             */
            $repInfo = $exclusiveRegions[$dto->countryCode];
        } elseif (!empty($exclusiveRegions[$geolocationLookup->countryCode])) {
            /**
             * @var array<string, int|string> $repInfo
             */
            $repInfo = $exclusiveRegions[$geolocationLookup->countryCode];
        }

        //
        //  If $repInfo has been set then this performer is from an exclusive region
        //  If the REP is active then we need to set them as the studio admin
        //
        if ($repInfo !== null && $repInfo['status'] === 1 && $repInfo['auto-assign'] === 1) {
            return (int)$repInfo['studio_user_id'];
        }

        return null;
    }

    /**
     * Port of the legacy findPartialMatches method
     *
     * @param ProspectApplicationSubmissionData $dto The submission DTO
     *
     * @return Prospect[] An array of partial matches
     */
    private function legacyFindPartialMatches(ProspectApplicationSubmissionData $dto): array
    {
        $name = trim(preg_replace('/\s+/', ' ', trim($dto->firstName . ' ' . $dto->lastName)) ?? '');

        return $this->getPartialMatches($name !== '' ? $name : null, $dto->studioName !== '' ? $dto->studioName : null);
    }

    /**
     * Port of the legacy determineCampaignIdByTracker method
     *
     * @param string $campaignId The campaign ID
     * @param string $tracker    The tracker
     * @param string $service    The service
     *
     * @return ?int An integer if a campaign ID can be determined otherwise null
     */
    private function legacyDetermineCampaignId(string $campaignId, string $tracker, string $service): ?int
    {
        if ($campaignId !== '') {
            if (strpos($tracker, 'backpage') !== false) {
                return 10005;
            } elseif (strpos($tracker, 'google_fetish') !== false) {
                return 10025;
            } elseif (strpos($tracker, 'google') === 0) {
                $googleCampaign = explode('|', $tracker); // Google campaign will use the format "tracker=google|{campaign}|{keyword}"
                switch ($googleCampaign[1] ?? '') {
                    case 'industry':
                        return 10027;

                    case 'locations':
                        return 10029;

                    case 'competitors':
                        return 10031;

                    case 'modeling':
                        return 10033;

                    case 'fetish':
                        return 10025;

                    case 'moc2':
                    case 'webcam':
                        return 10043;

                    case 'moc':
                        return 10037;

                    case 'moc3':
                        return 10049; // Task 494126

                    case 'mmo':
                        return 10045;

                    case 'mmo2':
                        return 10047;

                    default:
                        switch ($service) {
                            case 'guys':
                                return 10057;

                            case 'girls':
                                return 10057;

                            default:
                                return 10001;
                        }
                }
            } elseif (strpos($tracker, 'bing') !== false) {
                return 10007;
            } elseif ($tracker === 'sexyjobs' || $tracker === 'sexyjobs.com' || $tracker === 'sexyjobs.com-peel') {
                return 10008;
            } elseif (strpos($tracker, 'craigslist') !== false) {
                return 10009;
            } elseif (strpos($tracker, 'nekkedjobs') !== false) {
                return 10011;
            } elseif (strpos($tracker, 'unholyjobs') !== false) {
                return 10013;
            } elseif (strpos($tracker, 'lustjobs') !== false) {
                return 10015;
            } elseif (strpos($tracker, 'cityvibes') !== false) {
                return 10017;
            } elseif (strpos($tracker, 'men4rent') !== false) {
                return 10019;
            } elseif (strpos($tracker, 'xbiz') !== false) {
                return 10023;
            } elseif (strpos($tracker, 'twitter') !== false) {
                return 10041;
            } elseif (strpos($tracker, 'facebook') !== false) {
                return 10039;
            }
        }

        return filter_var($campaignId, FILTER_VALIDATE_INT) ? (int)$campaignId : null;
    }

    /**
     * Port of the legacy encryptAuthKey method
     *
     * @return string The encrypted auth key
     */
    private function legacyEncryptAuthKey(): string
    {
        $authKey = $this->legacyRandomVar(6, 3);
        $authKey = $this->legacyMd5Encrypt($authKey);
        $authKey = base64_encode($authKey ?? '');
        return str_replace('=', '', $authKey);
    }

    /**
     * Port of the legacy Variables.cl->randomVar method
     * Generate a random string based on the length and type arguments.
     *
     * @param int $length The length of the random variable
     * @param int $type   The type of random variable (1 = numbers, 2 = letters, 3 = both)
     *
     * @return string The random variable
     */
    private function legacyRandomVar(int $length, int $type = 3): string
    {
        switch ($type) {
            case 1:
                $chars = '0123456789';
                break;

            case 2:
                $chars = 'abcdefghijklmnopqrstuvwxyz';
                break;

            default:
                $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                break;
        }

        $var = '';
        if ($length > 0) {
            for ($i = 1; $i <= $length; $i++) {
                $var .= substr($chars, rand(0, strlen($chars) - 1), 1);
            }
        }
        return $var;
    }

    /**
     * Port of the legacy Encrypt.cl->md5Encrypt method
     * Shuffles the provided string with the provided key using the md5 algorithm to randomize the resultant string
     *
     * @param string $string The string to encrypt
     * @param string $key    The encryption key
     *
     * @return ?string The shuffled string or null on failure
     */
    private function legacyMd5Encrypt(string $string, string $key = 'changeThisKey'): ?string
    {
        if (empty($string)) {
            return null; // NOT_IMPLEMENTED: Should be an exception not a null return
        }

        // The value to return
        $result = null;

        // Get the md5 hash of the key
        $hash = md5($key);

        // Build the raw md5 cipher
        $sep    = chunk_split($hash, 2, ':');
        $chunks = explode(':', $sep);
        $md5    = '';

        $chunksCount = count($chunks);
        for ($i = 0; $i < $chunksCount; $i++) {
            if ($chunks[$i]) {
                $md5 .= sprintf('%c', hexdec($chunks[$i]));
            }
        }

        // Mix in the md5 key with the original string
        $stringLength = strlen($string);
        for ($i = 0; $i < $stringLength; $i++) {
            $result .= sprintf(
                '%s%s',
                substr($this->legacyMd5Shuffle($md5), $i % strlen($md5), 1),
                $string[$i],
            );
        }

        // Rebuild the string
        $resultLength = strlen($result ?? '');
        for ($i = 0; $i < $resultLength; $i++) {
            if (isset($string[$i])) {
                $string[$i] = sprintf('%c', ord($result[$i] ?? '') + ord(substr($md5, $i % strlen($md5), 1)));
            } else {
                $string .= sprintf('%c', ord($result[$i] ?? '') + ord(substr($md5, $i % strlen($md5), 1)));
            }
        }

        // Build encoded string and replace any invalid URL characters to prevent improper decoding
        return base64_encode($string);
    }

    /**
     * Port of the legacy Encrypt.cl->md5Shuffle method
     *
     * @param string $md5 The MD5 encrypted string
     *
     * @return string The shuffled string
     */
    private function legacyMd5Shuffle(string $md5): string
    {
        $used     = [];
        $shuffled = $md5;

        $md5Length = strlen($md5);

        for ($i = 0; $i < $md5Length; $i++) {
            while (!in_array(($pos = rand(0, $md5Length - 1)), $used, true)) {
                $used[]       = $pos;
                $shuffled[$i] = $md5[$pos];
            }
        }

        return $shuffled;
    }

    /**
     * Port of the legacy insertProspectToDatabase method
     *
     * @param ProspectApplicationSubmissionData $dto         The submission DTO
     * @param string                            $statusFinal The statusinal
     * @param string                            $service     The prospect's service
     * @param ?AdminUser                        $studioAdmin The prospect's studio admin (or null if unassigned)
     * @param ?PlatformUser                     $refUser     The referring user (or null if none applicable)
     *
     * @return ProspectApplication The prospect application
     */
    private function legacyInsertToDatabase(ProspectApplicationSubmissionData $dto, string $statusFinal, string $service, ?AdminUser $studioAdmin, ?PlatformUser $refUser): ProspectApplication
    {
        $now = new DateTime();

        //
        //  Generate a salt
        //
        $salt = hash('sha256', rand() . self::SECRET_PHRASE);

        //
        //  Create a new prospect and application then save them
        //
        $prospect = new Prospect(
            name:                   trim($dto->firstName . ' ' . $dto->lastName),
            companyName:            $dto->studioName,
            emailAddress:           $dto->emailAddress,
            password:               sha1($salt . sha1($dto->password)),
            salt:                   $salt,
            encPassword:            hash('sha256', $salt . $dto->password),
            phoneNumber:            $dto->phoneNumber,
            authKey:                $this->legacyEncryptAuthKey(),
            fax:                    '',
            imAddress:              '',
            countryCode:            $dto->countryCode,
            state:                  $dto->stateCode,
            experience:             '',
            onF4f:                  'no',
            stageName:              '',
            cameras:                $this->getOperatingSystemFromUserAgent($dto->userAgent),
            performers:             ucfirst($dto->type),
            ipAddress:              $dto->ipAddress,
            tracker:                $dto->tracker,
            campaignId:             $dto->campaignId,
            comments:               '',
            preferredContactMethod: $dto->preferredContactMethod,
            contactMethodInfo:      '',
            studioWebsite:          $dto->studioWebsite,
            refAffiliateId:         (int)$dto->refAffiliateId,
            refModelId:             (int)$dto->refModelId,
            refBroadcasterId:       (int)$dto->refBroadcasterId,
            refUserId:              $refUser?->getId(),
            birthdate:              is_string($dto->dateOfBirth) ? new DateTime($dto->dateOfBirth) : $dto->dateOfBirth,
            service:                $service,
            allowTextMessages:      0,
            dateCreated:            $now,
            dateLastUpdated:        $now,
            salespersonId:          $this->legacyGetSalesPersonIdBasedOnTrackerAssociation($dto->tracker),
            adminUserId:            $studioAdmin?->getId(),
            signupOrigin:           $dto->type === 'studio ' ? 'legacy' : '2019-a',
            status:                 ProspectStatus::ACTIVE,
            statusFinal:            $statusFinal,
        );

        $this->prospectWriter->save($prospect);

        $prospectApplication = new ProspectApplication(
            adminId:                     0,
            applicationId:               $this->prospectIdToApplicationId($prospect->getId() ?? 0),
            applicationStatus:           ProspectApplicationStatus::MISSING_PERSONAL_DATA,
            campaignId:                  $dto->campaignId,
            dob:                         is_string($dto->dateOfBirth) ? new DateTime($dto->dateOfBirth) : $dto->dateOfBirth,
            email:                       $dto->emailAddress,
            emailConfirmCode:            $this->uuidService->getUuid4(),
            fakeDob:                     null,
            firstName:                   $dto->firstName,
            gender:                      ProspectApplicationGender::tryFrom($dto->gender),
            geography:                   $dto->countryCode,
            idNumber:                    '',
            ipAddress:                   $dto->ipAddress,
            issuingAuthority:            '',
            language:                    $dto->language,
            lastName:                    $dto->lastName,
            preferredContactMethod:      ProspectApplicationContactMethod::tryFrom($dto->preferredContactMethod),
            prospect:                    $prospect,
            refAffiliateId:              $dto->refAffiliateId,
            refBroadcasterId:            $dto->refBroadcasterId,
            refModelId:                  $dto->refModelId,
            refScreenName:               $dto->refScreenName,
            service:                     $service,
            stageName:                   '',
            state:                       $dto->stateCode,
            studioName:                  $dto->studioName,
            studioWebsite:               $dto->studioWebsite,
            telephone:                   $dto->phoneNumber,
            type:                        $dto->type === 'studio' ? ProspectApplicationType::STUDIO : ProspectApplicationType::PERFORMER,
            typeOfIdentification:        null,
            dateCreated:                 $now,
            prospectApplicationSections: [
                new ProspectApplicationSection(
                    name:               ProspectApplicationSection::NAME_PERSONAL_DETAILS,
                    status:             ProspectApplicationSectionStatus::PENDING,
                    approvalTimestamp:  null,
                    rejectionReason:    null,
                    rejectionTimestamp: null,
                    dateCreated:        $now,
                    dateUpdated:        $now,
                ),
                new ProspectApplicationSection(
                    name:               ProspectApplicationSection::NAME_EMAIL_ADDRESS_CONFIRMATION,
                    status:             ProspectApplicationSectionStatus::PENDING,
                    approvalTimestamp:  null,
                    rejectionReason:    null,
                    rejectionTimestamp: null,
                    dateCreated:        $now,
                    dateUpdated:        $now,
                ),
            ],
            prospectImages:              [],
        );
        $prospectApplication->calculateStatus();

        $this->prospectApplicationWriter->save($prospectApplication);

        return $prospectApplication;
    }

    /**
     * Get the contents of a 2257 document image
     *
     * @param ProspectApplication $application The prospect's application
     * @param ?string             $signature   The user submitted signature for the 2257 document (optional) (default: null)
     *
     * @return string The contents of the 2257 document image
     */
    private function getDocument2257Image(ProspectApplication $application, ?string $signature = null): string
    {
        return $this->document2257Service->getImage(new Document2257UbixApiRequestData(
            legalFirstName:             $application->getFirstName() ?? '',
            legalLastName:              $application->getLastName() ?? '',
            dateOfBirth:                $application->getDob()?->format('m/d/Y') ?? '',
            formOfIdentification:       $application->getTypeOfIdentification() !== null ? $application->getTypeOfIdentification()->value : '',
            issuingAuthority:           $application->getIssuingAuthority() ?? '',
            idNumber:                   $application->getIdNumber() ?? '',
            stageNameForThisProduction: $application->getStageName() ?? '',
            todaysDate:                 (new DateTime())->format('F j, Y'),
            beginDate:                  (new DateTime())->format('F j, Y'),
            primaryProducer:            trim(($application->getFirstName() ?? '') . ' ' . ($application->getLastName() ?? '')),
            modelId:                    (string)($application->getProspect()?->getId() ?? ''),
            allOtherNames:              ['', '', ''],
            signature:                  $signature ?? '',
        ));
    }
}
