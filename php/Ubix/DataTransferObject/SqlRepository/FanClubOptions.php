<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of fan clubs
 *
 * @see \Ubix\Repository\FanClub\FanClubSqlRepository This DTO is used by the fan club SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\FanClubOptionsTest PHPUnit test case
 */
final readonly class FanClubOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $modelId The fan club's performer's ID (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $modelId = null,
    ) {
    }
}
