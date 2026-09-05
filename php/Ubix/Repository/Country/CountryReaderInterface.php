<?php

declare(strict_types=1);

namespace Ubix\Repository\Country;

use Ubix\Model\Country;

/**
 * Interface for reading country data
 */
interface CountryReaderInterface
{
    /**
     * Get country(/ies) by name
     *
     * @param string $name The country's name
     *
     * @return Country[] An array of matching country(/ies)
     */
    public function getByName(string $name): array;

    /**
     * Get country(/ies) by ISO 3166-1 alpha-2 code (two letter code)
     *
     * @param string $iso31661Alpha2 The country's ISO 3166-1 alpha-2 code (two letter code)
     *
     * @return Country[] An array of matching country(/ies)
     */
    public function getByIso31661Alpha2(string $iso31661Alpha2): array;

    /**
     * Get country(/ies) by ISO 3166-1 alpha-3 code (three letter code)
     *
     * @param string $iso31661Alpha3 The country's ISO 3166-1 alpha-3 code (three letter code)
     *
     * @return Country[] An array of matching country(/ies)
     */
    public function getByIso31661Alpha3(string $iso31661Alpha3): array;
}
