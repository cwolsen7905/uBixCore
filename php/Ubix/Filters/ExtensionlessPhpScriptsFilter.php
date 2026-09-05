<?php

declare(strict_types=1);

namespace Ubix\Formatting;

use PHP_CodeSniffer\Filters\Filter;
use PHP_CodeSniffer\Util\Common;
use RuntimeException;
use SplFileInfo;

/**
 * Filter to allow files without a .php extension to be treated as PHP files if they are CLI scripts starting with a shebang line like: #!/opt/php8.1/bin/php
 *
 * This is needed for the `phpstan` command in our project root which lacks a file extension.
 *
 * Usage: Add the following to your phpcs.xml file: <arg name="filter" value="php/Ubix/Filters/ExtensionlessPhpScripts.php"/>
 */
final class ExtensionlessPhpScriptsFilter extends Filter
{
    private const SCRIPT_SHEBANG = '@^#!/usr/bin/env\ php@';

    /**
     * Check whether the current element of the iterator is acceptable.
     *
     * Files are checked for allowed extensions and ignore patterns. Directories are checked for ignore patterns only.
     *
     * @throws RuntimeException If the file path cannot be determined
     *
     * @return bool
     */
    public function accept(): bool
    {
        $path = $this->current();
        if ($path instanceof SplFileInfo) {
            $path = $path->getPathname();
        } elseif (!is_string($path)) {
            throw new RuntimeException('Filter iterator current element is not a string or an instance of SplFileInfo');
        }

        $filePath = Common::realpath($path);
        if ($filePath === false) {
            throw new RuntimeException('Common::realpath() did not return a string');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (is_dir($filePath) || $extension !== '') {
            return parent::accept();
        }

        if ($this->shouldIgnorePath($filePath)) {
            return false;
        }

        return $this->isPhpShellScript($filePath);
    }

    /**
     * Determine if a file is a PHP shell script based on its shebang line
     *
     * @param string $filePath The file path
     *
     * @throws RuntimeException If the file cannot be opened or read
     *
     * @return bool Whether or not the file is a PHP shell script
     */
    private function isPhpShellScript(string $filePath): bool
    {
        if (is_dir($filePath)) {
            return false;
        }

        $fp = fopen($filePath, 'rb');
        if (!$fp) {
            throw new RuntimeException('Could not open file to determine type: ' . $filePath);
        }

        $line = fgets($fp, 80);
        fclose($fp);
        if ($line === false) {
            throw new RuntimeException('Could not read file to determine type: ' . $filePath);
        }

        return (bool)preg_match(self::SCRIPT_SHEBANG, $line);
    }
}
