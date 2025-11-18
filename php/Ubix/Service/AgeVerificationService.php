<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\AgeVerificationRequirementDetails;
use Ubix\Enum\AgeVerification\AgeVerificationRequirement;
use Ubix\Model\AgeVerificationRestrictedCountry;
use Ubix\Model\AgeVerificationRestrictedRegion;
use Ubix\Service\Geolocation\GeolocationServiceInterface as GeolocationService;

/**
 * Service to handle age verification
 *
 * @see \Ubix\Tests\Service\AgeVerificationServiceTest PHPUnit test case
 */
final class AgeVerificationService
{
    /**
     * Constructor
     *
     * @param Logger              $logger              Logger
     * @param GeolocationService  $geolocationService  Geolocation service
     * @param PlatformUserService $platformUserService Platform user service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private GeolocationService $geolocationService,
        private PlatformUserService $platformUserService,
    ) {
    }

    /**
     * Get the age verification requirement by IP address (and optionally platform user ID too)
     *
     * @param string $ipAddress      The IP address
     * @param ?int   $platformUserId The platform user ID (optional) (default: null)
     *
     * @return AgeVerificationRequirementDetails The age verification requirement details
     */
    public function getRequirement(string $ipAddress, ?int $platformUserId = null): AgeVerificationRequirementDetails
    {
        //
        //  Perform a geolocation lookup by IP address
        //
        $geolocationLookup = $this->geolocationService->getLookupByIpAddress($ipAddress);

        //
        //  Loop through the restricted countries to find a match
        //
        /**
         * @var ?AgeVerificationRestrictedCountry $matchingRestrictedCountry
         */
        $matchingRestrictedCountry = null;
        /**
         * @var ?AgeVerificationRestrictedRegion $matchingRestrictedRegion
         */
        $matchingRestrictedRegion = null;

        foreach ($this->getRestrictedCountries() as $restrictedCountry) {
            if ($restrictedCountry->getCode() === $geolocationLookup->countryCode) {
                $matchingRestrictedCountry = $restrictedCountry;
                $matchingRestrictedRegion  = $restrictedCountry->getMatchingRegion($geolocationLookup->stateCode);
                break;
            }
        }

        //
        //  If a platform user ID was passed check if it belongs to an account that was already age verified
        //
        $platformUser = null;
        if ($platformUserId !== null) {
            $platformUser = $this->platformUserService->getById($platformUserId);

            if ($matchingRestrictedCountry !== null && $matchingRestrictedRegion !== null) {
                $this->platformUserService->verifyAgeVerificationStatus($platformUser, $matchingRestrictedCountry, $matchingRestrictedRegion);
            }
        }

        //
        //  If we have a matching restricted region and it is active then return the age verification requirement
        //
        if ($matchingRestrictedRegion !== null && $matchingRestrictedRegion->isActive()) {
            if ($matchingRestrictedRegion->getBlocked() === true) {
                $ageVerificationRequirement = AgeVerificationRequirement::BLOCKED;
            } elseif ($platformUser !== null && $platformUser->getAgeVerified() === 'Y') { // If the platform user has previously gone through the age verification process they are not required to do so again
                $ageVerificationRequirement = AgeVerificationRequirement::NOT_REQUIRED;
            } else {
                $ageVerificationRequirement = AgeVerificationRequirement::REQUIRED;
            }

            return new AgeVerificationRequirementDetails(
                ageVerificationRequirement:       $ageVerificationRequirement,
                ageVerificationRestrictedCountry: $matchingRestrictedCountry,
                ageVerificationRestrictedRegion:  $matchingRestrictedRegion,
                geolocationLookup:                $geolocationLookup,
                nextUrl:                          $platformUser === null ? null : 'https://' . $platformUser->getActiveDomain() . '/',
            );
        }

        //
        //  Fallback to an age verification requirement of not required
        //
        return new AgeVerificationRequirementDetails(
            ageVerificationRequirement: AgeVerificationRequirement::NOT_REQUIRED,
            geolocationLookup:          $geolocationLookup,
        );
    }

    /**
     * Return an array of all restricted countries
     *
     * @return AgeVerificationRestrictedCountry[] Age verification restrictions by country
     */
    private function getRestrictedCountries(): array
    {
        return [
            new AgeVerificationRestrictedCountry(
                code:    'FR',
                regions: [
                    new AgeVerificationRestrictedRegion(
                        code:                AgeVerificationRestrictedRegion::CODE_FOR_WHOLE_COUNTRY,
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-07-14 18:00:00'),
                        avsRegulationCode:   'FR ALL 2025',
                    ),
                ],
            ),
            new AgeVerificationRestrictedCountry(
                code:    'GB',
                regions: [
                    new AgeVerificationRestrictedRegion(
                        code:                AgeVerificationRestrictedRegion::CODE_FOR_WHOLE_COUNTRY,
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-06-30 19:00:00'),
                        avsRegulationCode:   'GB ALL 2025',
                    ),
                ],
            ),
            new AgeVerificationRestrictedCountry(
                code:    'SE',
                regions: [
                    new AgeVerificationRestrictedRegion(
                        code:                AgeVerificationRestrictedRegion::CODE_FOR_WHOLE_COUNTRY,
                        active:              true,
                        blocked:             true,
                        creditCardCheckDate: new DateTime('2025-06-30 18:00:00'),
                        avsRegulationCode:   'SE ALL 2025',
                    ),
                ],
            ),
            new AgeVerificationRestrictedCountry(
                code:    'US',
                regions: [
                    new AgeVerificationRestrictedRegion(
                        code:                'AL',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US AL 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'AR',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US AR 2023',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'AZ',
                        active:              new DateTime('2025-09-26 2:59:59'), // Arizona (12AM PST)
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-09-26 03:00:00'),
                        avsRegulationCode:   'US AZ 2025',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'FL',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-15 00:00:00'),
                        avsRegulationCode:   'US FL 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'GA',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-07-01 00:00:00'),
                        avsRegulationCode:   'US GA 2025',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'ID',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US ID 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'IN',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US IN 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'KS',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US KS 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'KY',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US KY 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'LA',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US LA 2023',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'MO',
                        active:              new DateTime('2025-11-30 00:59:59'),
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-11-30 01:00:00'),
                        avsRegulationCode:   'US MO 2025',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'MS',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US MS 2023',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'MT',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US MT 2023',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'NC',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US NC 2023',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'ND',
                        active:              new DateTime('2025-08-01 00:59:59'), // North Dakota (CDT) (10PM PST)
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-08-01 01:00:00'),
                        avsRegulationCode:   'US ND 2025',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'NE',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US NE 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'OH',
                        active:              new DateTime('2025-09-28 23:59:59'),
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-09-29 00:00:00'),
                        avsRegulationCode:   'US OH 2025',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'OK',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US OK 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'SC',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US SC 2024',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'SD',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-07-01 01:00:00'),
                        avsRegulationCode:   'US SD 2025',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'TN',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US TN 2025',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'TX',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US TX 2023',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'UT',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US UT 2023',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'VA',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-01-01 00:00:00'),
                        avsRegulationCode:   'US VA 2023',
                    ),
                    new AgeVerificationRestrictedRegion(
                        code:                'WY',
                        active:              true,
                        blocked:             false,
                        creditCardCheckDate: new DateTime('2025-07-01 02:00:00'),
                        avsRegulationCode:   'US WY 2025',
                    ),
                ],
            ),
        ];
    }
}
