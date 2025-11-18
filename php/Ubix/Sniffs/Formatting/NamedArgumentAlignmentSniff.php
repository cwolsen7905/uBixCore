<?php

declare(strict_types=1);

namespace Ubix\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NamedArgumentAlignmentSniff implements Sniff
{
    public function register(): array
    {
        return [T_OPEN_PARENTHESIS];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // 1) Find matching closer; bail if none.
        $closer = $tokens[$stackPtr]['parenthesis_closer'] ?? null;
        if ($closer === null) {
            return;
        }

        // 2) Only look at multi-line argument lists.
        if ($tokens[$stackPtr]['line'] === $tokens[$closer]['line']) {
            return;
        }

        // 3) Only handle function/method calls or object instantiations:
        //    ensure this "(" follows a T_STRING (call name) or T_NEW.
        $prev = $phpcsFile->findPrevious(
            [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STRING],
            $stackPtr - 1,
            null,
            true
        );
        if ($prev === false) {
            return;
        }
        $prevCode = $tokens[$prev]['code'];
        if ($prevCode !== T_STRING && $prevCode !== T_NEW) {
            return;
        }

        // 4) Scan for named args at the top-level of this argument list only.
        $argumentInfo = [];
        $nestedParens = 0;

        for ($ptr = $stackPtr + 1; $ptr < $closer; $ptr++) {
            $code = $tokens[$ptr]['code'];

            // track deeper parentheses and skip any colons inside them
            if ($code === T_OPEN_PARENTHESIS) {
                $nestedParens++;
                continue;
            } elseif ($code === T_CLOSE_PARENTHESIS) {
                $nestedParens--;
                continue;
            }

            // only consider colons at the base level
            if ($nestedParens === 0 && $code === T_COLON) {
                $namePtr = $phpcsFile->findPrevious(
                    [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STRING],
                    $ptr - 1,
                    $stackPtr,
                    true
                );
                if ($namePtr === false) {
                    continue;
                }

                $name = $tokens[$namePtr]['content'];
                if (!preg_match('/^[a-zA-Z_]\\w*$/', $name)) {
                    continue;
                }

                $argumentInfo[] = [
                    'name'     => $name,
                    'namePtr'  => $namePtr,
                    'colonPtr' => $ptr,
                ];
            }
        }

        // 5) Nothing to do if there were no named args.
        if (count($argumentInfo) === 0) {
            return;
        }

        // 6) Compute alignment metrics.
        $names     = array_column($argumentInfo, 'name');
        $maxLength = max(array_map('strlen', $names));

        // 7) Validate & fix each argument:
        foreach ($argumentInfo as $info) {
            $name     = $info['name'];
            $namePtr  = $info['namePtr'];
            $colonPtr = $info['colonPtr'];

            // 7.1) No space BEFORE colon.
            if ($tokens[$namePtr + 1]['code'] === T_WHITESPACE) {
                $fix = $phpcsFile->addFixableError(
                    "Unexpected space before colon in named argument '{$name}'",
                    $colonPtr,
                    'SpaceBeforeColon'
                );
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($namePtr + 1, '');
                }
            }

            // 7.2) Correct spacing AFTER colon to align values.
            $expected = $maxLength - strlen($name) + 1;
            $actual   = 0;
            if ($tokens[$colonPtr + 1]['code'] === T_WHITESPACE) {
                $actual = strlen($tokens[$colonPtr + 1]['content']);
            }
            if ($actual !== $expected) {
                $fix = $phpcsFile->addFixableError(
                    "Expected {$expected} spaces after colon in named argument '{$name}', found {$actual}",
                    $colonPtr,
                    'SpacingAfterColon'
                );
                if ($fix) {
                    if ($actual > 0) {
                        $phpcsFile->fixer->replaceToken(
                            $colonPtr + 1,
                            str_repeat(' ', $expected)
                        );
                    } else {
                        $phpcsFile->fixer->addContent(
                            $colonPtr,
                            str_repeat(' ', $expected)
                        );
                    }
                }
            }
        }
    }
}
