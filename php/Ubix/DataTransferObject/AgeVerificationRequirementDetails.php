<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\AgeVerification\AgeVerificationRequirement;
use Ubix\Model\AgeVerificationRestrictedCountry;
use Ubix\Model\AgeVerificationRestrictedRegion;

/**
 * Data transfer object for the details of an age verification requirement
 *
 * @see \Ubix\Service\AgeVerificationService This DTO is used by age verification service
 * @see \Ubix\Tests\DataTransferObject\AgeVerificationRequirementDetailsTest PHPUnit test case
 */
final readonly class AgeVerificationRequirementDetails implements Dto
{
    /**
     * Constructor
     *
     * @param AgeVerificationRequirement       $ageVerificationRequirement       Age verification requirement
     * @param GeolocationLookup                $geolocationLookup                Geolocation lookup
     * @param AgeVerificationRestrictedCountry $ageVerificationRestrictedCountry Age verification restricted country (optional) (default: null)
     * @param AgeVerificationRestrictedRegion  $ageVerificationRestrictedRegion  Age verification restricted region (optional) (default: null)
     * @param ?string                          $nextUrl                          The next URL to redirect a customer to (optional) (default: null)
     */
    public function __construct(
        public readonly AgeVerificationRequirement $ageVerificationRequirement,
        public readonly GeolocationLookup $geolocationLookup,
        public readonly ?AgeVerificationRestrictedCountry $ageVerificationRestrictedCountry = null,
        public readonly ?AgeVerificationRestrictedRegion $ageVerificationRestrictedRegion = null,
        public readonly ?string $nextUrl = null,
    ) {
    }
}
