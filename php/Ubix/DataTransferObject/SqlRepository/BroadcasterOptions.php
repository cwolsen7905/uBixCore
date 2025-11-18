<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of broadcasters
 *
 * @see \Ubix\Repository\FanClubComment\BroadcasterSqlRepository This DTO is used by the broadcaster SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\BroadcasterOptionsTest PHPUnit test case
 */
final readonly class BroadcasterOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id                The broadcaster's ID (optional) (default: null)
     * @param ?string $namePartial       The broadcaster's name (partial) (optional) (default: null)
     * @param ?string $studioNamePartial The broadcaster's studio name (partial) (optional) (default: null)
     * @param ?int    $limit             The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $namePartial = null,
        public readonly ?string $studioNamePartial = null,
        public readonly ?int $limit = null,
    ) {
    }
}
