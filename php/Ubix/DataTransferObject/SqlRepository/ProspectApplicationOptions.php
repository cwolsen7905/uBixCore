<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of prospect applications
 *
 * @see \Ubix\Repository\ProspectApplication\ProspectApplicationSqlRepository This DTO is used by the prospect application SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\ProspectApplicationOptionsTest PHPUnit test case
 */
final readonly class ProspectApplicationOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id            The prospect application's ID (optional) (default: null)
     * @param ?string $applicationId The prospect application's application ID (optional) (default: null)
     * @param ?int    $limit         The query's LIMIT value (optional) (default: null)
     * @param ?string $orderBy       The query's ORDER BY value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $applicationId = null,
        public readonly ?int $limit = null,
        public readonly ?string $orderBy = null,
    ) {
    }
}
