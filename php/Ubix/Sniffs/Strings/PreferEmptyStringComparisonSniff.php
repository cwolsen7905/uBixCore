<?php declare(strict_types=1);

namespace Ubix\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

final class PreferEmptyStringComparisonSniff implements Sniff
{
    /** Turn on to apply autofixes (defaults to true since this is usually safe with typed codebases). */
    public $autoFix = true;

    /** Also flag == / != forms (warn only; no autofix). */
    public $allowLooseEquals = false;

    public function register(): array
    {
        return [T_STRING];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        if (strtolower($tokens[$stackPtr]['content']) !== 'strlen') {
            return;
        }

        // Ensure strlen(...)
        $open = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($open === false || $tokens[$open]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }
        $close = $tokens[$open]['parenthesis_closer'] ?? null;
        if (!is_int($close)) {
            return;
        }

        // Exactly one arg
        $args = $this->getCallArgs($phpcsFile, $open, $close);
        if (count($args) !== 1) {
            return;
        }
        [$argStart, $argEnd] = $args[0];
        $haystack = trim($phpcsFile->getTokensAsString($argStart, $argEnd - $argStart + 1));

        $stmtEnd = $phpcsFile->findEndOfStatement($stackPtr);

        // Find operator to the right first, else Yoda (to the left).
        [$opPtr, $opKind] = $this->findOpToRight($phpcsFile, $close + 1, $stmtEnd);
        $strlenSide = 'left';
        if ($opPtr === null) {
            $stmtStart = $this->findStartOfStatement($phpcsFile, $stackPtr);
            [$opPtr, $opKind] = $this->findOpToLeft($phpcsFile, $stmtStart, $stackPtr - 1);
            $strlenSide = 'right';
        }
        if ($opPtr === null) {
            return;
        }

        // Identify the "zero" operand (must be literally 0, possibly parenthesized).
        if ($strlenSide === 'left') {
            [$zeroStart, $zeroEnd] = $this->boundsToRight($phpcsFile, $opPtr);
        } else {
            [$zeroStart, $zeroEnd] = $this->boundsToLeft($phpcsFile, $opPtr);
        }
        $isZero = $this->isLiteralZero($phpcsFile, $zeroStart, $zeroEnd);
        if (!$isZero) {
            // Optionally warn on loose equals patterns to 0
            if ($this->allowLooseEquals && ($opKind === '==' || $opKind === '!=')) {
                $phpcsFile->addWarning('Loose equality with strlen detected; prefer strict comparison to 0 or empty string checks.', $stackPtr, 'LooseEquality');
            }
            return;
        }

        // We only transform: strlen(...) === 0  and  strlen(...) > 0 (with Yoda mirrors).
        $isEqZero = ($opKind === '===');
        $isGtZero = ($opKind === '>'  && $strlenSide === 'left') ||
                    ($opKind === '<'  && $strlenSide === 'right');

        if (!$isEqZero && !$isGtZero) {
            return;
        }

        $suggest = $isEqZero ? sprintf('%s === \'\'', $haystack)
                             : sprintf('%s !== \'\'', $haystack);

        $start = $this->comparisonStart($phpcsFile, $stackPtr, $opPtr, $strlenSide);
        $end   = $this->comparisonEnd($phpcsFile, $stackPtr, $opPtr, $strlenSide);
        $original = trim($phpcsFile->getTokensAsString($start, $end - $start + 1));

        $code    = $isEqZero ? 'UseEmptyStringComparison' : 'UseNotEmptyStringComparison';
        $message = sprintf('Prefer %s over %s.', $suggest, $original);

        if ($this->autoFix) {
            $fix = $phpcsFile->addFixableError($message, $stackPtr, $code);
            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $this->replaceTokenRange($phpcsFile, $start, $end, $suggest);
                $phpcsFile->fixer->endChangeset();
            }
        } else {
            $phpcsFile->addWarning($message . ' (autofix disabled)', $stackPtr, $code);
        }
    }

    /* ---------------------- helpers ---------------------- */

    private function getCallArgs(File $phpcsFile, int $open, int $close): array
    {
        $tokens = $phpcsFile->getTokens();
        $args = [];
        $lvl = 0; $start = $open + 1;
        for ($i = $open + 1; $i < $close; $i++) {
            $c = $tokens[$i]['code'];
            if ($c === T_OPEN_PARENTHESIS || $c === T_OPEN_SHORT_ARRAY) $lvl++;
            elseif ($c === T_CLOSE_PARENTHESIS || $c === T_CLOSE_SQUARE_BRACKET) $lvl--;
            elseif ($c === T_COMMA && $lvl === 0) {
                $s = $this->nextNonEmpty($phpcsFile, $start, $i - 1);
                if ($s !== null) $args[] = [$s, $this->prevNonEmpty($phpcsFile, $start, $i - 1)];
                $start = $i + 1;
            }
        }
        $s = $this->nextNonEmpty($phpcsFile, $start, $close - 1);
        if ($s !== null) $args[] = [$s, $this->prevNonEmpty($phpcsFile, $start, $close - 1)];
        return $args;
    }

    private function nextNonEmpty(File $phpcsFile, int $start, int $end): ?int
    {
        $i = $phpcsFile->findNext(Tokens::$emptyTokens, $start, $end + 1, true);
        return ($i === false) ? null : $i;
    }
    private function prevNonEmpty(File $phpcsFile, int $start, int $end): int
    {
        return (int)$phpcsFile->findPrevious(Tokens::$emptyTokens, $end, $start - 1, true);
    }

    private function findStartOfStatement(File $phpcsFile, int $ptr): int
    {
        $tokens = $phpcsFile->getTokens();
        for ($i = $ptr; $i > 0; $i--) {
            if ($tokens[$i]['code'] === T_SEMICOLON || $tokens[$i]['code'] === T_OPEN_CURLY_BRACKET) {
                return $i + 1;
            }
        }
        return 0;
    }

    /** First non-empty token to the right; maps to op kind. */
    private function findOpToRight(File $phpcsFile, int $start, int $end): array
    {
        $tokens = $phpcsFile->getTokens();
        $i = $phpcsFile->findNext(Tokens::$emptyTokens, $start, $end + 1, true);
        if ($i === false) return [null, null];
        $k = $this->mapOperatorKind($tokens[$i]['content'] ?? '', $tokens[$i]['code']);
        return [$k ? $i : null, $k];
    }

    /** First non-empty token to the left; maps to op kind. */
    private function findOpToLeft(File $phpcsFile, int $start, int $end): array
    {
        $tokens = $phpcsFile->getTokens();
        $i = $phpcsFile->findPrevious(Tokens::$emptyTokens, $end, $start - 1, true);
        if ($i === false) return [null, null];
        $k = $this->mapOperatorKind($tokens[$i]['content'] ?? '', $tokens[$i]['code']);
        return [$k ? $i : null, $k];
    }

    /**
     * Map token to a simple operator kind string.
     * $code can be an int (T_* constants) or a string for single-char operators.
     */
    private function mapOperatorKind(string $content, $code): ?string
    {
        if (is_int($code)) {
            switch ($code) {
                case T_IS_IDENTICAL:         return '===';
                case T_IS_NOT_IDENTICAL:     return '!==';
                case T_IS_EQUAL:             return $this->allowLooseEquals ? '==' : null;
                case T_IS_NOT_EQUAL:         return $this->allowLooseEquals ? '!=' : null;
                case T_IS_GREATER_OR_EQUAL:  return '>='; // keep for completeness
                case T_IS_SMALLER_OR_EQUAL:  return '<=';
            }
        }
        // Fall back to raw content for single-character operators.
        if ($content === '>') return '>';
        if ($content === '<') return '<';
        return null;
    }

    private function boundsToRight(File $phpcsFile, int $opPtr): array
    {
        $tokens = $phpcsFile->getTokens();
        $start = $phpcsFile->findNext(Tokens::$emptyTokens, $opPtr + 1, null, true);
        if ($start === false) return [$opPtr + 1, $opPtr + 1];
        if ($tokens[$start]['code'] === T_OPEN_PARENTHESIS) {
            return [$start, (int)$tokens[$start]['parenthesis_closer']];
        }
        return [$start, $start];
    }

    private function boundsToLeft(File $phpcsFile, int $opPtr): array
    {
        $tokens = $phpcsFile->getTokens();
        $end = $phpcsFile->findPrevious(Tokens::$emptyTokens, $opPtr - 1, null, true);
        if ($end === false) return [$opPtr - 1, $opPtr - 1];
        if ($tokens[$end]['code'] === T_CLOSE_PARENTHESIS) {
            return [(int)$tokens[$end]['parenthesis_opener'], $end];
        }
        return [$end, $end];
    }

    private function strlenCallBounds(File $phpcsFile, int $strlenNamePtr): array
    {
        $tokens = $phpcsFile->getTokens();
        $start = $strlenNamePtr;
        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $strlenNamePtr - 1, null, true);
        if ($prev !== false && $tokens[$prev]['code'] === T_NS_SEPARATOR) {
            $start = $prev;
        }
        $open = $phpcsFile->findNext(Tokens::$emptyTokens, $strlenNamePtr + 1, null, true);
        $end  = is_int($open) ? (int)$tokens[$open]['parenthesis_closer'] : $strlenNamePtr;
        return [$start, $end];
    }

    private function comparisonStart(File $phpcsFile, int $strlenNamePtr, int $opPtr, string $strlenSide): int
    {
        if ($strlenSide === 'left') {
            [$lStart, ] = $this->strlenCallBounds($phpcsFile, $strlenNamePtr);
            return $lStart;
        }
        [$leftStart, ] = $this->boundsToLeft($phpcsFile, $opPtr);
        return $leftStart;
    }

    private function comparisonEnd(File $phpcsFile, int $strlenNamePtr, int $opPtr, string $strlenSide): int
    {
        if ($strlenSide === 'left') {
            [, $rightEnd] = $this->boundsToRight($phpcsFile, $opPtr);
            return $rightEnd;
        }
        [, $rEnd] = $this->strlenCallBounds($phpcsFile, $strlenNamePtr);
        return $rEnd;
    }

    private function isLiteralZero(File $phpcsFile, int $start, int $end): bool
    {
        $tokens = $phpcsFile->getTokens();
        $zero = $phpcsFile->findNext([T_LNUMBER], $start, $end + 1);
        return $zero !== false && trim($tokens[$zero]['content']) === '0';
    }

    /** PHPCS 3.x polyfill for replaceTokenRange. */
    private function replaceTokenRange(File $phpcsFile, int $start, int $end, string $replacement): void
    {
        $phpcsFile->fixer->replaceToken($start, $replacement);
        for ($i = $start + 1; $i <= $end; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
    }
}
