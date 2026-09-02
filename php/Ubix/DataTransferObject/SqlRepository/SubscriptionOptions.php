<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Options for querying subscriptions
 *
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\SubscriptionOptionsTest PHPUnit test case
 */
final readonly class SubscriptionOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $id              Restrict to one subscription id
     * @param ?int $userId          Restrict to one supporter
     * @param ?int $creatorId       Restrict to one creator
     * @param bool $nonTerminalOnly Only active / past_due rows (FR-302 uniqueness scope)
     * @param ?int $limit           Maximum number of rows
     * @param ?int $offset          Offset for paged reads
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $userId = null,
        public readonly ?int $creatorId = null,
        public readonly bool $nonTerminalOnly = false,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
