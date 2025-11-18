<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubMembership;

use Ubix\Model\FanClubMembership;

/**
 * Interface for writing fan club membership data
 */
interface FanClubMembershipWriterInterface
{
    /**
     * Save a fan club membership
     *
     * @param FanClubMembership $membership The fan club membership to save
     *
     * @return void
     */
    public function save(FanClubMembership $membership): void;
}
