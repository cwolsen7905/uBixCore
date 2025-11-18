<?php declare(strict_types=1);

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowAssertEqualsSniff implements Sniff
{
    public function register(): array
    {
        // Look for object operators (`->`)
        return [T_OBJECT_OPERATOR];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Ensure the variable before `->` is `$this`
        $prev = $phpcsFile->findPrevious(T_VARIABLE, $stackPtr - 1, null, false, '$this');
        if ($prev === false) {
            return;
        }

        // Ensure the method name after `->` is "assertEquals"
        $next = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
        if ($next === false) {
            return;
        }

        if (strcasecmp($tokens[$next]['content'], 'assertEquals') === 0) {
            $phpcsFile->addError(
                'Use assertSame() instead of assertEquals() in PHPUnit tests.',
                $next,
                'Found'
            );
        }
    }
}
