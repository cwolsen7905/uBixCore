<?php declare(strict_types=1);

namespace Ubix\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ProtectedInFinalClassSniff implements Sniff
{
    public function register(): array
    {
        // we only care about the "protected" keyword
        return [T_PROTECTED];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // 1) Are we inside a final class?
        // PHP-CS-Fixer gives us a 'conditions' map on each token.
        $classPtr = null;
        foreach (($tokens[$stackPtr]['conditions'] ?? []) as $ptr => $code) {
            if ($code === T_CLASS) {
                $classPtr = (int)$ptr;
                break;
            }
        }
        if ($classPtr === null) {
            // not in a class at all
            return;
        }

        // find the first non-whitespace/comment token immediately before the class keyword
        $prev = $phpcsFile->findPrevious(
            [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT],
            $classPtr - 1,
            null,
            true
        );
        // if that token isn’t 'final', we’re not in a final class
        if ($prev === false || $tokens[$prev]['code'] !== T_FINAL) {
            return;
        }

        // 2) We’re in a final class and just hit a protected keyword — flag it.
        $error = 'Protected members in a final class serve no purpose; use private instead.';
        $fixable = $phpcsFile->addFixableError($error, $stackPtr, 'ProtectedInFinal');

        if ($fixable) {
            // Replace the "protected" token with "private"
            $phpcsFile->fixer->replaceToken($stackPtr, 'private');
        }
    }
}
