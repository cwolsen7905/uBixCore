<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of screen names
 *
 * @see \Ubix\Repository\ScreenName\ScreenNameSqlRepository This DTO is used by the screen SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\ScreenNameOptionsTest PHPUnit test case
 */
final readonly class ScreenNameOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int           $optiusersId The screen name's platform user ID (optional) (default: null)
     * @param int|int[]|null $status      The screen name's status (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $optiusersId = null,
        public readonly int|array|null $status = null,
    ) {
    }
}
