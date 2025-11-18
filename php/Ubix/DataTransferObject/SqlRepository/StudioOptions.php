<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of studios
 *
 * @see \Ubix\Repository\FanClubComment\BroadcasterSqlRepository This DTO is used by the studio SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\StudioOptionsTest PHPUnit test case
 */
final readonly class StudioOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $adminUserId The studio's admin user ID (optional) (default: null)
     * @param ?int $limit       The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $adminUserId = null,
        public readonly ?int $limit = null,
    ) {
    }
}
