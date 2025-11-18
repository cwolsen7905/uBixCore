<?php

declare(strict_types=1);

namespace Ubix\Controller\ModelSignupApi;

use Exception;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Throwable;
use Ubix\Controller\AbstractController as Controller;
use Ubix\DataTransferObject\ImageCropInstructions;
use Ubix\Enum\Exception\CustomerFacingExceptionCode;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\ProspectApplication\ProspectApplicationSectionStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationStatus;
use Ubix\Enum\ProspectApplication\ProspectApplicationType;
use Ubix\Enum\ProspectDocument\ProspectDocumentType;
use Ubix\Enum\ProspectImage\ProspectImageType;
use Ubix\Model\ProspectApplication;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\Base64Service;
use Ubix\Service\Blob\S3BlobService;
use Ubix\Service\JsonService;
use Ubix\Service\ProspectService;
use Ubix\Service\UuidService;

/**
 * Controller to handle prospect application related API calls
 *
 * @see \Ubix\Tests\Controller\ModelSignupApi\ApplicationControllerTest PHPUnit test case
 */
final class ApplicationController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger           $logger          PSR logger
     * @param TemplateRenderer $view            Template renderer
     * @param JsonService      $jsonService     JSON service
     * @param Base64Service    $base64Service   Base64 Service
     * @param ProspectService  $prospectService Prospect service
     * @param S3BlobService    $s3BlobService   S3 blob service
     * @param UuidService      $uuidService     UUID service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected Base64Service $base64Service,
        protected ProspectService $prospectService,
        protected S3BlobService $s3BlobService,
        protected UuidService $uuidService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Method to handle incoming document/2257/{applicationId} requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $applicationId The prospect application's ID
     *
     * @throws Exception If the values in the post body are missing or invalid
     * @throws HttpBadRequestException If there is a data type mismatch
     *
     * @return Response PSR response
     */
    public function document2257(Request $request, Response $response, string $applicationId): Response
    {
        //
        //  Load the objects needed to generate the document
        //
        $application = $this->prospectService->getApplicationByApplicationId(urlencode($applicationId));

        //
        //  Handle POST and GET request differently
        //
        if ($request->getMethod() === 'POST') { // POST requests are for signing the document
            /**
             * @var array{
             *     signature?: string,
             *     application_id?: string,
             * } $postBody
             */
            $postBody = $this->jsonService->decode($request->getBody()->getContents());

            $signature = $postBody['signature'] ?? null;
            if ($signature !== $application->getName()) {
                throw new Exception(
                    'The signature does not match the application name',
                    ExceptionCode::PROSPECT_APPLICATION_INVALID_SIGNATURE->value,
                );
            }

            $postedApplicationId = $postBody['application_id'] ?? null;
            if (urldecode($postedApplicationId ?? '') !== urldecode($applicationId)) {
                throw new Exception(
                    'The `application_id` in the request post body does not match the application ID',
                    ExceptionCode::PROSPECT_APPLICATION_ID_MISMATCH->value,
                );
            }

            $this->prospectService->approveDocument2257($application);
        } else { // GET requests are for generating and saving the document
            try {
                $application->getFile(ProspectDocumentType::DOCUMENT_2257);
            } catch (Exception $e) {
                $this->prospectService->generateDocument2257($request, $application);
            } catch (Throwable $e) {
                throw new HttpBadRequestException($request, 'Bad request');
            }
        }

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Method to handle incoming document/2257/image/{documentUuid} requests
     *
     * @param Request  $request      PSR request
     * @param Response $response     PSR response
     * @param string   $documentUuid The document's UUID
     *
     * @return Response PSR response
     */
    public function document2257Image(Request $request, Response $response, string $documentUuid): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $image = $this->s3BlobService->get(
            $this->prospectService->getS3Path(ProspectDocumentType::DOCUMENT_2257, $documentUuid),
        );

        //
        //  Send response
        //
        $response = $this->addCorsHeaders($response);

        return $response->withHeader('Content-Type', 'image/jpeg')->withBody(Stream::create($image));
    }

    /**
     * Method to handle incoming image/{imageUuid} requests
     *
     * @param Request  $request   PSR request
     * @param Response $response  PSR response
     * @param string   $imageUuid The image's UUID
     *
     * @return Response PSR response
     */
    public function imageDisplay(Request $request, Response $response, string $imageUuid): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $image = $this->s3BlobService->get(
            $this->prospectService->getS3Path(ProspectImageType::PHOTO_ID, $imageUuid),
        );

        //
        //  Send response
        //
        $response = $this->addCorsHeaders($response);

        return $response->withHeader('Content-Type', 'image/jpeg')->withBody(Stream::create($image));
    }

    /**
     * Method to handle incoming document/bc-agreement/{applicationId} requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $applicationId The prospect application's ID
     *
     * @throws Exception If the values in the post body are missing or invalid
     * @throws HttpBadRequestException If there is a data type mismatch
     *
     * @return Response PSR response
     */
    public function bcAgreement(Request $request, Response $response, string $applicationId): Response
    {
        //
        //  Load the objects needed to generate the document
        //
        $application = $this->prospectService->getApplicationByApplicationId(urlencode($applicationId));

        //
        //  Handle POST and GET request differently
        //
        if ($request->getMethod() === 'POST') { // POST requests are for signing the document
            /**
             * @var array{
             *     signature?: string,
             *     application_id?: string,
             * } $postBody
             */
            $postBody = $this->jsonService->decode($request->getBody()->getContents());

            $signature = $postBody['signature'] ?? null;
            if ($signature !== $application->getName()) {
                throw new Exception(
                    'The signature does not match the application name',
                    ExceptionCode::PROSPECT_APPLICATION_INVALID_SIGNATURE->value,
                );
            }

            $postedApplicationId = $postBody['application_id'] ?? null;
            if (urldecode($postedApplicationId ?? '') !== urldecode($applicationId)) {
                throw new Exception(
                    'The `application_id` in the request post body does not match the application ID',
                    ExceptionCode::PROSPECT_APPLICATION_ID_MISMATCH->value,
                );
            }

            $this->prospectService->approveBcAgreement($application);
        } else { // GET requests are for generating and saving the document
            try {
                $application->getFile(ProspectDocumentType::BROADCASTER_AGREEMENT);
            } catch (Exception $e) {
                $this->prospectService->generateBcAgreement($request, $application);
            } catch (Throwable $e) {
                throw new HttpBadRequestException($request, 'Bad request');
            }
        }

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Method to handle incoming document/2257/bc-agreement/{documentUuid} requests
     *
     * @param Request  $request      PSR request
     * @param Response $response     PSR response
     * @param string   $documentUuid The document's UUID
     *
     * @return Response PSR response
     */
    public function bcAgreementImage(Request $request, Response $response, string $documentUuid): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $image = $this->s3BlobService->get(
            $this->prospectService->getS3Path(ProspectDocumentType::BROADCASTER_AGREEMENT, $documentUuid),
        );

        //
        //  Send response
        //
        $response = $this->addCorsHeaders($response);

        return $response->withHeader('Content-Type', 'image/jpeg')->withBody(Stream::create($image));
    }

    /**
     * Method to handle incoming application/create-profile requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $applicationId The prospect application's ID
     *
     * @throws HttpBadRequestException If the profile creation fails
     *
     * @return Response PSR response
     */
    public function createProfile(Request $request, Response $response, string $applicationId): Response
    {
        //
        //  Load the application by application ID
        //
        $revisionParam = $request->getQueryParams()['revision'] ?? null;
        $revision      = is_numeric($revisionParam) ? (int)$revisionParam : null;

        $application = $this->prospectService->getApplicationByApplicationId(
            urlencode($applicationId),
            $revision,
        );

        //
        //  Load the posted values and attempt to create the profile
        //
        /**
         * @var array{
         *     application_id?: string,
         *     fake_dob?: string,
         *     stage_name?: string,
         * } $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        try {
            $this->prospectService->createProfile(
                application:   $application,
                applicationId: $postBody['application_id'] ?? '',
                fakeDob:       $postBody['fake_dob'] ?? '',
                stageName:     $postBody['stage_name'] ?? '',
            );
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (Throwable $e) {
            throw new HttpBadRequestException($request, 'Bad request');
        }

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Method to handle incoming application/identification requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $applicationId The prospect application's ID
     *
     * @throws HttpBadRequestException If setting the identification fails
     *
     * @return Response PSR response
     */
    public function identification(Request $request, Response $response, string $applicationId): Response
    {
        //
        //  Load the application by application ID
        //
        $revisionParam = $request->getQueryParams()['revision'] ?? null;
        $revision      = is_numeric($revisionParam) ? (int)$revisionParam : null;

        $application = $this->prospectService->getApplicationByApplicationId(
            urlencode($applicationId),
            $revision,
        );

        //
        //  Load the posted values and attempt to create the profile
        //
        /**
         * @var array{
         *     type_of_identification?: string,
         *     issuing_authority?: string,
         *     id_number?: string,
         * } $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        try {
            $this->prospectService->setIdentification(
                application:          $application,
                typeOfIdentification: $postBody['type_of_identification'] ?? '',
                issuingAuthority:     $postBody['issuing_authority'] ?? '',
                idNumber:             $postBody['id_number'] ?? '',
            );
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (Throwable $e) {
            throw new HttpBadRequestException($request, 'Bad request');
        }

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Method to handle incoming application/image requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $applicationId The prospect application's ID
     *
     * @throws Exception If the posted application ID doesn't match the URL application ID
     *
     * @return Response PSR response
     */
    public function image(Request $request, Response $response, string $applicationId): Response
    {
        //
        //  Load the objects needed to generate the document
        //
        $application = $this->prospectService->getApplicationByApplicationId(urlencode($applicationId));

        //
        //  Handle POST
        //
        /**
         * @var array{
         *     application_id?: string,
         *     image_type?: string,
         *     base64_encoded_binary_file?: string,
         *     crop?: array{
         *         x?: int,
         *         y?: int,
         *         width?: int,
         *         height?: int,
         *         rotate?: int,
         *     },
         * } $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        $postedApplicationId = $postBody['application_id'] ?? null;
        if (urldecode($postedApplicationId ?? '') !== urldecode($applicationId)) {
            throw new Exception(
                'The `application_id` in the request post body does not match the application ID',
                ExceptionCode::PROSPECT_APPLICATION_ID_MISMATCH->value,
            );
        }

        $this->prospectService->uploadImage(
            request:               $request,
            application:           $application,
            imageType:             ProspectImageType::from($postBody['image_type'] ?? ''),
            imageBinary:           $this->base64Service->decode($postBody['base64_encoded_binary_file'] ?? '') ?: '',
            imageCropInstructions: new ImageCropInstructions(
                x:      $postBody['crop']['x'] ?? 0,
                y:      $postBody['crop']['y'] ?? 0,
                width:  $postBody['crop']['width'] ?? 0,
                height: $postBody['crop']['height'] ?? 0,
                rotate: $postBody['crop']['rotate'] ?? 0,
            ),
        );

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Method to handle incoming application/submit requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param ?string  $applicationId The prospect application's ID (optional) (default: null)
     *
     * @throws HttpBadRequestException If the submission fails
     *
     * @return Response PSR response
     */
    public function submit(Request $request, Response $response, ?string $applicationId): Response
    {
        //
        //  Load the application by application ID
        //
        $revisionParam = $request->getQueryParams()['revision'] ?? null;
        $revision      = is_numeric($revisionParam) ? (int)$revisionParam : null;

        $application = $applicationId === null ? null : $this->prospectService->getApplicationByApplicationId(
            urlencode($applicationId),
            $revision,
        );

        //
        //  Load the posted values and attempt to create the profile
        //
        /**
         * @var array{
         *     type?: string,
         *     first_name?: string,
         *     last_name?: string,
         *     studio_name?: string,
         *     studio_website?: string,
         *     email?: string,
         *     phone_number?: string,
         *     preferred_contact_method?: string,
         *     country?: string,
         *     state?: string,
         *     password?: string,
         *     omit_password?: bool,
         *     dob?: string,
         *     gender?: string,
         *     tracker?: string,
         *     campaign_id?: string,
         *     ref_affiliate_id?: string,
         *     ref_model_id?: string,
         *     ref_broadcaster_id?: string,
         *     ref_screen_name?: string,
         *     service?: string,
         *     language?: string,
         *     captcha_val?: string,
         * } $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        $ipAddress = $request->getAttribute('normalizedIpAddress') ?? '';
        assert(is_string($ipAddress));

        try {
            $application = $this->prospectService->submit(
                existingApplication:    $application,
                type:                   $postBody['type'] ?? '',
                firstName:              $postBody['first_name'] ?? '',
                lastName:               $postBody['last_name'] ?? '',
                studioName:             $postBody['studio_name'] ?? '',
                studioWebsite:          $postBody['studio_website'] ?? '',
                emailAddress:           $postBody['email'] ?? '',
                phoneNumber:            $postBody['phone_number'] ?? '',
                preferredContactMethod: $postBody['preferred_contact_method'] ?? '',
                countryCode:            strtoupper($postBody['country'] ?? ''),
                stateCode:              strtoupper($postBody['state'] ?? ''),
                password:               $postBody['password'] ?? '',
                omitPassword:           $application !== null && isset($postBody['omit_password']) && $postBody['omit_password'] === true, // Only allow password omission if an existing application is being updated
                dateOfBirth:            $postBody['dob'] ?? '',
                gender:                 $postBody['gender'] ?? '',
                tracker:                $postBody['tracker'] ?? '',
                campaignId:             $postBody['campaign_id'] ?? '',
                refAffiliateId:         $postBody['ref_affiliate_id'] ?? '',
                refModelId:             $postBody['ref_model_id'] ?? '',
                refBroadcasterId:       $postBody['ref_broadcaster_id'] ?? '',
                refScreenName:          $postBody['ref_screen_name'] ?? '',
                language:               $postBody['language'] ?? 'en',
                ipAddress:              $ipAddress,
                captcha:                $postBody['captcha_val'] ?? '',
                userAgent:              $request->getHeaderLine('User-Agent'),
            );
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (Throwable $e) {
            throw new HttpBadRequestException($request, 'Bad request');
        }

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Method to handle incoming application/status requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $applicationId The prospect application's ID
     *
     * @return Response PSR response
     */
    public function status(Request $request, Response $response, string $applicationId): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        //
        //  Load the application by application ID
        //
        $application = $this->prospectService->getApplicationByApplicationId(urlencode($applicationId));

        //
        //  Confirm the application's status
        //
        $this->prospectService->confirmStatus($application);

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Method to handle incoming application/email-confirm requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $applicationId The prospect application's ID
     *
     * @return Response PSR response
     */
    public function emailConfirm(Request $request, Response $response, string $applicationId): Response
    {
        //
        //  Load the objects needed to generate the document
        //
        $application = $this->prospectService->getApplicationByApplicationId(urlencode($applicationId));

        //
        //  Load the posted values and attempt to confirm the email
        //
        /**
         * @var array{
         *     code?: string,
         * } $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        $ipAddress = $request->getAttribute('normalizedIpAddress') ?? '';
        assert(is_string($ipAddress));

        $this->prospectService->confirmEmailAddress(
            application: $application,
            confirmCode: $postBody['code'] ?? '',
        );

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Method to handle incoming application/resend-email requests
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $applicationId The prospect application's ID
     *
     * @return Response PSR response
     */
    public function resendEmail(Request $request, Response $response, string $applicationId): Response
    {
        //
        //  Load the objects needed to generate the document
        //
        $application = $this->prospectService->getApplicationByApplicationId(urlencode($applicationId));

        //
        //  Load the posted values and attempt to resend the confirmation email
        //
        /**
         * @var array{
         *     resend?: bool,
         * } $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        $resend = $postBody['resend'] ?? false;
        if ($resend) {
            $this->prospectService->sendConfirmationEmail($application);
        }

        //
        //  Send JSON response
        //
        $response = $this->addCorsHeaders($response);

        return $this->renderApplicationAsJson($response, $application);
    }

    /**
     * Add CORS headers to a PSR response
     *
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    private function addCorsHeaders(Response $response): Response // NOT IMPLEMENTED: this method likely shouldn't be here and should be more broadly accessible to the entire Project Neptune framework - we should also consider tightening up these headers to be more specific and potentially include validation (this may mean moving them into middleware or the abstract controller)
    {
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response = $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
        return $response->withHeader('Control-Allow-Credentials', 'true');
    }

    /**
     * Render a prospect application as JSON
     *
     * @param Response            $response    PSR response
     * @param ProspectApplication $application The prospect application
     *
     * @return Response PSR response
     */
    private function renderApplicationAsJson(Response $response, ProspectApplication $application): Response
    {
        $json = [
            'application_id'     => $application->getApplicationId(),
            'application_status' => [
                'date_created' => $application->getDateCreated()?->format('Y-m-d H:i:s'),
            ],
            'revision'           => $application->getId(),
            'status'             => [],
            'status_code'        => match ($application->getApplicationStatus()) {
                ProspectApplicationStatus::MISSING_PERSONAL_DATA       => CustomerFacingExceptionCode::APPLICATION_MISSING_PERSONAL_DATA_FOR_MODEL_SIGNUP_API,
                ProspectApplicationStatus::PERSONAL_DATA_PENDING_REVIEW => CustomerFacingExceptionCode::APPLICATION_PERSONAL_DATA_PENDING_REVIEW_FOR_MODEL_SIGNUP_API,
                ProspectApplicationStatus::MISSING_DOCUMENTS          => CustomerFacingExceptionCode::APPLICATION_MISSING_DOCUMENTS_FOR_MODEL_SIGNUP_API,
                ProspectApplicationStatus::DOCUMENTS_PENDING_REVIEW    => CustomerFacingExceptionCode::APPLICATION_DOCUMENTS_PENDING_REVIEW_FOR_MODEL_SIGNUP_API,
                ProspectApplicationStatus::DOCUMENTS_APPROVED         => CustomerFacingExceptionCode::APPLICATION_DOCUMENTS_APPROVED_FOR_MODEL_SIGNUP_API,
                ProspectApplicationStatus::APPROVED                  => CustomerFacingExceptionCode::APPLICATION_APPROVED_FOR_MODEL_SIGNUP_API,
                ProspectApplicationStatus::REJECTED                  => CustomerFacingExceptionCode::APPLICATION_REJECTED_FOR_MODEL_SIGNUP_API,
                default                                              => null,
            },
            'status_code_text'   => $application->getProspect()?->getStatusFinal() === 'sent_exclusive' ? 'The selected country belongs to an exclusive region. We\'ve forwarded your application to the selected Studio.' : match ($application->getApplicationStatus()) {
                ProspectApplicationStatus::MISSING_PERSONAL_DATA       => 'You must fill in all personal data.',
                ProspectApplicationStatus::PERSONAL_DATA_PENDING_REVIEW => 'Your personal data has been submitted and is pending review.',
                ProspectApplicationStatus::MISSING_DOCUMENTS          => 'You must submit all documents.',
                ProspectApplicationStatus::DOCUMENTS_PENDING_REVIEW    => 'Your documents have been submitted and are pending review.',
                ProspectApplicationStatus::DOCUMENTS_APPROVED         => 'Your documents have been approved.',
                ProspectApplicationStatus::APPROVED                  => 'Your application has been approved.',
                ProspectApplicationStatus::REJECTED                  => 'Your application has been rejected.',
                default                                              => null,
            },
            'values'             => [
                'allow_text_messages'      => $application->getProspect()?->getAllowTextMessages(),
                'application_id'           => $application->getApplicationId(),
                'birthdate'                => $application->getProspect()?->getBirthdate()?->format('Y-m-d'),
                'cameras'                  => $application->getProspect()?->getCameras(),
                'company_name'             => $application->getProspect()?->getCompanyName(),
                'contact_method_info'      => $application->getProspect()?->getContactMethodInfo(),
                'country'                  => $application->getProspect()?->getCountryCode(),
                'dob'                      => $application->getDob()?->format('Y-m-d'),
                'documents'                => array_map(function ($item) {
                    return [
                        'date_created' => $item->getDateCreated()?->format('Y-m-d'),
                        'md5_hash'     => $item->getMd5Hash(),
                        'type'         => $item->getType()?->value,
                        'url'          => $item->getUrl(),
                    ];
                }, $application->getApplicationDocuments() ?? []),
                'email'                    => $application->getEmail(),
                'experience'               => $application->getProspect()?->getExperience(),
                'fake_dob'                 => $application->getFakeDob()?->format('Y-m-d'),
                'fax'                      => $application->getProspect()?->getFax(),
                'first_name'               => $application->getFirstName(),
                'gender'                   => $application->getGender()?->value,
                'geography'                => $application->getGeography(),
                'id'                       => $application->getProspect()?->getId(),
                'id_number'                => $application->getIdNumber(),
                'images'                   => array_map(function ($item): array {
                    return [
                        'date_created' => $item->getDateCreated()?->format('Y-m-d'),
                        'md5_hash'     => $item->getMd5Hash(),
                        'type'         => $item->getType()?->value,
                        'url'          => $item->getUrl(),
                    ];
                }, $application->getApplicationImages() ?? []),
                'issuing_authority'        => $application->getIssuingAuthority(),
                'last_name'                => $application->getLastName(),
                'name'                     => trim(($application->getFirstName() ?? '') . ' ' . ($application->getLastName() ?? '')),
                'on_f4f'                   => $application->getProspect()?->getOnF4f(),
                'performers'               => $application->getProspect()?->getPerformers(),
                'phone_number'             => $application->getTelephone(),
                'preferred_contact_method' => $application->getPreferredContactMethod()?->value,
                'prospect_id'              => $this->prospectService->applicationIdToProspectId($application->getApplicationId() ?? ''),
                'service'                  => $application->getService(),
                'site_type'                => $application->getProspect()?->getSiteType(),
                'stage_name'               => $application->getStageName(),
                'state'                    => $application->getState(),
                'studio_website'           => $application->getStudioWebsite(),
                'telephone'                => $application->getProspect()?->getPhoneNumber(),
                'type'                     => match ($application->getType()) {
                    ProspectApplicationType::PERFORMER => 'performer',
                    ProspectApplicationType::STUDIO    => 'studio',
                    default                            => null,
                },
                'type_of_identification'   => $application->getTypeOfIdentification()?->value,
            ],
        ];

        if ($application->getProspectApplicationSections() !== null) {
            foreach ($application->getProspectApplicationSections() as $prospectApplicationSection) {
                $json['data']['status'][$prospectApplicationSection->getName()] = [
                    'approval_timestamp'  => $prospectApplicationSection->getApprovalTimestamp()?->format('Y-m-d'),
                    'date_created'        => $prospectApplicationSection->getDateCreated()?->format('Y-m-d'),
                    'date_updated'        => $prospectApplicationSection->getDateUpdated()?->format('Y-m-d'),
                    'rejection_reason'    => $prospectApplicationSection->getRejectionReason(),
                    'rejection_timestamp' => $prospectApplicationSection->getRejectionTimestamp()?->format('Y-m-d'),
                    'state'               => match ($prospectApplicationSection->getStatus()) {
                        ProspectApplicationSectionStatus::APPROVED => 'APPROVED',
                        ProspectApplicationSectionStatus::PENDING  => 'PENDING',
                        ProspectApplicationSectionStatus::REJECTED => 'REJECTED',
                        default                                    => null,
                    },
                    'suggestion1'         => $prospectApplicationSection->getRejectSuggest1(),
                    'suggestion2'         => $prospectApplicationSection->getRejectSuggest2(),
                    'suggestion3'         => $prospectApplicationSection->getRejectSuggest3(),
                ];
            }
        }

        return $this->renderJson($response, $json);
    }
}
