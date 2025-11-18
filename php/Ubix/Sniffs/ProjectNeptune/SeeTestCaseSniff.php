<?php declare(strict_types=1);

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class SeeTestCaseSniff implements Sniff
{
    /**
     * Listen for class and enum declarations.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [\T_CLASS, \T_ENUM];
    }

    /**
     * Processes the class or enum token to ensure a @see PHPUnit test case annotation.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the class/enum token.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // ── skip abstract classes (only want concrete classes & enums) ──
        if ($tokens[$stackPtr]['code'] === T_CLASS) {
            // find the first non‐empty token before "class"
            $prev = $phpcsFile->findPrevious(
                \PHP_CodeSniffer\Util\Tokens::$emptyTokens,
                $stackPtr - 1,
                null,
                true
            );
            // if that token is "abstract", bail out
            if ($prev !== false && $tokens[$prev]['code'] === T_ABSTRACT) {
                return;
            }
        }

        // 1) Find the DOC comment opener immediately before the class/enum.
        $docStart = $phpcsFile->findPrevious(\T_DOC_COMMENT_OPEN_TAG, $stackPtr - 1);
        if ($docStart === false) {
            $phpcsFile->addError(
                'Missing PHPDoc block with @see PHPUnit test case annotation.',
                $stackPtr,
                'MissingTestCaseSee'
            );
            return;
        }

        $docEnd = $tokens[$docStart]['comment_closer'] ?? null;
        if ($docEnd === null) {
            // Malformed docblock; bail.
            return;
        }

        // 2) Compute expected test case FQCN
        $namespacePtr = $phpcsFile->findPrevious(\T_NAMESPACE, $stackPtr);
        $namespace = '';
        if ($namespacePtr !== false) {
            $nsEnd = $phpcsFile->findNext(\T_SEMICOLON, $namespacePtr);
            $namespace = trim($phpcsFile->getTokensAsString($namespacePtr + 2, $nsEnd - $namespacePtr - 2));
        }
        $namePtr    = $phpcsFile->findNext(\T_STRING, $stackPtr);
        $className  = $tokens[$namePtr]['content'];
        $parts      = explode('\\', ltrim($namespace, '\\'));
        if (isset($parts[0]) && $parts[0] === 'Ubix') {
            array_splice($parts, 1, 0, 'Tests');
        }
        $testNamespace = '\\' . implode('\\', $parts);
        $expected      = sprintf(
            '@see %s\\%sTest PHPUnit test case',
            $testNamespace,
            $className
        );

        // 3) Scan @see tags in the docblock and count *all* references to our Test FQCN
        $testRefsCount = 0;
        $validSeeCount = 0;
        $testFqcn      = sprintf('%s\\%sTest', $testNamespace, $className);
        for ($i = $docStart; $i <= $docEnd; $i++) {
            if ($tokens[$i]['code'] === \T_DOC_COMMENT_TAG
                && $tokens[$i]['content'] === '@see'
            ) {
                $stringPtr = $phpcsFile->findNext(\T_DOC_COMMENT_STRING, $i + 1, $docEnd);
                $seeText   = $stringPtr !== false ? trim($tokens[$stringPtr]['content']) : '';
                $lineText  = '@see ' . $seeText;

                // if this @see points at our Test class FQCN, count it
                if (stripos($seeText, $testFqcn) === 0) {
                    $testRefsCount++;
                    if ($lineText === $expected) {
                        $validSeeCount++;
                    } else {
                        $phpcsFile->addError(
                            "Incorrect @see PHPUnit test case: '{$lineText}'",
                            $i,
                            'InvalidTestCaseSee'
                        );
                    }
                }
            }
        }

        // 4) Handle missing or duplicate test-case @see annotations
        //   - missing if no exact, duplicates if more than one reference
        if ($validSeeCount === 0) {
            $fix = $phpcsFile->addFixableError(
                'Missing @see PHPUnit test case annotation: ' . $expected,
                $docStart,
                'AddTestCaseSee'
            );
            if ($fix) {
                // Determine indent from the line after the open tag
                $indent = isset($tokens[$docStart + 1]['column'])
                    ? str_repeat(' ', $tokens[$docStart + 1]['column'])
                    : ' ';
                $insert = $indent . '* ' . $expected . "\n";

                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContentBefore($docEnd, $insert);
                $phpcsFile->fixer->endChangeset();
            }
        } elseif ($testRefsCount > 1) {
            $phpcsFile->addError(
                'Multiple @see PHPUnit test case annotations found.',
                $docStart,
                'MultipleTestCaseSee'
            );
        }
    }
}
