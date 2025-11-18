<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of prospect images
 *
 * @see \Ubix\Repository\ProspectImage\ProspectImageSqlRepository This DTO is used by the prospect application SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\ProspectImageOptionsTest PHPUnit test case
 */
final readonly class ProspectImageOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $prospectId The prospect's ID (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $prospectId = null,
    ) {
    }
}
