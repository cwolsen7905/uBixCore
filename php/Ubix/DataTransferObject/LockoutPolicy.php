<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Account lockout thresholds (env-driven; authentication SRS FR-23)
 *
 * @see \Ubix\Tests\DataTransferObject\LockoutPolicyTest PHPUnit test case
 */
final readonly class LockoutPolicy implements Dto
{
    /**
     * Constructor
     *
     * @param int $threshold Consecutive failed attempts before lockout
     * @param int $minutes   Lockout window length in minutes
     */
    public function __construct(
        public readonly int $threshold = 5,
        public readonly int $minutes = 15,
    ) {
    }
}
