<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for process results
 *
 * @see \Ubix\Tests\DataTransferObject\ProcessResultsTest PHPUnit test case
 */
final readonly class ProcessResults implements Dto
{
    /**
     * Constructor
     *
     * @param int    $exitCode     The exit code
     * @param string $stdoutOutput The STDOUT output
     * @param string $stderrOutput The STDERR output
     */
    public function __construct(
        public readonly int $exitCode,
        public readonly string $stdoutOutput,
        public readonly string $stderrOutput,
    ) {
    }
}
