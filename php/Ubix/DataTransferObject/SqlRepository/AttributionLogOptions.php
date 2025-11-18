<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of attribution logs
 *
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\AttributionLogOptionsTest PHPUnit test case
 */
final readonly class AttributionLogOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $userId The attribution log's user ID (optional) (default: null)
     * @param ?int $limit  The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $userId = null,
        public readonly ?int $limit = null,
    ) {
    }
}
