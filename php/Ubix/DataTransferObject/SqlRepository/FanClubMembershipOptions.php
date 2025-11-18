<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of fan club memberships
 *
 * @see \Ubix\Repository\FanClubMembership\FanClubMembershipSqlRepository This DTO is used by the fan club membership SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\FanClubMembershipOptionsTest PHPUnit test case
 */
final readonly class FanClubMembershipOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?bool                $endDateInFuture Filter for memberships with an end date in the future (optional) (default: null)
     * @param ?int                 $id              The membership's ID (optional) (default: null)
     * @param ?int                 $limit           The query's LIMIT value (optional) (default: null)
     * @param ?int                 $modelId         The membership's performer's ID (optional) (default: null)
     * @param string|string[]|null $status          The membership status(es) (optional) (default: null)
     * @param ?int                 $userId          The membership's user's ID (optional) (default: null)
     */
    public function __construct(
        public readonly ?bool $endDateInFuture = null,
        public readonly ?int $id = null,
        public readonly ?int $limit = null,
        public readonly ?int $modelId = null,
        public readonly string|array|null $status = null,
        public readonly ?int $userId = null,
    ) {
    }
}
