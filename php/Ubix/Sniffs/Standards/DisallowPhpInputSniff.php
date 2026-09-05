<?php declare(strict_types=1);

namespace Ubix\Sniffs\CodeQuality;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowPhpInputSniff implements Sniff
{
    public function register(): array
    {
        // Catch every string literal in the file
        return [T_CONSTANT_ENCAPSED_STRING];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $token    = $phpcsFile->getTokens()[$stackPtr];
        $content  = $token['content'];

        // Trim the surrounding quotes (single or double)
        $inner = substr($content, 1, -1);

        if (strtolower($inner) === 'php://input') {
            $phpcsFile->addError(
                'Use of php://input is forbidden, use PSR request objects and \Psr\Http\Message\ServerRequestInterface->getBody()->getContents() instead.',
                $stackPtr,
                'FoundPhpInput'
            );
        }
    }
}
