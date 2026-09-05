<?php

declare(strict_types=1);

namespace Ubix\Model;

use Exception;
use Ubix\DataTransferObject\MachineCodeReviewFileViolation;
use Ubix\DataTransferObject\MachineCodeReviewTestSuite;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\MachineCodeReview\MachineCodeReviewTool;
use Ubix\Model\AbstractModel as Model;
use Ubix\Service\MachineCodeReviewService;

/**
 * Model of a machine code review
 *
 * @see \Ubix\Tests\Model\MachineCodeReviewTest PHPUnit test case
 */
final class MachineCodeReview extends Model
{
    /**
     * Constructor
     *
     * @param ?MachineCodeReviewFile[] $files The machine code review's files
     */
    public function __construct(
        private ?array $files = null,
    ) {
    }

    /**
     * Convert the machine code review to plaintext
     *
     * @return string The machine code review as plaintext
     */
    public function toPlaintext(): string
    {
        $plaintext = '';

        foreach ($this->files ?? [] as $file) {
            if (count($file->getViolations() ?? []) > 0) {
                $plaintext .= $file->getPath() . PHP_EOL;

                foreach ($file->getViolations() as $violation) {
                    $plaintext .= $violation->lineNumber !== null ? 'Line ' . $violation->lineNumber : MachineCodeReviewService::NO_LINE_NUMBER_TEXT;
                    $plaintext .= ': ';
                    $plaintext .= $violation->text . ' (' . $violation->rule . ')';
                    $plaintext .= PHP_EOL;
                }

                $plaintext .= PHP_EOL;
            }
        }

        return trim($plaintext);
    }

    /**
     * Get the number of assertions that were run as part of test suites
     *
     * @param ?MachineCodeReviewTool $tool If not null then only count results from this tool (optional) (default: null)
     *
     * @return int The number of assertions that were run as part of test suites
     */
    public function getAssertionsCount(?MachineCodeReviewTool $tool = null): int
    {
        return $this->getTestSuiteResultsCount('assertions', $tool);
    }

    /**
     * Get the number of errors that were present as part of test suites
     *
     * @param ?MachineCodeReviewTool $tool If not null then only count results from this tool (optional) (default: null)
     *
     * @return int The number of errors that were present as part of test suites
     */
    public function getErrorsCount(?MachineCodeReviewTool $tool = null): int
    {
        return $this->getTestSuiteResultsCount('errors', $tool);
    }

    /**
     * Get the number of failures that were present as part of test suites
     *
     * @param ?MachineCodeReviewTool $tool If not null then only count results from this tool (optional) (default: null)
     *
     * @return int The number of failures that were present as part of test suites
     */
    public function getFailuresCount(?MachineCodeReviewTool $tool = null): int
    {
        return $this->getTestSuiteResultsCount('failures', $tool);
    }

    /**
     * Get the number of tests that were run as part of test suites
     *
     * @param ?MachineCodeReviewTool $tool If not null then only count results from this tool (optional) (default: null)
     *
     * @return int The number of tests that were run as part of test suites
     */
    public function getTestsCount(?MachineCodeReviewTool $tool = null): int
    {
        return $this->getTestSuiteResultsCount('tests', $tool);
    }

    /**
     * Add a test suite to the machine code review
     *
     * @param string                $path The file's path
     * @param MachineCodeReviewTool $tool The tool to add
     *
     * @return void
     */
    public function addTool(string $path, MachineCodeReviewTool $tool): void
    {
        $filePosition = $this->getFilePosition($path);
        if (isset($this->files[$filePosition])) {
            $this->files[$filePosition]->addTool($tool);
        }
    }

    /**
     * Add a test suite to the machine code review
     *
     * @param string                     $path      The file's path
     * @param MachineCodeReviewTestSuite $testSuite The test suite to add
     *
     * @return void
     */
    public function addTestSuite(string $path, MachineCodeReviewTestSuite $testSuite): void
    {
        $filePosition = $this->getFilePosition($path);
        if (isset($this->files[$filePosition])) {
            $this->files[$filePosition]->addTestSuite($testSuite);
        }
    }

    /**
     * Add a file to the machine code review
     *
     * @param string                         $path      The file's path
     * @param MachineCodeReviewFileViolation $violation The violation to add
     *
     * @return void
     */
    public function addViolation(string $path, MachineCodeReviewFileViolation $violation): void
    {
        $filePosition = $this->getFilePosition($path);
        if (isset($this->files[$filePosition])) {
            $this->files[$filePosition]->addViolation($violation);
        }
    }

    /**
     * Merge a new machine code review into this one
     *
     * @param self $new The new machine code review to merge into this one
     *
     * @return void
     */
    public function merge(self $new): void
    {
        foreach ($new->getFiles() ?? [] as $file) {
            if ($file->getPath() !== null) {
                //
                //  Merge tools
                //
                foreach ($file->getTools() ?? [] as $tool) {
                    $this->addTool(
                        path: $file->getPath(),
                        tool: $tool,
                    );
                }

                //
                //  Merge test suites
                //
                foreach ($file->getTestSuites() ?? [] as $testSuite) {
                    $this->addTestSuite(
                        path:      $file->getPath(),
                        testSuite: $testSuite,
                    );
                }

                //
                //  Merge violations
                //
                foreach ($file->getViolations() ?? [] as $violation) {
                    $this->addViolation(
                        path:      $file->getPath(),
                        violation: $violation,
                    );
                }
            }
        }
    }

    /**
     * Get the number of violations in the machine code review
     *
     * @param bool                   $onlyAutofixable Whether or not to only count auto-fixable violations (optional) (default: false)
     * @param ?MachineCodeReviewTool $tool            If not null then only count violations from this tool (optional) (default: null)
     *
     * @return int The number of violations
     */
    public function getViolationsCount(bool $onlyAutofixable = false, ?MachineCodeReviewTool $tool = null): int
    {
        $count = 0;

        foreach ($this->getFiles() ?? [] as $file) {
            foreach ($file->getViolations() ?? [] as $violation) {
                $autofixableMatch = false;
                if ($onlyAutofixable) {
                    if ($violation->autofixable) {
                        $autofixableMatch = true;
                    }
                } else {
                    $autofixableMatch = true;
                }

                $toolMatch = false;
                if ($tool !== null) {
                    if ($violation->tool === $tool) {
                        $toolMatch = true;
                    }
                } else {
                    $toolMatch = true;
                }

                if ($autofixableMatch && $toolMatch) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Get the value of files
     *
     * @return ?MachineCodeReviewFile[] The value of files
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * Set the value of files
     *
     * @param ?MachineCodeReviewFile[] $files The value for files
     *
     * @return void
     */
    public function setFiles(?array $files): void
    {
        $this->files = $files;
    }

    /**
     * Get the tools used in the machine code review
     *
     * @return MachineCodeReviewTool[] The tools used in the machine code review
     */
    public function getTools(): array
    {
        $tools = [];

        foreach ($this->files ?? [] as $file) {
            foreach ($file->getTools() ?? [] as $tool) {
                if (!in_array($tool, $tools, true)) {
                    $tools[] = $tool;
                }
            }
        }

        return $tools;
    }

    /**
     * Get the position of a file in the machine code review by path
     *
     * @param string $path The file's path
     *
     * @throws Exception If the file already exists in the machine code review but with a different MD5 hash
     *
     * @return int The file's position
     */
    private function getFilePosition(string $path): int
    {
        //
        //  Ensure the files array is initialized
        //
        if ($this->files === null) {
            $this->files = [];
        }

        //
        //  Check the existing files for a match
        //
        foreach ($this->files as $i => $existingFile) {
            if ($existingFile->getPath() === $path) {
                if ($this->getMd5HashFromPath($path) !== $existingFile->getMd5Hash()) {
                    throw new Exception(
                        'File `' . $path . '` already exists in the machine code review but with a different MD5 hash',
                        ExceptionCode::MACHINE_CODE_REVIEW_FILE_MD5_HASH_MISMATCH->value,
                    );
                }

                return $i;
            }
        }

        //
        //  If no matching file is found then add it to the files array
        //
        $this->files[] = new MachineCodeReviewFile(
            path:    $path,
            md5Hash: $this->getMd5HashFromPath($path),
        );

        //
        //  Sort the files alphabetically by path
        //
        usort($this->files, function ($a, $b) {
            return $a->getPath() <=> $b->getPath();
        });

        //
        //  Return the position of the newly added file
        //
        return count($this->files) - 1;
    }

    /**
     * Get the MD5 hash of a file from its path
     *
     * @param string $path The path
     *
     * @return ?string The MD5 hash or null if hashing fails or the file doesn't exist
     */
    private function getMd5HashFromPath(string $path): ?string
    {
        return is_file($path) ? (md5_file($path) ?: null) : null;
    }

    /**
     * Get a count of test suite results by type
     *
     * @param string                 $type The type of test suite result to count, e.g. "tests" or "assertions" // NOT_IMPLEMENTED: should be an enum
     * @param ?MachineCodeReviewTool $tool If not null then only count results from this tool (optional) (default: null)
     *
     * @return int The count of test suite results by type
     */
    private function getTestSuiteResultsCount(string $type, ?MachineCodeReviewTool $tool = null): int
    {
        $count = 0;

        foreach ($this->files ?? [] as $file) {
            foreach ($file->getTestSuites() ?? [] as $testSuite) {
                $toolMatch = $tool === null || $tool === $testSuite->tool;
                if ($toolMatch && is_int($testSuite->{$type})) {
                    $count += $testSuite->{$type};
                }
            }
        }

        return $count;
    }
}
