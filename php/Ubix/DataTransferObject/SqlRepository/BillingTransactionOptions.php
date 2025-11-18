<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\DataType\Int\PlatformUserId;

/**
 * Data transfer object for the SQL repository options of billing transactions
 *
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\BillingTransactionOptionsTest PHPUnit test case
 */
final readonly class BillingTransactionOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?PlatformUserId $userId   The transaction's user ID (optional) (default: null)
     * @param ?string         $dateTime The date to compare against in 'Y-m-d H:i:s' format (optional) (default: null)
     * @param ?int            $limit    The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?PlatformUserId $userId = null,
        public readonly ?string $dateTime = null,
        public readonly ?int $limit = null,
    ) {
    }
}
