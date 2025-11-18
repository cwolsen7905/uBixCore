<?php

declare(strict_types=1);

namespace Ubix\Repository\Studio;

use Ubix\Model\Studio;

/**
 * Interface for reading Studio data
 */
interface StudioReaderInterface
{
    /**
     * Get studios by admin user ID
     *
     * @param int $adminUserId The studio's admin user ID
     *
     * @return Studio[] An array of studio objects
     */
    public function getByAdminUserId(int $adminUserId): array;

    /**
     * Get a count of studios by admin user ID
     *
     * @param int $adminUserId The studio's admin user ID
     *
     * @return int The count of studios
     */
    public function getCountByAdminUserId(int $adminUserId): int;
}
