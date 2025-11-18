<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of platform white labels
 *
 * @see \Ubix\Repository\PlatformWhiteLabel\PlatformWhiteLabelSqlRepository This DTO is used by the platform white label SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PlatformWhiteLabelOptionsTest PHPUnit test case
 */
final readonly class PlatformWhiteLabelOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?string $domain The domain of the white label (optional) (default: null)
     * @param ?int    $limit  The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?string $domain = null,
        public readonly ?int $limit = null,
    ) {
    }
}
