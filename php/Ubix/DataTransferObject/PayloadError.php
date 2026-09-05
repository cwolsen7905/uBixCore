<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the details of a PDO error
 *
 * @see \Ubix\Tests\DataTransferObject\PayloadErrorTest PHPUnit test case
 */
final readonly class PayloadError implements Dto
{
    /**
     * Constructor
     *
     * @param ?int                                       $count  Number of errors (optional) (default: null)
     * @param ?array<array{name: string, error: string}> $errors Array of field errors (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $count = null,
        public readonly ?array $errors = null,
    ) {
    }
}
