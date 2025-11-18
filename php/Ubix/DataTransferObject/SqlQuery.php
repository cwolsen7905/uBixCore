<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a SQL query
 *
 * @see \Ubix\Tests\DataTransferObject\SqlQueryTest PHPUnit test case
 */
final readonly class SqlQuery implements Dto
{
    /**
     * Constructor
     *
     * @param string                                    $sql        The SQL query
     * @param array<string, bool|float|int|string|null> $parameters The SQL query's parameters
     */
    public function __construct(
        public readonly string $sql,
        public readonly array $parameters,
    ) {
    }
}
