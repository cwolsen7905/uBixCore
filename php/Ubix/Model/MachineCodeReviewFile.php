<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\DataTransferObject\MachineCodeReviewFileViolation;
use Ubix\DataTransferObject\MachineCodeReviewTestSuite;
use Ubix\Enum\MachineCodeReview\MachineCodeReviewTool;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a file in a machine code review
 *
 * @see \Ubix\Tests\Model\MachineCodeReviewFileTest PHPUnit test case
 */
final class MachineCodeReviewFile extends Model
{
    /**
     * Constructor
     *
     * @param string                           $path       The file's path
     * @param string                           $md5Hash    The file's MD5 Hash
     * @param MachineCodeReviewTestSuite[]     $testSuites The file's test suites
     * @param MachineCodeReviewTool[]          $tools      The file's tools
     * @param MachineCodeReviewFileViolation[] $violations The file's violations
     */
    public function __construct(
        private ?string $path = null,
        private ?string $md5Hash = null,
        private ?array $testSuites = null,
        private ?array $tools = null,
        private ?array $violations = null,
    ) {
    }

    /**
     * Add a test suite to the file
     *
     * @param MachineCodeReviewTestSuite $testSuite The test suite
     *
     * @return void
     */
    public function addTestSuite(MachineCodeReviewTestSuite $testSuite): void
    {
        //
        //  Ensure the testSuites array is initialized
        //
        if ($this->testSuites === null) {
            $this->testSuites = [];
        }

        //
        //  Add the test suite's tool to the file's tools array
        //
        $this->addTool($testSuite->tool);

        //
        //  Add the test suite to the testSuites array
        //
        $this->testSuites[] = $testSuite;
    }

    /**
     * Add a tool to the file
     *
     * @param MachineCodeReviewTool $tool The tool
     *
     * @return void
     */
    public function addTool(MachineCodeReviewTool $tool): void
    {
        //
        //  Ensure the tools array is initialized
        //
        if ($this->tools === null) {
            $this->tools = [];
        }

        //
        //  Add the tool to the tools array
        //
        if (!in_array($tool, $this->tools, true)) {
            $this->tools[] = $tool;
        }
    }

    /**
     * Add a violation to the file
     *
     * @param MachineCodeReviewFileViolation $violation The violation
     *
     * @return void
     */
    public function addViolation(MachineCodeReviewFileViolation $violation): void
    {
        //
        //  Ensure the violations array is initialized
        //
        if ($this->violations === null) {
            $this->violations = [];
        }

        //
        //  Add the violation's tool to the file's tools array
        //
        $this->addTool($violation->tool);

        //
        //  Check for a duplicate
        //
        $found = false;

        if ($this->violations !== null) {
            foreach ($this->violations as $existingViolation) {
                if (serialize($existingViolation) === serialize($violation)) {
                    //
                    //  If a matching violation is found break out of the foreach loop
                    //
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            //
            //  If no matching violation is found then add it to the violations array
            //
            $this->violations[] = $violation;

            //
            //  Sort the violations by line number then column position
            //
            usort($this->violations, function ($a, $b) {
                return $a->lineNumber <=> $b->lineNumber ?: $a->columnPosition <=> $b->columnPosition;
            });
        }
    }

    /**
     * Get the value of path
     *
     * @return ?string The value of path
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @param ?string $path The value for path
     *
     * @return void
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * Get the value of md5Hash
     *
     * @return ?string The value of md5Hash
     */
    public function getMd5Hash(): ?string
    {
        return $this->md5Hash;
    }

    /**
     * Set the value of md5Hash
     *
     * @param ?string $md5Hash The value for md5Hash
     *
     * @return void
     */
    public function setMd5Hash(?string $md5Hash): void
    {
        $this->md5Hash = $md5Hash;
    }

    /**
     * Get the value of testSuites
     *
     * @return ?MachineCodeReviewTestSuite[] The value of testSuites
     */
    public function getTestSuites(): ?array
    {
        return $this->testSuites;
    }

    /**
     * Set the value of testSuites
     *
     * @param ?MachineCodeReviewTestSuite[] $testSuites The value for testSuites
     *
     * @return void
     */
    public function setTestSuites(?array $testSuites): void
    {
        $this->testSuites = $testSuites;
    }

    /**
     * Get the value of tools
     *
     * @return ?MachineCodeReviewTool[] The value of tools
     */
    public function getTools(): ?array
    {
        return $this->tools;
    }

    /**
     * Set the value of tools
     *
     * @param ?MachineCodeReviewTool[] $tools The value for tools
     *
     * @return void
     */
    public function setTools(?array $tools): void
    {
        $this->tools = $tools;
    }

    /**
     * Get the value of violations
     *
     * @return ?MachineCodeReviewFileViolation[] The value of violations
     */
    public function getViolations(): ?array
    {
        return $this->violations;
    }

    /**
     * Set the value of violations
     *
     * @param ?MachineCodeReviewFileViolation[] $violations The value for violations
     *
     * @return void
     */
    public function setViolations(?array $violations): void
    {
        $this->violations = $violations;
    }
}
