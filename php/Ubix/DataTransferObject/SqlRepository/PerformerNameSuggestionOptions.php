<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\Performer\PerformerNameSuggestionSex;

/**
 * Data transfer object for the SQL repository options of performer name suggestions
 *
 * @see \Ubix\Repository\Performer\PerformerNameSuggestionSqlRepository This DTO is used by the performer name suggestion SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PerformerNameSuggestionOptionsTest PHPUnit test case
 */
final readonly class PerformerNameSuggestionOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int                        $limit       The query's LIMIT value (optional) (default: null)
     * @param ?int                        $limitOffset The query's LIMIT offset (optional) (default: null)
     * @param ?string                     $orderBy     The query's ORDER BY value (optional) (default: null)
     * @param ?PerformerNameSuggestionSex $sex         The performer name suggestion sex (optional) (default: null)
     * @param ?string                     $name        The name (optional) (default: null)
     * @param ?string                     $searchQuery The search query (optional) (default: null)
     * @param ?int                        $status      The status column (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $limitOffset = null,
        public readonly ?string $orderBy = null,
        public readonly ?PerformerNameSuggestionSex $sex = null,
        public readonly ?string $name = null,
        public readonly ?string $searchQuery = null,
        public readonly ?int $status = null,
    ) {
    }
}
