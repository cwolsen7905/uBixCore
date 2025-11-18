<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUser;

use Ubix\Model\PlatformUser;

/**
 * Interface for writing platform user data
 */
interface PlatformUserWriterInterface
{
    /**
     * Save the platform user
     *
     * @param PlatformUser $platformUser The platform user to be saved
     *
     * @return void
     */
    public function save(PlatformUser $platformUser): void;
}
