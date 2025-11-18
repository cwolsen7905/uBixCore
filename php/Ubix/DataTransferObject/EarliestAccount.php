<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Model\PlatformUser;

/**
 * Data transfer object for an earliest account
 *
 * @see \Ubix\Tests\DataTransferObject\EarliestAccountTest PHPUnit test case
 */
final readonly class EarliestAccount implements Dto
{
    /**
     * Constructor
     *
     * @param ?PlatformUser $user   The platform user object
     * @param ?string       $reason The reason for selection (optional) (default: null)
     */
    public function __construct(
        public readonly ?PlatformUser $user = null,
        public readonly ?string $reason = null,
    ) {
    }
}
