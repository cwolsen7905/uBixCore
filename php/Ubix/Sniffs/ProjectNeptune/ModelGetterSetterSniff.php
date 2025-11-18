<?php

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ModelGetterSetterSniff implements Sniff
{
    public function register() : array
    {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        // 1) Find the namespace of this file:
        $namespace = '';
        $nsPtr     = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($nsPtr !== false) {
            $semi = $phpcsFile->findNext(T_SEMICOLON, $nsPtr);
            // grab everything between "namespace" and ";"
            $namespace = $phpcsFile->getTokensAsString($nsPtr + 2, $semi - $nsPtr - 2);
        }

        if (strpos($namespace, 'Ubix\\Model') !== 0) {
            return;  // only apply in your model classes
        }

        // 2) Walk the class body, find private/protected props
        $properties = [];

        $scopeOpener = $tokens[$stackPtr]['scope_opener'];
        $scopeCloser = $tokens[$stackPtr]['scope_closer'];

        for ($ptr = $scopeOpener; $ptr < $scopeCloser; $ptr++) {
            $code = $tokens[$ptr]['code'];
            if (!in_array($code, [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR], true)) {
                continue; // only property visibilities
            }

            // If this visibility is for a method (e.g. "private function ..."), skip.
            $nextNonEmpty = $phpcsFile->findNext(\PHP_CodeSniffer\Util\Tokens::$emptyTokens, $ptr + 1, null, true);
            if ($nextNonEmpty !== false && $tokens[$nextNonEmpty]['code'] === T_FUNCTION) {
                continue;
            }

            // find the variable name
            $varPtr = $phpcsFile->findNext(T_VARIABLE, $ptr + 1, $scopeCloser);
            if ($varPtr === false) {
                continue; // nothing variable-like after the visibility
            }
            $varName = ltrim($tokens[$varPtr]['content'], '$');

            // now extract the type tokens between $ptr and $varPtr
            $type = '';
            for ($i = $ptr + 1; $i < $varPtr; $i++) {
                $c = $tokens[$i]['code'];
                // skip visibility/static/whitespace
                if (in_array($c, [T_WHITESPACE, T_STATIC], true)) {
                    continue;
                }
                // these token codes represent parts of a type hint
                if (in_array($c, [
                    T_STRING,
                    T_NAME_QUALIFIED,
                    T_NAME_FULLY_QUALIFIED,
                    T_NAME_RELATIVE,
                    T_NS_SEPARATOR,
                    T_NULLABLE,
                    T_TYPE_UNION ?? -1,          // if defined in your PHPCS version
                    T_TYPE_INTERSECTION ?? -1,   // if defined in your PHPCS version
                ], true)) {
                    $type .= $tokens[$i]['content'];
                    continue;
                }
                // as soon as we hit something else (e.g. the dollar sign), stop
                break;
            }

            // fallback
            $properties[$varName] = $type ?: 'mixed';
        }

        // 3) Gather existing method names:
        $methods = [];
        foreach ($tokens as $pos => $tok) {
            if ($tok['code'] === T_FUNCTION) {
                $name = $phpcsFile->getDeclarationName($pos); // null for closures
                if ($name !== null) {
                    $methods[] = $name;
                }
            }
        }

        // 4) For each property, check getter/setter:
        foreach ($properties as $propName => $type) {
            $getter = 'get' . strtoupper(substr($propName, 0, 1)) . substr($propName, 1);
            if (!in_array($getter, $methods, true)) {
                $error = "Missing getter method {$getter}() for property \${$propName}";
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'MissingGetter');
                if ($fix) {
                    $this->addGetter($phpcsFile, $stackPtr, $propName, $type);
                }
            }

            $setter = 'set' . strtoupper(substr($propName, 0, 1)) . substr($propName, 1);
            if (!in_array($setter, $methods, true)) {
                $error = "Missing setter method {$setter}() for property \${$propName}";
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'MissingSetter');
                if ($fix) {
                    $this->addSetter($phpcsFile, $stackPtr, $propName, $type);
                }
            }
        }
    }

    private function addGetter(File $phpcsFile, int $classPtr, string $prop, string $type) : void
    {
        $tokens = $phpcsFile->getTokens();
        $close  = $tokens[$classPtr]['scope_closer'];
        $method = $this->buildGetterCode($prop, $type);
        $phpcsFile->fixer->addContentBefore($close, $method);
    }

    private function addSetter(File $phpcsFile, int $classPtr, string $prop, string $type) : void
    {
        $tokens = $phpcsFile->getTokens();
        $close  = $tokens[$classPtr]['scope_closer'];
        $method = $this->buildSetterCode($prop, $type);
        $phpcsFile->fixer->addContentBefore($close, $method);
    }

    private function buildGetterCode(string $prop, string $type) : string
    {
        $ucProp = strtoupper(substr($prop, 0, 1)) . substr($prop, 1);
        return "\n"
             . '    /**' . "\n"
             . '     * Get the value of ' . $prop . "\n"
             . '     *' . "\n"
             . '     * @return ' . $type . ' The value of ' . $prop . "\n"
             . '     */' . "\n"
             . '    public function get' . $ucProp . '(): ' . $type . "\n"
             . '    {' . "\n"
             . '        return $this->' . $prop . ';' . "\n"
             . '    }' . "\n"
             . "\n";
    }

    private function buildSetterCode(string $prop, string $type) : string
    {
        $ucProp = strtoupper(substr($prop, 0, 1)) . substr($prop, 1);
        return "\n"
             . '    /**' . "\n"
             . '     * Set the value of ' . $prop . "\n"
             . '     *' . "\n"
             . '     * @param ' . $type . ' $' . $prop . ' The value for ' . $prop . "\n"
             . '     *' . "\n"
             . '     * @return void' . "\n"
             . '     */' . "\n"
             . '    public function set' . $ucProp . '(' . $type . ' $' . $prop . '): void' . "\n"
             . '    {' . "\n"
             . '        $this->' . $prop . ' = $' . $prop . ';' . "\n"
             . '    }' . "\n"
             . "\n";
    }
}
