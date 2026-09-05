<?php

declare(strict_types=1);

namespace Ubix\Tests;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * Abstract class for the test that proves every concrete class and enum in a code tree has a PHPUnit test case
 *
 * Ships with uBixCore so a host project enforces the same rule on its own tree:
 * extend it, return the host's code directory / namespace and tests directory /
 * namespace, and call `assertEveryConcreteClassAndEnumHasATestCase()` from a
 * test method. `<code dir>/X/Y.php` (class `<ns>\X\Y`) must have
 * `<tests dir>/X/YTest.php` (class `<tests ns>\X\YTest`) extending the uBix
 * concrete-class test case and implementing its interface.
 */
abstract class AbstractPhpunitTestCasesTestCase extends TestCase
{
    /**
     * Get the directory holding the code under test, e.g. `<root>/php/Ubix`
     *
     * @return string
     */
    abstract protected function getCodeDirectory(): string;

    /**
     * Get the namespace mapped to that directory, e.g. `Ubix`
     *
     * @return string
     */
    abstract protected function getCodeNamespace(): string;

    /**
     * Get the directory holding the test cases, e.g. `<root>/tests`
     *
     * @return string
     */
    abstract protected function getTestsDirectory(): string;

    /**
     * Get the namespace mapped to the tests directory, e.g. `Ubix\Tests`
     *
     * @return string
     */
    abstract protected function getTestsNamespace(): string;

    /**
     * Get namespace prefixes (with trailing backslash) whose classes are not required to have test cases
     *
     * @return string[]
     */
    protected function getExemptNamespaces(): array
    {
        return [];
    }

    /**
     * Get fully-qualified class names that are not required to have test cases
     *
     * @return string[]
     */
    protected function getExemptClasses(): array
    {
        return [];
    }

    /**
     * Assert that every concrete class and enum under the code directory has a matching test case
     *
     * @return void
     */
    protected function assertEveryConcreteClassAndEnumHasATestCase(): void
    {
        $codeDirectory = realpath($this->getCodeDirectory());
        if ($codeDirectory === false) {
            $this->fail('Code directory `' . $this->getCodeDirectory() . '` not found');
        }
        $codeNamespace  = trim($this->getCodeNamespace(), '\\');
        $testsDirectory = rtrim($this->getTestsDirectory(), '/');
        $testsNamespace = trim($this->getTestsNamespace(), '\\');

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($codeDirectory));
        foreach ($iterator as $file) {
            if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $realPath = $file->getRealPath();
            if ($realPath === false) {
                continue;
            }

            $relative      = substr($realPath, strlen($codeDirectory) + 1, -4); // E.g. Service/JsonService
            $class         = $codeNamespace . '\\' . strtr($relative, ['/' => '\\']);
            $testCaseClass = $testsNamespace . '\\' . strtr($relative, ['/' => '\\']) . 'Test';
            $testCaseFile  = $testsDirectory . '/' . $relative . 'Test.php';

            if (in_array($class, $this->getExemptClasses(), true)) {
                continue;
            }
            $exempt = false;
            foreach ($this->getExemptNamespaces() as $namespace) {
                if (str_starts_with($class, $namespace)) {
                    $exempt = true;
                    break;
                }
            }
            if ($exempt) {
                continue;
            }

            $this->assertTrue(
                class_exists($class) || enum_exists($class) || interface_exists($class),
                'The `' . $class . '` class must exist in the `' . $realPath . '` file but does not',
            );

            $reflection = new ReflectionClass($class);
            if ($reflection->isAbstract() || $reflection->isInterface()) { // Only concrete classes and enums need test cases
                continue;
            }

            $this->assertTrue(
                file_exists($testCaseFile),
                'The `' . $reflection->getName() . '` class must have a corresponding PHPUnit test case file at `' . $testCaseFile . '` but does not',
            );

            if (!class_exists($testCaseClass)) {
                $this->assertTrue(
                    class_exists($testCaseClass),
                    'The `' . $reflection->getName() . '` class must have a corresponding PHPUnit test case class called `' . $testCaseClass . '` but does not',
                );
            } else {
                $testCaseReflection = new ReflectionClass($testCaseClass);
                $this->assertTrue(
                    $testCaseReflection->getParentClass() !== false && $testCaseReflection->getParentClass()->getName() === UbixConcreteClassOrEnumTestCase::class,
                    'The `' . $testCaseReflection->getName() . '` class must extend the `' . UbixConcreteClassOrEnumTestCase::class . '` abstract class but does not',
                );
                $this->assertTrue(
                    $testCaseReflection->implementsInterface(IUbixConcreteClassOrEnumTestCase::class),
                    'The `' . $testCaseReflection->getName() . '` class must implement the `' . IUbixConcreteClassOrEnumTestCase::class . '` interface but does not',
                );
            }
        }
    }
}
