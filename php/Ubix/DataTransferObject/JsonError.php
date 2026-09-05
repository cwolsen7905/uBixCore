<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the details of a PDO error
 *
 * @see \Ubix\Service\JsonService This DTO is used by the JSON service
 * @see \Ubix\Tests\DataTransferObject\JsonErrorTest PHPUnit test case
 */
final readonly class JsonError implements Dto
{
    /**
     * Constructor
     *
     * @param int    $code    The error code
     * @param string $message The error message
     * @param string $method  The method that triggered the error
     * @param string $input   The input that triggered the error
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly string $method,
        public readonly string $input,
    ) {
    }
}
