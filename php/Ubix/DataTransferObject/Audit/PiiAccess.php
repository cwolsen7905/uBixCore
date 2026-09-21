<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Audit;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * One access to personal data, as `docs/standards/sensitive-data-access.md` records it
 *
 * `entityType` is a host-defined string, not a framework enum. The standard
 * enumerates one product's subject domains, and a framework that names a
 * product's nouns cannot be used by any other product. Each host decides its
 * own values (a membership product might use `supporter` and `creator`).
 *
 * @see \Ubix\Tests\DataTransferObject\Audit\PiiAccessTest PHPUnit test case
 */
final readonly class PiiAccess implements Dto
{
    /**
     * Constructor
     *
     * @param int             $actorId    The operator who accessed the data
     * @param string          $entityType The subject domain, host-defined, at most 32 characters
     * @param array<int, int> $subjectIds Every subject whose data the access returned
     * @param string          $reason     Why the access happened (a stable key, not free text)
     * @param ?string         $searchTerm The term or parameters that produced the access, if any
     */
    public function __construct(
        public readonly int $actorId = 0,
        public readonly string $entityType = '',
        public readonly array $subjectIds = [],
        public readonly string $reason = '',
        public readonly ?string $searchTerm = null,
    ) {
    }
}
