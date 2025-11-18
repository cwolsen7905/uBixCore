<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubMembership;

use Ubix\Model\FanClubMembership;

/**
 * Interface for reading fan club membership data
 */
interface FanClubMembershipReaderInterface
{
    /**
     * Get all fan club memberships by ID
     *
     * @param int $id The membership ID
     *
     * @return FanClubMembership[] An array of fan club membership objects
     */
    public function getById(int $id): array;

    /**
     * Get all fan club memberships by user ID
     *
     * @param int $userId The membership's user ID
     *
     * @return FanClubMembership[] An array of fan club membership objects
     */
    public function getByUserId(int $userId): array;

    /**
     * Get all fan club memberships by performer ID
     *
     * @param int $performerId The membership's performer ID
     *
     * @return FanClubMembership[] An array of fan club membership objects
     */
    public function getByModelId(int $performerId): array;

    /**
     * Get all active fan club memberships by platform user ID and performer ID
     *
     * @param int $userId      The membership's platform user ID
     * @param int $performerId The membership's performer ID
     *
     * @return FanClubMembership[] An array of fan club membership objects
     */
    public function getActiveByUserAndPerformerIds(int $userId, int $performerId): array;
}
