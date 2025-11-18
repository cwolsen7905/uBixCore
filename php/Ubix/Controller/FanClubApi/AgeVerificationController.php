<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\AgeVerification\AgeVerificationRequirement;
use Ubix\Model\AgeVerificationRestrictedRegion;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\AgeVerificationService;
use Ubix\Service\JsonService;
use Ubix\Service\LocationService;

/**
 * Controller to handle API calls involving filtering data
 *
 * @see \Ubix\Tests\Controller\FanClubApi\AgeVerificationControllerTest PHPUnit test case
 */
final class AgeVerificationController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger                 $logger                 PSR logger
     * @param TemplateRenderer       $view                   Template renderer
     * @param JsonService            $jsonService            JSON service
     * @param AgeVerificationService $ageVerificationService Age verification service
     * @param LocationService        $locationService        Location service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected AgeVerificationService $ageVerificationService,
        protected LocationService $locationService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Method to determine the age verification requirement
     *
     * @param Request  $request   PSR request
     * @param Response $response  PSR response
     * @param string   $ipAddress The IP address
     * @param ?int     $userId    The platform user ID (optional) (default: null)
     *
     * @throws HttpBadRequestException If the filter service fails (likely due to the API being down)
     *
     * @return Response PSR response
     */
    public function requirement(Request $request, Response $response, string $ipAddress, ?int $userId = null): Response
    {
        //
        //  Check if age verification is required
        //
        try {
            $ageVerificationRequirementDetails = $this->ageVerificationService->getRequirement($ipAddress, $userId);
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }

        //
        //  Render JSON response
        //
        $json = [
            'location'    => $ageVerificationRequirementDetails->ageVerificationRestrictedRegion?->getCode() === AgeVerificationRestrictedRegion::CODE_FOR_WHOLE_COUNTRY ? $this->countryCodeToText($ageVerificationRequirementDetails->ageVerificationRestrictedCountry?->getCode() ?? '') : $this->regionCodeToText($ageVerificationRequirementDetails->ageVerificationRestrictedCountry?->getCode() ?? '', $ageVerificationRequirementDetails->ageVerificationRestrictedRegion?->getCode() ?? ''),
            'requirement' => match ($ageVerificationRequirementDetails->ageVerificationRequirement) {
                AgeVerificationRequirement::BLOCKED     => 'blocked',
                AgeVerificationRequirement::NOT_REQUIRED => 'notRequired',
                AgeVerificationRequirement::REQUIRED    => 'required',
            },
        ];

        if ($ageVerificationRequirementDetails->nextUrl !== null) {
            $json['nextUrl'] = $ageVerificationRequirementDetails->nextUrl;
        }

        return $this->renderJson($response, $json);
    }

    /**
     * Transform a country code into text
     *
     * @param string $countryCode The country code
     *
     * @return string The text
     */
    private function countryCodeToText(string $countryCode): string
    {
        try {
            $country = $this->locationService->getCountryByIso31661Alpha2($countryCode);
            return $country->getName() ?? $countryCode;
        } catch (Exception $e) {
            return $countryCode;
        }
    }

    /**
     * Transform a region code into text
     *
     * @param string $countryCode The country code
     * @param string $regionCode  The region code
     *
     * @return string The text
     */
    private function regionCodeToText(string $countryCode, string $regionCode): string
    {
        try {
            $state = $this->locationService->getStateByIso31662($countryCode . '-' . $regionCode);
            return $state->getName() ?? $regionCode;
        } catch (Exception $e) {
            return $regionCode;
        }
    }
}
