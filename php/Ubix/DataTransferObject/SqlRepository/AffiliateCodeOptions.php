<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of affiliate codes
 *
 * @see \Ubix\Repository\AffiliateCode\AffiliateCodeSqlRepository This DTO is used by the affiliate SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\AffiliateCodeOptionsTest PHPUnit test case
 */
final readonly class AffiliateCodeOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $id    The affiliate code's ID (optional) (default: null)
     * @param ?int $limit The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $limit = null,
    ) {
    }
}
