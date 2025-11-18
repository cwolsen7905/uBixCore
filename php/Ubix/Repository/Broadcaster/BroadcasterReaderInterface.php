<?php

declare(strict_types=1);

namespace Ubix\Repository\Broadcaster;

use Ubix\Model\Broadcaster;

/**
 * Interface for reading broadcaster data
 */
interface BroadcasterReaderInterface
{
    /**
     * Get broadcasters by ID
     *
     * @param int $id The broadcaster's ID
     *
     * @return Broadcaster[] An array of broadcaster objects
     */
    public function getById(int $id): array;

    /**
     * Get broadcaster(s) by partial match
     *
     * @param string $name       The broadcaster's name
     * @param string $studioName The broadcaster's studio name
     *
     * @return Broadcaster[] An array of partially matching broadcaster object(s)
     */
    public function getPartialMatches(string $name, string $studioName): array;

    /**
     * Get a count of broadcasters by ID
     *
     * @param int $id The broadcaster's ID
     *
     * @return int The count of broadcasters
     */
    public function getCountById(int $id): int;
}
