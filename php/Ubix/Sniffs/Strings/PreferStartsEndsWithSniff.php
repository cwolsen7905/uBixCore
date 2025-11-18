<?php declare(strict_types=1);

namespace Ubix\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Prefer str_starts_with()/str_ends_with() over specific substr comparisons.
 *
 * Flags patterns like:
 *   substr($s, -15) === 'ReaderInterface'   -> str_ends_with($s, 'ReaderInterface')
 *   substr($s, 0, 8) === 'Whatever'         -> str_starts_with($s, 'Whatever')
 *
 * Autofixes only when literal length matches the numeric bound.
 */
final class PreferStartsEndsWithSniff implements Sniff
{
    /** @var bool If true, attempt autofix when provably safe. */
    public $autoFix = true;

    /** @var bool If true, also look at mb_substr() (byte semantics differ!). */
    public $checkMbSubstr = false;

    /** @inheritDoc */
    public function register(): array
    {
        return [T_STRING];
    }

    /** @inheritDoc */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $name   = strtolower($tokens[$stackPtr]['content']);

        if ($name !== 'substr' && !($this->checkMbSubstr && $name === 'mb_substr')) {
            return;
        }

        // Ensure it's a function call
        $open = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($open === false || $tokens[$open]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }
        $close = $tokens[$open]['parenthesis_closer'] ?? null;
        if (!is_int($close)) {
            return;
        }

        // Parse arguments
        $args = $this->getCallArgs($phpcsFile, $open, $close);
        $argc = count($args);
        if ($argc < 2 || $argc > 3) {
            return; // only care about 2- or 3-arg calls
        }

        // Try to find the comparison operator (=== or !==) in the same statement.
        $stmtEnd = $phpcsFile->findEndOfStatement($stackPtr);
        $comparePtr = $phpcsFile->findNext([T_IS_IDENTICAL, T_IS_NOT_IDENTICAL], $close + 1, $stmtEnd + 1);
        $substrSide = 'left'; // default

        if ($comparePtr === false) {
            // Maybe we're on the right side (Yoda style).
            $stmtStart = $this->findStartOfStatement($phpcsFile, $stackPtr);
            $comparePtr = $phpcsFile->findPrevious([T_IS_IDENTICAL, T_IS_NOT_IDENTICAL], $stackPtr - 1, $stmtStart);
            $substrSide = 'right';
        }
        if ($comparePtr === false) {
            return; // not part of a strict comparison we care about
        }

        $isNot = ($tokens[$comparePtr]['code'] === T_IS_NOT_IDENTICAL);

        // Identify the "other side" of the comparison and ensure it’s a constant string.
        [$leftStart, $leftEnd]   = $this->expandOperandBounds($phpcsFile, $comparePtr - 1, 'left');
        [$rightStart, $rightEnd] = $this->expandOperandBounds($phpcsFile, $comparePtr + 1, 'right');

        $otherStart = ($substrSide === 'left') ? $rightStart : $leftStart;
        $otherEnd   = ($substrSide === 'left') ? $rightEnd   : $leftEnd;

        $literalPtr = $phpcsFile->findNext([T_CONSTANT_ENCAPSED_STRING], $otherStart, $otherEnd + 1);
        if ($literalPtr === false) {
            // We still *flag* opportunities without a literal, but we won't autofix.
            $literal = null;
        } else {
            $literal = $tokens[$literalPtr]['content']; // includes quotes
        }

        // Extract argument strings
        $haystack = trim($phpcsFile->getTokensAsString($args[0][0], $args[0][1] - $args[0][0] + 1));
        $second   = trim($phpcsFile->getTokensAsString($args[1][0], $args[1][1] - $args[1][0] + 1));
        $third    = null;
        if ($argc === 3) {
            $third = trim($phpcsFile->getTokensAsString($args[2][0], $args[2][1] - $args[2][0] + 1));
        }

        // Detect ends-with: substr($s, -N) and no 3rd param
        $isEndsPattern = ($argc === 2) && $this->isNegativeIntLiteral($second);

        // Detect starts-with: substr($s, 0, N)
        $isStartsPattern = ($argc === 3) && $this->isZero($second) && $this->isPositiveIntLiteral($third);

        if (!$isEndsPattern && !$isStartsPattern) {
            return;
        }

        $func = $isEndsPattern ? 'str_ends_with' : 'str_starts_with';

        // Report
        $errorCode = $isEndsPattern ? 'UseStrEndsWith' : 'UseStrStartsWith';
        $message   = sprintf(
            'Use %s(%s, <needle>) instead of %s(%s).',
            $func,
            $haystack,
            $name,
            $this->prettyArgs($phpcsFile, $args)
        );

        // Can we safely autofix? Only if the other side is a constant string whose length
        // matches the numeric bound (abs(-N) or N), and it’s a true constant (no encapsed).
        $canFix = false;
        $needleText = null;
        if ($this->autoFix && $literal !== null && $this->isSimpleConstantString($literal)) {
            $needleText = $literal; // keep exact literal as code
            $len = $this->literalLength($literal); // best-effort, single-quoted exact, double-quoted approximate
            if ($isEndsPattern) {
                $n = abs((int)$second);
                $canFix = ($len === $n);
            } else {
                $n = (int)$third;
                $canFix = ($len === $n);
            }
        }

        if ($canFix) {
            $fix = $phpcsFile->addFixableError($message, $stackPtr, $errorCode);
            if ($fix) {
                $replacement = sprintf('%s(%s, %s)', $func, $haystack, $needleText);
                if ($isNot) {
                    $replacement = '!' . $replacement;
                }

                // Replace the whole comparison (literal/operator/substr side) with the call.
                $replaceStart = min(
                    ($substrSide === 'left') ? $this->findOperandStart($phpcsFile, $stackPtr) : $leftStart,
                    ($substrSide === 'left') ? $rightStart : $this->findOperandStart($phpcsFile, $stackPtr)
                );
                $replaceEnd = max(
                    ($substrSide === 'left') ? $rightEnd : $this->findOperandEnd($phpcsFile, $stackPtr),
                    ($substrSide === 'left') ? $this->findOperandEnd($phpcsFile, $stackPtr) : $leftEnd
                );

                $phpcsFile->fixer->beginChangeset();
                $this->replaceTokenRange($phpcsFile, $replaceStart, $replaceEnd, $replacement);
                $phpcsFile->fixer->endChangeset();
            }
        } else {
            // Warn only
            $phpcsFile->addWarning($message . ' (autofix skipped: non-literal or length mismatch)', $stackPtr, $errorCode);
        }
    }

    /** Split top-level arguments inside (...) into [[$start,$end], ...]. */
    private function getCallArgs(File $phpcsFile, int $open, int $close): array
    {
        $tokens = $phpcsFile->getTokens();
        $args = [];
        $level = 0;
        $start = $open + 1;

        for ($i = $open + 1; $i < $close; $i++) {
            $code = $tokens[$i]['code'];
            if ($code === T_OPEN_PARENTHESIS || $code === T_OPEN_SHORT_ARRAY) {
                $level++;
            } elseif ($code === T_CLOSE_PARENTHESIS || $code === T_CLOSE_SQUARE_BRACKET) {
                $level--;
            } elseif ($code === T_COMMA && $level === 0) {
                $args[] = [$this->nextNonEmpty($phpcsFile, $start, $i - 1), $this->prevNonEmpty($phpcsFile, $start, $i - 1)];
                $start = $i + 1;
            }
        }

        $endStart = $this->nextNonEmpty($phpcsFile, $start, $close - 1);
        if ($endStart !== null) {
            $args[] = [$endStart, $this->prevNonEmpty($phpcsFile, $start, $close - 1)];
        }

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

    private function isNegativeIntLiteral(string $text): bool
    {
        return (bool)preg_match('/^-\s*[1-9]\d*\z/', $text);
    }

    private function isPositiveIntLiteral(string $text): bool
    {
        return (bool)preg_match('/^[1-9]\d*\z/', $text);
    }

    private function isZero(string $text): bool
    {
        return (bool)preg_match('/^0+\z/', $text);
    }

    private function isSimpleConstantString(string $literal): bool
    {
        // T_CONSTANT_ENCAPSED_STRING with single quotes is safest.
        // We allow double quotes only if there are no backslashes (no escapes).
        $q = $literal[0];
        if ($q === "'") {
            return true;
        }
        if ($q === '"') {
            return strpos($literal, '\\') === false;
        }
        return false;
    }

    /** Compute byte length of a simple literal (single-quoted exact; double-quoted naive). */
    private function literalLength(string $literal): int
    {
        // Strip outer quotes.
        $q = $literal[0];
        $inner = substr($literal, 1, -1);

        if ($q === "'") {
            // Only two escapes are significant in single-quoted PHP strings: \' and \\.
            $inner = str_replace(["\\\\", "\\'"], ["\\", "'"], $inner);
            return strlen($inner);
        }

        // For double-quoted (no backslashes by isSimpleConstantString) just strlen.
        return strlen($inner);
    }

    private function prettyArgs(File $phpcsFile, array $args): string
    {
        $tokens = $phpcsFile->getTokens();
        $parts = [];
        foreach ($args as [$s, $e]) {
            $parts[] = trim($phpcsFile->getTokensAsString($s, $e - $s + 1));
        }
        return implode(', ', $parts);
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

    private function expandOperandBounds(File $phpcsFile, int $ptr, string $side): array
    {
        // Expand to the natural operand (skip whitespace, include surrounding parentheses).
        $tokens = $phpcsFile->getTokens();
        if ($side === 'left') {
            $end = $phpcsFile->findPrevious(Tokens::$emptyTokens, $ptr, null, true);
            $start = $this->rewindThroughParen($phpcsFile, $end, 'left');
        } else {
            $start = $phpcsFile->findNext(Tokens::$emptyTokens, $ptr, null, true);
            $end = $this->rewindThroughParen($phpcsFile, $start, 'right');
        }
        return [$start, $end];
    }

    private function rewindThroughParen(File $phpcsFile, int $ptr, string $dir): int
    {
        $tokens = $phpcsFile->getTokens();
        if ($dir === 'left') {
            // Move left to include matching open parenthesis if we land on ')'
            if ($tokens[$ptr]['code'] === T_CLOSE_PARENTHESIS) {
                return $tokens[$ptr]['parenthesis_opener'];
            }
            // Else, return best guess token start.
            return $phpcsFile->findStartOfStatement($ptr);
        } else {
            // Move right to include matching close parenthesis if we land on '('
            if ($tokens[$ptr]['code'] === T_OPEN_PARENTHESIS) {
                return $tokens[$ptr]['parenthesis_closer'];
            }
            return $phpcsFile->findEndOfStatement($ptr);
        }
    }

    private function findOperandStart(File $phpcsFile, int $substrNamePtr): int
    {
        // Start of the substr call (including any leading '\')
        $tokens = $phpcsFile->getTokens();
        $start = $substrNamePtr;
        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $substrNamePtr - 1, null, true);
        if ($prev !== false && $tokens[$prev]['code'] === T_NS_SEPARATOR) {
            $start = $prev;
        }
        return $start;
    }

    private function findOperandEnd(File $phpcsFile, int $substrNamePtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $open = $phpcsFile->findNext(Tokens::$emptyTokens, $substrNamePtr + 1, null, true);
        $close = $tokens[$open]['parenthesis_closer'] ?? null;
        return is_int($close) ? $close : $substrNamePtr;
    }

    /**
     * PHPCS 3.x doesn’t have replaceTokenRange(). Emulate it:
     *  - write $replacement into $start
     *  - clear all tokens ($start+1 .. $end)
     */
    private function replaceTokenRange(File $phpcsFile, int $start, int $end, string $replacement): void
    {
        $phpcsFile->fixer->replaceToken($start, $replacement);
        for ($i = $start + 1; $i <= $end; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
    }
}
