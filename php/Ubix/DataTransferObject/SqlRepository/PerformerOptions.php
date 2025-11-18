<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of performers
 *
 * @see \Ubix\Repository\Performer\PerformerSqlRepository This DTO is used by the performer SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PerformerOptionsTest PHPUnit test case
 */
final readonly class PerformerOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id           The performer's ID (optional) (default: null)
     * @param ?int    $limit        The query's LIMIT value (optional) (default: null)
     * @param ?string $passwordMd5  The performer's password (MD5 hash) (optional) (default: null)
     * @param ?string $emailAddress The performer's email address (optional) (default: null)
     * @param ?string $slug         The performer's slug (optional) (default: null)
     * @param ?string $name         The performer's name (optional) (default: null)
     * @param ?string $namePartial  The performer's name (partial match only) (optional) (default: null)
     * @param ?string $username     The performer's username (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $limit = null,
        public readonly ?string $passwordMd5 = null,
        public readonly ?string $emailAddress = null,
        public readonly ?string $slug = null,
        public readonly ?string $name = null,
        public readonly ?string $namePartial = null,
        public readonly ?string $username = null,
    ) {
    }
}
