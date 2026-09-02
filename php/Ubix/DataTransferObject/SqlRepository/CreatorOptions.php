<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of creators
 *
 * @see \Ubix\Repository\Creator\CreatorSqlRepository This DTO is used by the creator SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\CreatorOptionsTest PHPUnit test case
 */
final readonly class CreatorOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id     The creator's ID (optional) (default: null)
     * @param ?int    $userId The owning user's ID (optional) (default: null)
     * @param ?string $slug   The creator's slug (optional) (default: null)
     * @param ?string $status The lifecycle status value (optional) (default: null)
     * @param ?int    $limit  The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $userId = null,
        public readonly ?string $slug = null,
        public readonly ?string $status = null,
        public readonly ?int $limit = null,
    ) {
    }
}
