<?php

declare(strict_types=1);

namespace Ubix\Repository\Prospect;

use DateTimeInterface;
use Ubix\Model\Prospect;

/**
 * Interface for reading prospect data
 */
interface ProspectReaderInterface
{
    /**
     * Get prospect(s)
     *
     * @param ?string            $emailAddress      The prospect's email address (optional) (default: null)
     * @param ?string            $phoneNumber       The prospect's phone number (optional) (default: null)
     * @param ?DateTimeInterface $dateOfBirth       The prospect's date of birth (optional) (default: null)
     * @param ?string            $name              The prospect's name (optional) (default: null)
     * @param bool               $includeAdminEmail Whether to include admin email addresses (optional) (default: true)
     *
     * @return Prospect[] An array of prospect object(s)
     */
    public function get(?string $emailAddress = null, ?string $phoneNumber = null, ?DateTimeInterface $dateOfBirth = null, ?string $name = null, bool $includeAdminEmail = true): array;

    /**
     * Get prospect(s) by ID
     *
     * @param int $id The prospect's ID
     *
     * @return Prospect[] An array of prospect objects
     */
    public function getById(int $id): array;

    /**
     * Get prospect(s) by stage name
     *
     * @param string $stageName The prospect's stage name
     *
     * @return Prospect[] An array of prospect objects
     */
    public function getByStageName(string $stageName): array;

    /**
     * Get prospect(s) by partial match
     *
     * @param ?string $name       The prospect's name
     * @param ?string $studioName The prospect's studio name
     *
     * @return Prospect[] An array of partially matching prospect object(s)
     */
    public function getPartialMatches(?string $name, ?string $studioName): array;
}
