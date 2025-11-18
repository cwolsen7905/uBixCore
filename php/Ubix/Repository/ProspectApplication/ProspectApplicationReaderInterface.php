<?php

declare(strict_types=1);

namespace Ubix\Repository\ProspectApplication;

use Ubix\Model\ProspectApplication;

/**
 * Interface for reading prospect application data
 */
interface ProspectApplicationReaderInterface
{
    /**
     * Get applications by ID
     *
     * @param int $id The application's ID
     *
     * @return ProspectApplication[] An array of application objects
     */
    public function getById(int $id): array;

    /**
     * Get applications by application ID and ID
     *
     * @param string $applicationId The application's application ID
     * @param ?int   $id            The application's ID (optional) (default: null)
     *
     * @return ProspectApplication[] An array of application objects
     */
    public function getByApplicationId(string $applicationId, ?int $id = null): array;
}
