<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of performer statistics
 *
 * @see \Ubix\Repository\PerformerStatistics\PerformerStatisticsSqlRepository This DTO is used by the [erformer statistics SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PerformerStatisticsOptionsTest PHPUnit test case
 */
final readonly class PerformerStatisticsOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $performerId The performer's ID (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $performerId = null,
    ) {
    }
}
