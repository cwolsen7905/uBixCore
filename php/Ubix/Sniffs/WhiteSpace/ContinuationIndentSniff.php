<?php

declare(strict_types=1);

namespace Ubix\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ContinuationIndentSniff implements Sniff
{
    private const INDENT_SPACES = 4;

    private const CONTINUATION_TOKENS = [
        T_BOOLEAN_AND,  // &&
        T_BOOLEAN_OR,   // ||
        T_LOGICAL_AND,  // and
        T_LOGICAL_OR,   // or
        T_LOGICAL_XOR,  // xor
        //T_CONCAT_EQUAL, // .=
        T_STRING_CONCAT, // .
    ];

    public function register(): array
    {
        return self::CONTINUATION_TOKENS;
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Ensure this token is the first on its line (i.e., it's a hanging indent)
        $token = $tokens[$stackPtr];
        if ($token['column'] === 1) {
            return; // starts at col 1; ignore (not a hanging continuation)
        }

        $prevNonWhitespace = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($prevNonWhitespace === false) {
            return;
        }

        $prevToken = $tokens[$prevNonWhitespace];

        // Find the line of the assignment or comparison that began the expression
        $expressionStartPtr = $this->findExpressionStart($phpcsFile, $stackPtr);
        $expressionStartToken = $tokens[$expressionStartPtr];

        // Get column of start and current token
        $startColumn = $expressionStartToken['column'];
        $expectedColumn = $startColumn + self::INDENT_SPACES;
        $actualColumn = $token['column'];

        if ($actualColumn !== $expectedColumn) {
            $error = sprintf(
                'Continuation line not indented correctly; expected %d spaces, found %d.',
                $expectedColumn,
                $actualColumn
            );

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'BadContinuationIndent');

            if ($fix) {
                $padding = str_repeat(' ', $expectedColumn - 1);
                $phpcsFile->fixer->replaceToken($stackPtr - 1, $padding);
            }
        }
    }

    private function findExpressionStart(File $phpcsFile, int $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $currentLine = $tokens[$stackPtr]['line'];

        for ($i = $stackPtr - 1; $i >= 0; --$i) {
            if ($tokens[$i]['line'] < $currentLine) {
                return $i; // return $phpcsFile->findFirstOnLine($i);
            }
        }

        return $stackPtr; // fallback: use self if we can't find earlier
    }
}
