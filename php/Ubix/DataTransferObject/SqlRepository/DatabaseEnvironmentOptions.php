<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Options DTO for `DatabaseEnvironmentSqlRepository`
 *
 * The table holds one row; the only choice is whether a missing table is an
 * answer ("unlabelled") or an error.
 *
 * @see \Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentSqlRepository This DTO is used by the database environment SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\DatabaseEnvironmentOptionsTest PHPUnit test case
 */
final readonly class DatabaseEnvironmentOptions implements Dto
{
    /**
     * Constructor
     *
     * @param bool $missingIsUnlabelled Treat a missing table or SYSTEMS database as "no label"
     */
    public function __construct(
        public bool $missingIsUnlabelled = true,
    ) {
    }
}
