<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\Country;
use Ubix\Model\State;
use Ubix\Repository\Country\CountryReaderInterface as CountryReader;
use Ubix\Repository\State\StateReaderInterface as StateReader;

/**
 * Service to access location data
 *
 * @see \Ubix\Tests\Service\LocationServiceTest PHPUnit test case
 */
final class LocationService
{
    /**
     * Constructor.
     *
     * @param Logger        $logger        Logger
     * @param CountryReader $countryReader Country reader
     * @param StateReader   $stateReader   State reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private CountryReader $countryReader,
        private StateReader $stateReader,
    ) {
    }

    /**
     * Get a country by name
     *
     * @param string $name The name of the country
     *
     * @throws Exception If no country is found with the specified name
     *
     * @return Country The country with matching name
     */
    public function getCountryByName(string $name): Country
    {
        $countries = $this->countryReader->getByName($name);
        if (count($countries) > 0) {
            return $countries[0];
        } else {
            throw new Exception('No country found with the name `' . $name . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_COUNTRY_NAME->value);
        }
    }

    /**
     * Get a country by ISO 3166-1 alpha-2 code (two letter code)
     *
     * @param string $iso31661Alpha2 The ISO 3166-1 alpha-2 code (two letter code) of the country
     *
     * @throws Exception If no country is found with the specified ISO 3166-1 alpha-2 code (two letter code)
     *
     * @return Country The country with matching ISO 3166-1 alpha-2 code (two letter code)
     */
    public function getCountryByIso31661Alpha2(string $iso31661Alpha2): Country
    {
        $countries = $this->countryReader->getByIso31661Alpha2($iso31661Alpha2);
        if (count($countries) > 0) {
            return $countries[0];
        } else {
            throw new Exception('No country found with the ISO 3166-1 alpha-2 code `' . $iso31661Alpha2 . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_COUNTRY_ISO31661_ALPHA2->value);
        }
    }

    /**
     * Get a country by ISO 3166-1 alpha-3 code (three letter code)
     *
     * @param string $iso31661Alpha3 The ISO 3166-1 alpha-3 code (three letter code) of the country
     *
     * @throws Exception If no country is found with the specified ISO 3166-1 alpha-3 code (three letter code)
     *
     * @return Country The country with matching ISO 3166-1 alpha-3 code (three letter code)
     */
    public function getCountryByIso31661Alpha3(string $iso31661Alpha3): Country
    {
        $countries = $this->countryReader->getByIso31661Alpha3($iso31661Alpha3);
        if (count($countries) > 0) {
            return $countries[0];
        } else {
            throw new Exception('No country found with the ISO 3166-1 alpha-3 code `' . $iso31661Alpha3 . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_COUNTRY_ISO31661_ALPHA3->value);
        }
    }

    /**
     * Get a state by name
     *
     * @param string $name The name of the state
     *
     * @throws Exception If no state is found with the specified name
     *
     * @return State The state with matching name
     */
    public function getStateByName(string $name): State
    {
        $states = $this->stateReader->getByName($name);
        if (count($states) > 0) {
            return $states[0];
        } else {
            throw new Exception('No state found with the name `' . $name . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_STATE_NAME->value);
        }
    }

    /**
     * Get a state by ISO 3166-2 code (two letter country code then a dash then two letter state code)
     *
     * @param string $iso31662 The ISO 3166-2 code (two letter country code then a dash then two letter state code) of the state
     *
     * @throws Exception If no state is found with the specified ISO 3166-2 code (two letter country code then a dash then two letter state code)
     *
     * @return State The state with matching ISO 3166-2 code (two letter country code then a dash then two letter state code)
     */
    public function getStateByIso31662(string $iso31662): State
    {
        $states = $this->stateReader->getByIso31662($iso31662);
        if (count($states) > 0) {
            return $states[0];
        } else {
            throw new Exception('No state found with the ISO 3166-2 code `' . $iso31662 . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_STATE_ISO31662->value);
        }
    }
}
