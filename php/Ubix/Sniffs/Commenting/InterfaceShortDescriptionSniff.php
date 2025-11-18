<?php

namespace Ubix\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Ensures that every interface has a PHPDoc with a non-empty short description.
 */
class InterfaceShortDescriptionSniff implements Sniff
{
    /**
     * Register for interface declarations.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [T_INTERFACE];
    }

    /**
     * Process the interface token and verify PHPDoc.
     *
     * @param File $phpcsFile The file under inspection.
     * @param int  $stackPtr  The position of the interface token.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Find the doc comment close tag immediately preceding the interface
        $commentEnd = $phpcsFile->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $stackPtr - 1);

        if ($commentEnd === false) {
            // No doc comment found
            $name  = $phpcsFile->getDeclarationName($stackPtr);
            $phpcsFile->addError(
                'Missing PHPDoc for interface %s',
                $stackPtr,
                'MissingDocComment',
                [$name]
            );
            return;
        }

        // Get the opening tag of the doc comment
        $commentStart = $tokens[$commentEnd]['comment_opener'];

        // Scan for a non-tag doc comment string (short description)
        $hasDescription = false;
        for ($i = $commentStart + 1; $i < $commentEnd; $i++) {
            if ($tokens[$i]['code'] === T_DOC_COMMENT_STRING) {
                $text = trim((string) $tokens[$i]['content']);
                if ($text !== '' && $text[0] !== '@') {
                    $hasDescription = true;
                    break;
                }
            }
        }

        if (!$hasDescription) {
            $name = $phpcsFile->getDeclarationName($stackPtr);
            $phpcsFile->addError(
                'Interface %s must have a PHPDoc short description',
                $commentStart,
                'MissingShortDescription',
                [$name]
            );
        }
    }
}
