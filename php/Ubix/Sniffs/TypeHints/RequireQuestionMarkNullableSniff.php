<?php declare(strict_types=1);

namespace Ubix\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class RequireQuestionMarkNullableSniff implements Sniff
{
    public function register(): array
    {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        //
        // 1) PHPDoc @param and @return tags
        //
        $docOpen = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $stackPtr, null, false);
        if ($docOpen !== false) {
            $docClose = $tokens[$docOpen]['comment_closer'] ?? null;
            if ($docClose !== null) {
                for ($i = $docOpen; $i <= $docClose; $i++) {
                    if (
                        $tokens[$i]['code'] === T_DOC_COMMENT_TAG
                        && in_array($tokens[$i]['content'], ['@param','@return'], true)
                    ) {
                        $tag  = $tokens[$i]['content'];
                        $next = $phpcsFile->findNext(
                            T_DOC_COMMENT_WHITESPACE,
                            $i + 1,
                            $docClose,
                            true
                        );
                        if (!isset($tokens[$next])) {
                            continue;
                        }

                        // The entire string token (e.g. "int|null   $modelId  description...")
                        $original = $tokens[$next]['content'];

                        // Extract just the type (stop at first space)
                        $typePart = strpos($original, ' ')
                            ? substr($original, 0, strpos($original, ' '))
                            : $original;

                        // Only single nullable unions
                        $hasMany = strpos($typePart, '|') !== false
                            && strpos($typePart, '|') !== strrpos($typePart, '|');

                        if (!$hasMany
                            && (str_starts_with($typePart, 'null|') || str_ends_with($typePart, '|null'))
                        ) {
                            // Compute the new "?Type"
                            $expected = '?' . (
                                str_starts_with($typePart, 'null|')
                                    ? substr($typePart, strpos($typePart, '|') + 1)
                                    : substr($typePart, 0, strpos($typePart, '|'))
                            );

                            $fix = $phpcsFile->addFixableError(
                                "{$tag} must use a question mark for nullable, change `{$typePart}` to `{$expected}`",
                                $next,
                                'Doc' . ucfirst(trim($tag, '@'))
                            );

                            if ($fix) {
                                // Preserve everything after the typePart in the same token
                                $suffix     = substr($original, strlen($typePart));
                                $newContent = $expected . $suffix;

                                $phpcsFile->fixer->beginChangeset();
                                $phpcsFile->fixer->replaceToken($next, $newContent);
                                $phpcsFile->fixer->endChangeset();
                            }
                        }
                    }
                }
            }
        }

        //
        // 2) Find the "(" that opens the param list
        //
        $open = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr + 1, null, false);
        if ($open === false) {
            return;
        }

        //
        // 3) Manually find the matching ")"
        //
        $depth = 1;
        $close = null;
        $max   = count($tokens);
        for ($i = $open + 1; $i < $max; $i++) {
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $depth++;
            } elseif ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                $depth--;
                if ($depth === 0) {
                    $close = $i;
                    break;
                }
            }
        }
        if ($close === null) {
            return;
        }

        //
        // 4) Scan each parameter and capture start/end of the union text
        //
        for ($i = $open + 1; $i < $close; $i++) {
            if ($tokens[$i]['code'] !== T_VARIABLE) {
                continue;
            }
            // Back up to skip whitespace
            $prev = $i - 1;
            while ($prev > $open && $tokens[$prev]['code'] === T_WHITESPACE) {
                $prev--;
            }

            // Gather contiguous union text and record start/end pointers
            $parts     = [];
            $hintStart = null;
            $hintEnd   = $prev;
            for ($j = $prev; $j > $open; $j--) {
                $text = $tokens[$j]['content'];
                if (preg_match('/^[\?\\\\\w\|]+$/', $text)) {
                    $hintStart = $j;
                    array_unshift($parts, $text);
                } else {
                    break;
                }
            }
            $hint = $parts ? implode('', $parts) : 'mixed';

            $this->processTypeHint(
                $phpcsFile,
                'Param',
                $hint,
                $hintStart ?? $i,
                $hintEnd,
                'Param'
            );
        }

        //
        // 5) Scan return type
        //
        $after = $close + 1;
        if (isset($tokens[$after]) && $tokens[$after]['code'] === T_COLON) {
            // Skip whitespace/comments
            $k = $after + 1;
            while (
                isset($tokens[$k])
                && in_array($tokens[$k]['code'], [
                    T_WHITESPACE,
                    T_COMMENT,
                    T_DOC_COMMENT_WHITESPACE,
                ], true)
            ) {
                $k++;
            }

            // Collect contiguous union text
            $parts     = [];
            $hintStart = $k;
            $hintEnd   = $k;
            for (; isset($tokens[$k]); $k++) {
                $text = $tokens[$k]['content'];
                if (preg_match('/^[\?\\\\\w\|]+$/', $text)) {
                    $parts[]  = $text;
                    $hintEnd  = $k;
                } else {
                    break;
                }
            }
            $retType = $parts ? implode('', $parts) : 'mixed';

            $this->processTypeHint(
                $phpcsFile,
                'Return type',
                $retType,
                $hintStart,
                $hintEnd,
                'Return'
            );
        } else {
            // no explicit return → mixed
            $this->processTypeHint(
                $phpcsFile,
                'Return type',
                'mixed',
                $stackPtr,
                $stackPtr,
                'Return'
            );
        }
    }

    /**
     * @param File   $phpcsFile
     * @param string $context    'Param', '@param', 'Return type', etc.
     * @param string $typeHint   the raw hint text (e.g. "string|null")
     * @param int    $startPtr   first token to replace
     * @param int    $endPtr     last  token to replace
     * @param string $errorLabel PHPCS code
     */
    private function processTypeHint(
        File $phpcsFile,
        string $context,
        string $typeHint,
        int $startPtr,
        int $endPtr,
        string $errorLabel
    ): void {
        // Only single nullable union (Type|null or null|Type)
        $hasMany = strpos($typeHint, '|') !== false
            && strpos($typeHint, '|') !== strrpos($typeHint, '|');

        if (!$hasMany && (
            str_starts_with($typeHint, 'null|')
            || str_ends_with($typeHint, '|null')
        )) {
            // Compute expected ?Type
            $expected = '?' . (
                str_starts_with($typeHint, 'null|')
                    ? substr($typeHint, strpos($typeHint, '|') + 1)
                    : substr($typeHint, 0, strpos($typeHint, '|'))
            );

            $fix = $phpcsFile->addFixableError(
                "{$context} must use a question mark for nullable, change `{$typeHint}` to `{$expected}`",
                $startPtr,
                $errorLabel
            );

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                // Replace the entire union text in one shot
                for ($ptr = $startPtr; $ptr <= $endPtr; $ptr++) {
                    $phpcsFile->fixer->replaceToken(
                        $ptr,
                        $ptr === $startPtr ? $expected : ''
                    );
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
