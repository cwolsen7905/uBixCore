<?php declare(strict_types=1);

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ConstructorMethodBanSniff implements Sniff
{
    public function register(): array
    {
        // Only look at identifiers (method names)
        return [T_STRING];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // 1) Only continue when it's literally "addDefinitions"
        if ($tokens[$stackPtr]['content'] !== 'addDefinitions') {
            return;
        }

        // 2) Find the "(" that opens the call
        $openParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        if ($openParen === false) {
            return;
        }

        // 3) Make sure it has a matching ")"
        $closeParen = $tokens[$openParen]['parenthesis_closer'] ?? null;
        if ($closeParen === null) {
            return;
        }

        // 4) Scan every T_OBJECT_OPERATOR in the entire addDefinitions(...) args
        for ($i = $openParen + 1; $i < $closeParen; $i++) {
            if ($tokens[$i]['code'] === T_OBJECT_OPERATOR) {
                $nextString = $phpcsFile->findNext(
                    T_STRING,
                    $i + 1,
                    $closeParen
                );
                if ($nextString !== false) {
                    $methodName = $tokens[$nextString]['content'];
                    if ($methodName === 'constructor') {
                        $phpcsFile->addError(
                            "Banned method “->{$methodName}()” found, use “->constructorParameter()” instead",
                            $nextString,
                            'ConstructorMethodFound'
                        );
                    }
                }
            }
        }
    }
}
