<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\MachineCodeReview\MachineCodeReviewTool;

/**
 * Data transfer object for the details of a test suite in a machine code review
 *
 * @see \Ubix\Service\MachineCodeReviewService This DTO is used by the machine code review service
 * @see \Ubix\Tests\DataTransferObject\MachineCodeReviewTestSuiteTest PHPUnit test case
 */
final readonly class MachineCodeReviewTestSuite implements Dto
{
    /**
     * Constructor
     *
     * @param MachineCodeReviewTool $tool       The tool used for the test suite
     * @param int                   $tests      The tests run in the test suite (optional) (default: 0)
     * @param int                   $assertions The assertions run in the test suite (optional) (default: 0)
     * @param int                   $errors     The errors encountered in the test suite (optional) (default: 0)
     * @param int                   $failures   The failures encountered in the test suite (optional) (default: 0)
     * @param int                   $skipped    The skipped tests in the test suite (optional) (default: 0)
     */
    public function __construct(
        public readonly MachineCodeReviewTool $tool,
        public readonly int $tests = 0,
        public readonly int $assertions = 0,
        public readonly int $errors = 0,
        public readonly int $failures = 0,
        public readonly int $skipped = 0,
    ) {
    }
}
