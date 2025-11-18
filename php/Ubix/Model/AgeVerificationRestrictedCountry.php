<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\Model\AbstractModel as Model;

/**
 * Model of an age verification restricted country
 *
 * @see \Ubix\Tests\Model\AgeVerificationRestrictedCountryTest PHPUnit test case
 */
final class AgeVerificationRestrictedCountry extends Model
{
    /**
     * Constructor
     *
     * @param ?string                            $code    The country code (optional) (default: null)
     * @param ?AgeVerificationRestrictedRegion[] $regions The age verification restrictions for the country's regions (optional) (default: null)
     */
    public function __construct(
        private ?string $code = null,
        private ?array $regions = null,
    ) {
    }

    /**
     * Get a matching region by region code
     *
     * @param ?string $regionCode The region code to match (optional) (default: null)
     *
     * @return ?AgeVerificationRestrictedRegion The matching restricted region if found otherwise null
     */
    public function getMatchingRegion(?string $regionCode = null): ?AgeVerificationRestrictedRegion
    {
        $wholeCountry = null;

        //
        //  Loop through the country's regions
        //
        if ($this->getRegions() !== null) {
            foreach ($this->getRegions() as $region) {
                if ($region->getCode() !== null) {
                    switch ($region->getCode()) {
                        case $regionCode:
                            return $region;

                        case AgeVerificationRestrictedRegion::CODE_FOR_WHOLE_COUNTRY:
                            $wholeCountry = $region;
                            break;
                    }
                }
            }
        }

        //
        //  If we got this far then return the whole country match (even if it's null)
        //
        return $wholeCountry;
    }

    /**
     * Get the value of code
     *
     * @return ?string The value of code
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @param ?string $code The value for code
     *
     * @return void
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get the value of regions
     *
     * @return ?AgeVerificationRestrictedRegion[] The value of regions
     */
    public function getRegions(): ?array
    {
        return $this->regions;
    }

    /**
     * Set the value of regions
     *
     * @param ?AgeVerificationRestrictedRegion[] $regions The value for regions
     *
     * @return void
     */
    public function setRegions(?array $regions): void
    {
        $this->regions = $regions;
    }
}
