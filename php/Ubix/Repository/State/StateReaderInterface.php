<?php

declare(strict_types=1);

namespace Ubix\Repository\State;

use Ubix\Model\State;

/**
 * Interface for reading state data
 */
interface StateReaderInterface
{
    /**
     * Get state(s) by name
     *
     * @param string $name The state's name
     *
     * @return State[] An array of matching state(s)
     */
    public function getByName(string $name): array;

    /**
     * Get state(s) by ISO 3166-2 code (two letter country code then a dash then two letter state code)
     *
     * @param string $iso31662 The state's ISO 3166-2 code (two letter country code then a dash then two letter state code)
     *
     * @return State[] An array of matching state(s)
     */
    public function getByIso31662(string $iso31662): array;
}
