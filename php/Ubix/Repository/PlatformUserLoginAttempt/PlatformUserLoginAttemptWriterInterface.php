<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUserLoginAttempt;

use Ubix\Model\PlatformUserLoginAttempt;

/**
 * Interface for writing platform user login attempt data
 */
interface PlatformUserLoginAttemptWriterInterface
{
    /**
     * Save the platform user login attempt
     *
     * @param PlatformUserLoginAttempt $platformUserLoginAttempt The platform user login attempt to be saved
     *
     * @return void
     */
    public function save(PlatformUserLoginAttempt $platformUserLoginAttempt): void;
}
