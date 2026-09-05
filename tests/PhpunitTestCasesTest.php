<?php

declare(strict_types=1);

namespace Ubix\Tests;

use Ubix\Tests\AbstractPhpunitTestCasesTestCase as PhpunitTestCasesTestCase;

/**
 * PHPUnit test case proving every uBixCore concrete class and enum has a test case
 *
 * The logic lives in the framework (`AbstractPhpunitTestCasesTestCase`) so host
 * projects run the same check on their own trees; this file only says where
 * this repo's code and tests are.
 */
final class PhpunitTestCasesTest extends PhpunitTestCasesTestCase
{
    /**
     * Test that all uBix concrete classes and enums have a corresponding PHPUnit test case
     *
     * @return void
     */
    public function testEveryUbixConcreteClassAndEnumHasAPhpunitTestCase(): void
    {
        $this->assertEveryConcreteClassAndEnumHasATestCase();
    }

    /**
     * {@inheritDoc}
     */
    protected function getCodeDirectory(): string
    {
        return dirname(__DIR__) . '/php/Ubix';
    }

    /**
     * {@inheritDoc}
     */
    protected function getCodeNamespace(): string
    {
        return 'Ubix';
    }

    /**
     * {@inheritDoc}
     */
    protected function getTestsDirectory(): string
    {
        return __DIR__;
    }

    /**
     * {@inheritDoc}
     */
    protected function getTestsNamespace(): string
    {
        return 'Ubix\\Tests';
    }

    /**
     * {@inheritDoc}
     */
    protected function getExemptNamespaces(): array
    {
        return [
            'Ubix\\Bootstrap\\', // Bootstrap hooks are procedural scripts (vault.php, bootstrap.php), not classes
            'Ubix\\Filters\\',   // Custom phpcs filters are not subject to a full machine code review
            'Ubix\\Sniffs\\',    // Custom phpcs sniffs are not subject to a full machine code review
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getExemptClasses(): array
    {
        return [
            'Ubix\\External\\GoogleAuthenticator', // NOT_IMPLEMENTED: temporary until this class is spun into its own repository
        ];
    }
}
