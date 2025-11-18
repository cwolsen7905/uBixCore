<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of deals
 *
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\DealOptionsTest PHPUnit test case
 */
final readonly class DealOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $affiliateId The deal's affiliate ID (optional) (default: null)
     * @param ?string $mpCode      The deal's MP code (optional) (default: null)
     * @param ?int    $limit       The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $affiliateId = null,
        public readonly ?string $mpCode = null,
        public readonly ?int $limit = null,
    ) {
    }
}
