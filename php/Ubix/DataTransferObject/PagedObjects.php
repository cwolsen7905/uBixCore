<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a paged subset of objects including metadata
 *
 * @see \Ubix\Tests\DataTransferObject\PagedObjectsTest PHPUnit test case
 */
final readonly class PagedObjects implements Dto
{
    /**
     * Constructor
     *
     * @param object[] $objects The array of objects
     * @param int      $offset  The offset from the start of the subset that the objects come from
     * @param int      $total   The total number of objects (not a count of the $objects array, but the total count from the datastore)
     */
    public function __construct(
        public readonly array $objects,
        public readonly int $offset,
        public readonly int $total,
    ) {
    }
}
