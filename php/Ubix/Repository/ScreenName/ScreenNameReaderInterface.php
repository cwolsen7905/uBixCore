<?php

declare(strict_types=1);

namespace Ubix\Repository\ScreenName;

use Ubix\Model\ScreenName;

/**
 * Interface for reading screen name data
 */
interface ScreenNameReaderInterface
{
    /**
     * Get screen name(s) by optiusers ID
     *
     * @param int $optiusersId The optiusers ID
     *
     * @return ScreenName[] An array of matching screen name(s)
     */
    public function getByOptiusersId(int $optiusersId): array;
}
