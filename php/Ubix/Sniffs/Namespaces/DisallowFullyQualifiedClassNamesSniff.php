<?php

namespace Ubix\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Disallows fully qualified class names outside of use or namespace declarations.
 * Encourages importing classes via `use` statements instead.
 */
class DisallowFullyQualifiedClassNamesSniff implements Sniff
{
    /**
     * Tracks the last token position of a processed fully-qualified name to avoid duplicate errors.
     *
     * @var int
     */
    private int $lastFqcnEnd = 0;

    /**
     * Register to listen for namespace-separator tokens.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [T_NS_SEPARATOR];
    }

    /**
     * Process each namespace-separator and flag if it's part of a fully-qualified class name
     * outside of a use or namespace declaration.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The token position for a backslash (T_NS_SEPARATOR).
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        // Skip if this position is within a previously reported FQCN
        if ($stackPtr <= $this->lastFqcnEnd) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Skip use declarations
        $usePtr = $phpcsFile->findPrevious(T_USE, $stackPtr);
        if ($usePtr !== false) {
            $semicolon = $phpcsFile->findNext(T_SEMICOLON, $usePtr);
            if ($semicolon !== false && $semicolon > $stackPtr) {
                return;
            }
        }

        // Skip namespace declarations
        $nsPtr = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($nsPtr !== false) {
            $semicolon = $phpcsFile->findNext(T_SEMICOLON, $nsPtr);
            if ($semicolon !== false && $semicolon > $stackPtr) {
                return;
            }
        }

        // Ensure next token is string, part of FQCN
        $nextPtr = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
        if ($nextPtr === false) {
            return;
        }

        // Gather full FQCN extent
        $end = $stackPtr;
        $pos = $stackPtr + 1;
        while (isset($tokens[$pos]) && in_array($tokens[$pos]['code'], [T_NS_SEPARATOR, T_STRING], true)) {
            $end = $pos;
            $pos++;
        }

        // Mark end to avoid multiple errors for same FQCN
        $this->lastFqcnEnd = $end;

        // Emit single error for this FQCN
        $phpcsFile->addError(
            'Use a use-statement instead of a fully qualified class name',
            $stackPtr,
            'FullyQualifiedClassName'
        );
    }
}
