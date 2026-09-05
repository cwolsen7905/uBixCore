<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\MachineCodeReview\MachineCodeReviewTool;

/**
 * Data transfer object for the details of a violation in a file in a machine code review
 *
 * @see \Ubix\Service\MachineCodeReviewService This DTO is used by the machine code review service
 * @see \Ubix\Tests\DataTransferObject\MachineCodeReviewFileViolationTest PHPUnit test case
 */
final readonly class MachineCodeReviewFileViolation implements Dto
{
    /**
     * Constructor
     *
     * @param string                $text           The violation's text
     * @param MachineCodeReviewTool $tool           The violation's tool
     * @param string                $rule           The violation's rule
     * @param bool                  $testFailure    Whether or not the violation is the result of a test failure (optional) (default: false)
     * @param ?int                  $lineNumber     The violation's line number (optional) (default: null)
     * @param ?int                  $columnPosition The violation's column position (optional) (default: null)
     * @param bool                  $autofixable    Whether or not the violation is auto-fixable (optional) (default: false)
     */
    public function __construct(
        public readonly string $text,
        public readonly MachineCodeReviewTool $tool,
        public readonly string $rule,
        public readonly bool $testFailure = false,
        public readonly ?int $lineNumber = null,
        public readonly ?int $columnPosition = null,
        public readonly bool $autofixable = false,
    ) {
    }
}
