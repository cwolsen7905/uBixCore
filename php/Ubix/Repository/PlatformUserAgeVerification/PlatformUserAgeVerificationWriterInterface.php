<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUserAgeVerification;

use Ubix\Model\PlatformUserAgeVerification;

/**
 * Interface for writing platform user age verification data
 */
interface PlatformUserAgeVerificationWriterInterface
{
    /**
     * Save a platform user age verification
     *
     * @param PlatformUserAgeVerification $platformUserAgeVerification The platform user age verification to save
     *
     * @return void
     */
    public function save(PlatformUserAgeVerification $platformUserAgeVerification): void;
}
