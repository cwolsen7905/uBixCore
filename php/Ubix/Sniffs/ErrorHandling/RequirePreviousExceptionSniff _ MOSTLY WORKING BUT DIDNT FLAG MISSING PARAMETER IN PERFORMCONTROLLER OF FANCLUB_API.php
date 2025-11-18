<?php declare(strict_types=1);

namespace Ubix\Sniffs\ErrorHandling;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class RequirePreviousExceptionSniff implements Sniff
{
    public function register(): array
    {
        // We only care about `throw` statements.
        return [T_THROW];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens     = $phpcsFile->getTokens();
        $conditions = $tokens[$stackPtr]['conditions'];

        // 1) Must be inside a catch block.
        $catchPtr = null;
        foreach ($conditions as $ptr => $code) {
            if ($code === T_CATCH) {
                $catchPtr = $ptr;
                break;
            }
        }
        if ($catchPtr === null) {
            return;
        }

        // 2) Must be `throw new Something(`…
        $newPtr = $phpcsFile->findNext(T_NEW, $stackPtr + 1, null, false);
        if ($newPtr === false) {
            return;
        }

        // 3) Identify the caught-variable name (`catch ( … $e )`).
        $catchOpener = $tokens[$catchPtr]['parenthesis_opener'] ?? null;
        $catchCloser = $tokens[$catchPtr]['parenthesis_closer'] ?? null;
        if ($catchOpener === null || $catchCloser === null) {
            return;
        }
        $caughtVarPtr  = $phpcsFile->findNext(T_VARIABLE, $catchOpener + 1, $catchCloser);
        if ($caughtVarPtr === false) {
            return;
        }
        $caughtVarName = $tokens[$caughtVarPtr]['content'];

        // 4) Locate the exception-constructor’s parentheses:
        //    pick the first `(` after T_NEW whose closer encloses a comma.
        $openParen  = null;
        $closeParen = null;
        for ($j = $newPtr + 1; $j < $phpcsFile->numTokens; $j++) {
            if ($tokens[$j]['code'] !== T_OPEN_PARENTHESIS) {
                continue;
            }
            $potentialCloser = $tokens[$j]['parenthesis_closer'] ?? null;
            if ($potentialCloser === null) {
                continue;
            }

            // Does this paren-pair contain at least one comma?
            $hasComma = $phpcsFile->findPrevious(
                T_COMMA,
                $potentialCloser - 1,
                $j,
                false
            ) !== false;

            if ($hasComma) {
                $openParen  = $j;
                $closeParen = $potentialCloser;
                break;
            }
        }
        if ($openParen === null) {
            // no multi-arg parentheses found → nothing to enforce
            return;
        }

        // 5) If there's no comma, there is only one argument → skip.
        // we've already found at least one comma here, so use it:
        $lastComma = $phpcsFile->findPrevious(T_COMMA, $closeParen - 1, $openParen, false);
        if ($lastComma === false) {
            return;
        }

        // 6) Find the start of the last argument (skip whitespace/comments).
        $lastArgPtr = $phpcsFile->findNext(
            [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STRING],
            $lastComma + 1,
            $closeParen,
            true
        );
        if ($lastArgPtr === false) {
            return;
        }

        // 7) If that last argument isn’t exactly the caught variable, error.
        if (
            $tokens[$lastArgPtr]['code'] !== T_VARIABLE
            || $tokens[$lastArgPtr]['content'] !== $caughtVarName
        ) {
            $phpcsFile->addError(
                sprintf(
                    'When re-throwing inside this catch, pass the caught exception %s as the `$previous` argument.',
                    $caughtVarName
                ),
                $stackPtr,
                'MissingPreviousException'
            );
        }
    }
}
