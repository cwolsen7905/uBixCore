<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of commission plans
 *
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\CommissionPlanOptionsTest PHPUnit test case
 */
final readonly class CommissionPlanOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $affiliateId The affiliate's ID (optional) (default: null)
     * @param ?int $limit       The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $affiliateId = null,
        public readonly ?int $limit = null,
    ) {
    }
}
