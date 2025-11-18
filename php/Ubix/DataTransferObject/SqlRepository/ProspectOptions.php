<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use DateTimeInterface;
use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of prospects
 *
 * @see \Ubix\Repository\Prospect\ProspectSqlRepository This DTO is used by the prospect SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\ProspectOptionsTest PHPUnit test case
 */
final readonly class ProspectOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int               $id                The prospect's ID (optional) (default: null)
     * @param ?string            $emailAddress      The prospect's email address (optional) (default: null)
     * @param ?string            $phoneNumber       The prospect's phone number (optional) (default: null)
     * @param ?DateTimeInterface $dateOfBirth       The prospect's date of birth (optional) (default: null)
     * @param ?string            $namePartial       The prospect's name (partial) (optional) (default: null)
     * @param ?string            $studioNamePartial The prospect's studio name (partial) (optional) (default: null)
     * @param ?string            $name              The prospect's name (optional) (default: null)
     * @param ?string            $stageName         The prospect's stage name (optional) (default: null)
     * @param bool               $includeAdminEmail Whether to include the admin email in the query (optional) (default: true)
     * @param ?int               $limit             The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $emailAddress = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?DateTimeInterface $dateOfBirth = null,
        public readonly ?string $namePartial = null,
        public readonly ?string $studioNamePartial = null,
        public readonly ?string $name = null,
        public readonly ?string $stageName = null,
        public readonly bool $includeAdminEmail = true,
        public readonly ?int $limit = null,
    ) {
    }
}
