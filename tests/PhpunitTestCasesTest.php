<?php

declare(strict_types=1);

namespace Ubix\Tests;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Ubix\Tests\AbstractTestCase as TestCase;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case to ensure PHPUnit test cases are set up correctly
 */
final class PhpunitTestCasesTest extends TestCase
{
    /**
     * Test that all VSM concrete classes and enums have a corresponding PHPUnit test case
     *
     * @return void
     */
    public function testEveryUbixConcreteClassAndEnumHasAPhpunitTestCase(): void
    {
        $codeDirectory  = __DIR__ . '/../php';
        $testsDirectory = __DIR__;

        //
        //  Ensure we have a real path to avoid string|false issues
        //
        $codeDirectoryRealPath = realpath($codeDirectory);
        if ($codeDirectoryRealPath === false) {
            $this->fail('Code directory `' . $codeDirectory . '` not found');
        }
        $codeDirectory = $codeDirectoryRealPath;

        //
        //  Loop through the code directory
        //
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($codeDirectory));
        foreach ($iterator as $file) {
            if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') { // Ensure the file is a PHP file
                continue;
            }

            $realPath = $file->getRealPath();
            if ($realPath === false) { // Ensure the file has a real path
                continue;
            }

            $relative      = substr($realPath, strlen($codeDirectory) + 1);
            $class         = strtr(substr($relative, 0, -4), ['/' => '\\']);
            $testCaseClass = 'Ubix\\Tests\\' . substr($class, 4) . 'Test';
            $testCaseFile  = $testsDirectory . '/' . substr($relative, 4, -4) . 'Test.php';

            if (str_starts_with($class, 'Ubix\\Filters\\') || str_starts_with($class, 'Ubix\\Sniffs\\')) { // We are not subjecting custom phpcs filters or sniffs to a full machine code review
                continue;
            }

            if (in_array($class, ['Ubix\\External\\GoogleAuthenticator'], true)) { // NOT_IMPLEMENTED: this is temporary until we spin this class into its own repository
                continue;
            }

            //
            //  Ensure $class is a valid class-string before reflecting
            //
            $this->assertTrue(
                class_exists($class) || enum_exists($class) || interface_exists($class),
                'The `' . $class . '` class must exist in the `' . $file->getRealPath() . '` file but does not',
            );

            //
            //  Use reflection to ensure the class is concrete (or an enum) and had a corresponding test case
            //
            $reflection = new ReflectionClass($class);

            if ($reflection->isAbstract() || $reflection->isInterface()) { // We are only looking at concrete classes and enums so skip abstract classes and interfaces
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
