<?php declare(strict_types=1);

namespace Ubix\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MultilineArrayArrowAlignmentSniff implements Sniff
{
    public function register(): array
    {
        return [T_ARRAY, T_OPEN_SHORT_ARRAY];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Find array opener/closer.
        if ($tokens[$stackPtr]['code'] === T_ARRAY) {
            $opener = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
            $closer = $tokens[$opener]['parenthesis_closer'];
        } else {
            $opener = $stackPtr;
            $closer = $tokens[$opener]['bracket_closer'];
        }

        // Ignore single-line arrays.
        if ($tokens[$opener]['line'] === $tokens[$closer]['line']) {
            return;
        }

        // Collect ONLY top-level keys (exclude nested arrays).
        $maxKeyLength = 0;
        $arrows       = [];
        $ptr          = $opener + 1;

        while ($ptr < $closer) {
            // ── 1) If we hit a nested short array "[ ... ]", skip its whole span.
            if ($tokens[$ptr]['code'] === T_OPEN_SHORT_ARRAY) {
                $ptr = $tokens[$ptr]['bracket_closer'] + 1;
                continue;
            }

            // ── 2) If we hit a nested long array "array ( ... )", skip its whole span.
            if ($tokens[$ptr]['code'] === T_ARRAY) {
                $paren = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $ptr, $closer);
                if ($paren !== false && isset($tokens[$paren]['parenthesis_closer'])) {
                    $ptr = $tokens[$paren]['parenthesis_closer'] + 1;
                    continue;
                }
            }

            // ── 3) Find the next arrow from here (still inside current array bounds).
            $arrowPtr = $phpcsFile->findNext(T_DOUBLE_ARROW, $ptr, $closer);
            if ($arrowPtr === false) {
                break;
            }

            // ── 4) If that arrow actually belongs to a nested array, jump past the nested array and continue.
            //      (A) Short array check
            $innerShort = $phpcsFile->findPrevious(T_OPEN_SHORT_ARRAY, $arrowPtr - 1, $opener);
            if ($innerShort !== false && $innerShort > $opener && $tokens[$innerShort]['bracket_closer'] > $arrowPtr) {
                $ptr = $tokens[$innerShort]['bracket_closer'] + 1;
                continue;
            }
            //      (B) Long array check
            $innerLongArray = $phpcsFile->findPrevious(T_ARRAY, $arrowPtr - 1, $opener);
            if ($innerLongArray !== false && $innerLongArray > $opener) {
                $innerParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $innerLongArray, $closer);
                if ($innerParen !== false && $tokens[$innerParen]['parenthesis_closer'] > $arrowPtr) {
                    $ptr = $tokens[$innerParen]['parenthesis_closer'] + 1;
                    continue;
                }
            }

            // Find start and end of key expression.
            $end = $arrowPtr - 1;
            while (in_array($tokens[$end]['code'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true) && $end > $opener + 1) {
                $end--;
            }
            $start = $phpcsFile->findStartOfStatement($end);

            // Extract raw key text.
            $keyText = '';
            for ($i = $start; $i <= $end; $i++) {
                $keyText .= $tokens[$i]['content'];
            }

            $keyLength = strlen($keyText);
            $maxKeyLength = max($maxKeyLength, $keyLength);

            $arrows[] = [
                'arrowPtr' => $arrowPtr,
                'keyText'  => $keyText,
                'keyLen'   => $keyLength,
            ];

            $ptr = $arrowPtr + 1;
        }

        // Nothing to enforce if fewer than 2 arrows.
        if (count($arrows) < 2) {
            return;
        }

        // Enforce alignment.
        foreach ($arrows as $entry) {
            $arrowPtr = $entry['arrowPtr'];
            $keyLen   = $entry['keyLen'];
            $expectedSpaces = ($maxKeyLength - $keyLen) + 1;

            $foundSpaces = ($tokens[$arrowPtr - 1]['code'] === T_WHITESPACE)
                ? strlen($tokens[$arrowPtr - 1]['content'])
                : 0;

            if ($tokens[$arrowPtr - 1]['code'] !== T_WHITESPACE || $foundSpaces !== $expectedSpaces) {
                $error = sprintf(
                    'Array arrow not aligned correctly; expected %d space(s) before =>, found %d',
                    $expectedSpaces,
                    $foundSpaces
                );
                $fix = $phpcsFile->addFixableError($error, $arrowPtr, 'MisalignedArrow');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();

                    // If the token before => is whitespace, normalize it.
                    if ($tokens[$arrowPtr - 1]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($arrowPtr - 1, str_repeat(' ', $expectedSpaces));
                    } else {
                        // No whitespace yet -> insert the padding before the arrow.
                        $phpcsFile->fixer->addContentBefore($arrowPtr, str_repeat(' ', $expectedSpaces));
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }
}
