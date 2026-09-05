<?php declare(strict_types=1);

namespace Ubix\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Enforce multi-line constructor parameter lists with a trailing comma.
 *
 * - Only applies to __construct methods inside classes/traits.
 * - If the parameter list is single-line, it is flagged and auto-fixed to:
 *
 *   public function __construct(
 *       <param>,
 *       <param>,
 *   ) {
 *   }
 */
final class ConstructorMustBeMultilineSniff implements Sniff
{
    public function register(): array
    {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Only methods inside a class/interface/trait.
        if (empty($tokens[$stackPtr]['conditions'])) {
            return;
        }

        // Find the method name.
        $namePtr = $phpcsFile->findNext(T_STRING, $stackPtr);
        if ($namePtr === false || strtolower($tokens[$namePtr]['content']) !== '__construct') {
            return;
        }

        // Parameter parentheses.
        $open  = $tokens[$stackPtr]['parenthesis_opener'] ?? null;
        $close = $tokens[$stackPtr]['parenthesis_closer'] ?? null;
        if ($open === null || $close === null) {
            return; // abstract interface method without params, etc.
        }

        // No parameters -> nothing to enforce.
        if ($phpcsFile->findNext([T_STRING, T_VARIABLE, T_ELLIPSIS, T_ATTRIBUTE], $open + 1, $close) === false) {
            return;
        }

        // Already multi-line?
        if ($tokens[$open]['line'] !== $tokens[$close]['line']) {
            // Optional: ensure trailing comma in multi-line constructors.
            // Look for the last *meaningful* token (skip whitespace and comments).
            $lastMeaningful = $phpcsFile->findPrevious(\PHP_CodeSniffer\Util\Tokens::$emptyTokens, $close - 1, $open + 1, true);
            if ($lastMeaningful !== false && $tokens[$lastMeaningful]['code'] !== T_COMMA) {
                if ($phpcsFile->addFixableError('Multi-line constructor parameters must end with a trailing comma.', $close, 'MissingTrailingComma')) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContent($lastMeaningful, ',');
                    $phpcsFile->fixer->endChangeset();
                }
            }
            return;
        }

        // Single-line constructor -> error + fix.
        if (!$phpcsFile->addFixableError('Constructor parameters must be split across multiple lines with a trailing comma.', $namePtr, 'ConstructorMustBeMultiline')) {
            return;
        }

        // Build indentation.
        $functionIndentCol = max(1, (int)($tokens[$stackPtr]['column'] ?? 1));
        $indent            = str_repeat(' ', $functionIndentCol - 1);
        $paramIndent       = $indent . str_repeat(' ', 4); // PSR-12 default indent

        // Collect parameter segments at top level (ignore commas inside defaults/arrays).
        $segments = [];
        $start = $open + 1;
        $depthParen = 0; $depthSquare = 0; $depthCurly = 0;
        for ($i = $open + 1; $i < $close; $i++) {
            $code = $tokens[$i]['code'];
            if ($code === T_OPEN_PARENTHESIS)  { $depthParen++; }
            if ($code === T_CLOSE_PARENTHESIS) { $depthParen--; }
            if ($code === T_OPEN_SQUARE_BRACKET)  { $depthSquare++; }
            if ($code === T_CLOSE_SQUARE_BRACKET) { $depthSquare--; }
            if ($code === T_OPEN_CURLY_BRACKET)   { $depthCurly++; }
            if ($code === T_CLOSE_CURLY_BRACKET)  { $depthCurly--; }

            if ($code === T_COMMA && $depthParen === 0 && $depthSquare === 0 && $depthCurly === 0) {
                $segments[] = trim($phpcsFile->getTokensAsString($start, $i - $start));
                $start = $i + 1;
            }
        }
        // Final segment
        $final = trim($phpcsFile->getTokensAsString($start, $close - $start));
        if ($final !== '') {
            // drop any trailing comma if present in single-line form (rare but valid)
            $final = rtrim($final);
            $final = rtrim($final, ',');
            $segments[] = $final;
        }

        // Reconstruct multiline parameter list with trailing comma.
        $replacement = "\n";
        foreach ($segments as $idx => $seg) {
            $replacement .= $paramIndent . $seg . ",\n";
        }
        $replacement .= $indent; // align closing paren with the method

        // Apply fix.
        $phpcsFile->fixer->beginChangeset();
        // Replace everything between "(" and ")" (exclusive) with our multiline content.
        $phpcsFile->fixer->replaceToken($open, $tokens[$open]['content'] . $replacement);
        // Remove all original tokens inside, we already injected everything above with replaceToken on opener.
        for ($i = $open + 1; $i < $close; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
        $phpcsFile->fixer->replaceToken($close, ')'); // keep closer; brace spacing stays intact.
        $phpcsFile->fixer->endChangeset();
    }
}
