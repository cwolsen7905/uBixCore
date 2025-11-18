<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of fan club transactions
 *
 * @see \Ubix\Repository\FanClubTransaction\FanClubTransactionSqlRepository This DTO is used by the fan club transaction SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\FanClubTransactionOptionsTest PHPUnit test case
 */
final readonly class FanClubTransactionOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $id      The transaction's ID (optional) (default: null)
     * @param ?int $modelId The transaction's performer's ID (optional) (default: null)
     * @param ?int $userId  The transaction's user's ID (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $modelId = null,
        public readonly ?int $userId = null,
    ) {
    }
}
