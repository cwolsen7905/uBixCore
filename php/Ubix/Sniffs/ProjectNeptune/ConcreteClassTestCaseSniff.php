<?php
declare(strict_types=1);

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ConcreteClassTestCaseSniff implements Sniff
{
    public function register(): array
    {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // 1) Only interested in "public function testFollowingUbixStandards()"
        $namePtr = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
        if (!$namePtr || $tokens[$namePtr]['content'] !== 'testFollowingUbixStandards') {
            return;
        }
        // check public visibility
        $visPtr = $phpcsFile->findPrevious([T_PUBLIC, T_PROTECTED, T_PRIVATE], $stackPtr);
        if (!$visPtr || $tokens[$visPtr]['code'] !== T_PUBLIC) {
            return;
        }

        // 2) Find the containing class
        $classPtr = $phpcsFile->findPrevious(T_CLASS, $stackPtr);
        if (!$classPtr) {
            return;
        }

        // 3) Build a map of any file-level "use" aliases
        $aliases = $this->getUseAliases($phpcsFile);

        // 4) Resolve and check the "extends" clause
        $extPtr = $phpcsFile->findNext(T_EXTENDS, $classPtr + 1, null, false);
        if (!$extPtr) {
            return;
        }
        $parentFqn = $this->getFullyQualifiedName($phpcsFile, $extPtr, $aliases);
        if ($parentFqn !== 'Ubix\Tests\AbstractUbixConcreteClassTestCase') {
            return;
        }

        // 5) Resolve and check the "implements" clause (stop when we hit the "{")
        $impPtr = $phpcsFile->findNext(T_IMPLEMENTS, $classPtr + 1, null, false);
        if (!$impPtr) {
            return;
        }
        $implements = [];
        $ptr = $impPtr + 1;
        // use the global token for "{"
        while ($ptr && $tokens[$ptr]['code'] !== \T_OPEN_CURLY_BRACKET) {
            if (in_array($tokens[$ptr]['code'], [T_STRING, T_NS_SEPARATOR], true)) {
                // Simplest: just read the alias name and map it to its FQCN
                $alias = $tokens[$ptr]['content'];
                $implements[] = $aliases[$alias] ?? $alias;
                // skip forward past the name
                while (in_array($tokens[$ptr]['code'], [T_STRING, T_NS_SEPARATOR], true)) {
                    $ptr++;
                }
            } else {
                $ptr++;
            }
        }
        if (!in_array('Ubix\Tests\UbixConcreteClassTestCaseInterface', $implements, true)) {
            return;
        }

        // 6) Finally, inspect the method body token‐by‐token for a real call
        $opener = $tokens[$stackPtr]['scope_opener']  ?? null;
        $closer = $tokens[$stackPtr]['scope_closer'] ?? null;
        if (!$opener || !$closer) {
            return;
        }

        $found = false;
        for ($i = $opener + 1; $i < $closer; $i++) {
            // look for: $this → -> → testClassFollowingUbixStandards → (
            if ($tokens[$i]['code'] === T_VARIABLE && $tokens[$i]['content'] === '$this') {
                $next = $phpcsFile->findNext([\T_WHITESPACE], $i + 1, $closer, true);
                if ($next !== false && $tokens[$next]['code'] === T_OBJECT_OPERATOR) {
                    $methodPtr = $phpcsFile->findNext([\T_WHITESPACE], $next + 1, $closer, true);
                    if ($methodPtr !== false
                        && $tokens[$methodPtr]['code'] === T_STRING
                        && $tokens[$methodPtr]['content'] === 'testClassFollowingUbixStandards'
                    ) {
                        $openParen = $phpcsFile->findNext([T_WHITESPACE], $methodPtr + 1, $closer, true);
                        if ($openParen !== false && $tokens[$openParen]['code'] === T_OPEN_PARENTHESIS) {
                            $found = true;
                            break;
                        }
                    }
                }
            }
        }

        if (! $found) {
            $phpcsFile->addError(
                'When extending AbstractUbixConcreteClassTestCase and implementing UbixConcreteClassTestCaseInterface, testFollowingUbixStandards() must call $this->testClassFollowingUbixStandards().',
                $namePtr,
                'MissingStandardsCall'
            );
        }
    }

    /**
     * Scan all file-level "use" statements (excluding closures)
     * and build [ alias => fully\qualified\Name ].
     */
    private function getUseAliases(File $phpcsFile): array
    {
        $aliases = [];
        $tokens  = $phpcsFile->getTokens();
        $count   = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            // we only care about namespace-level imports, not trait-uses or closure-uses
            if ($tokens[$i]['code'] !== T_USE) {
                continue;
            }
            $scopes = array_keys($tokens[$i]['conditions']);
            // if it’s inside a class, closure or function, skip (trait use / closure use)
            if (in_array(T_CLASS,   $scopes, true)
             || in_array(T_CLOSURE, $scopes, true)
             || in_array(T_FUNCTION,$scopes, true)
            ) {
                continue;
            }

            // grab the FQCN
            $namePtr = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], $i + 1);
            $full    = '';
            for ($ptr = $namePtr; in_array($tokens[$ptr]['code'], [T_STRING, T_NS_SEPARATOR], true); $ptr++) {
                $full .= $tokens[$ptr]['content'];
            }

            // check for an "as" alias
            $asPtr = $phpcsFile->findNext(T_AS, $ptr, null, false);
            if ($asPtr) {
                $aliasPtr = $phpcsFile->findNext(T_STRING, $asPtr + 1);
                $alias    = $tokens[$aliasPtr]['content'];
            } else {
                $parts = explode('\\', $full);
                $alias = end($parts);
            }

            $aliases[$alias] = ltrim($full, '\\');
            $i = $ptr;
        }

        return $aliases;
    }

    /**
     * Given a pointer to an "extends" or "implements" keyword,
     * return its fully-qualified classname, resolving any alias.
     */
    private function getFullyQualifiedName(File $phpcsFile, int $keywordPtr, array $aliases): string
    {
        $tokens = $phpcsFile->getTokens();

        // find the start of the name
        $ptr = $keywordPtr + 1;
        while ($tokens[$ptr]['code'] === T_WHITESPACE) {
            $ptr++;
        }

        // collect all T_STRING and T_NS_SEPARATOR
        $name = '';
        while (in_array($tokens[$ptr]['code'], [T_STRING, T_NS_SEPARATOR], true)) {
            $name .= $tokens[$ptr]['content'];
            $ptr++;
        }

        $name = ltrim($name, '\\');
        return $aliases[$name] ?? $name;
    }
}
