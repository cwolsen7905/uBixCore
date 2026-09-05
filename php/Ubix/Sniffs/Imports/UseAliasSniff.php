<?php
namespace Ubix\Sniffs\Imports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class UseAliasSniff implements Sniff
{
    /**
     * One-off FQCN overrides and their desired alias.
     *
     * @var string[]
     */
    public $aliasOverrides = [];

    private $currentNamespace = '';

    /**
     * Register tokens this sniff listens for.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [T_NAMESPACE, T_USE, T_CLASS];
    }

    /**
     * Process a use statement to enforce alias rules.
     *
     * @param File $phpcsFile
     * @param int  $stackPtr  Position of the T_USE token.
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Capture namespace
        if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
            $start = $phpcsFile->findNext([T_WHITESPACE], $stackPtr + 1, null, true);
            $end   = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $stackPtr + 1) - 1;
            $this->currentNamespace = $this->extractFqcn($phpcsFile, $start, $end);
            return;
        }

        // Handle class declarations: check extends/implements for missing aliases
        if ($tokens[$stackPtr]['code'] === T_CLASS) {
            $this->processClassDeclaration($phpcsFile, $stackPtr);
            return;
        }
        // -- fall through to your existing T_USE logic below --


        // Skip closures (use in closures) and function/const imports
        if ($this->isClosureUse($tokens, $stackPtr) || $this->isFunctionOrConstUse($phpcsFile, $stackPtr)) {
            return;
        }

        // Find end of FQCN or T_AS
        $endPtr = $phpcsFile->findNext([T_SEMICOLON, T_AS], $stackPtr + 1);
        $fqcn   = $this->extractFqcn($phpcsFile, $stackPtr + 1, $endPtr - 1);
        $short  = $this->getShortName($fqcn);

        $aliasPtr = null;
        $alias    = '';
        if ($tokens[$endPtr]['code'] === T_AS) {
            $aliasPtr = $phpcsFile->findNext(T_STRING, $endPtr + 1);
            $alias    = $tokens[$aliasPtr]['content'];
        }

        // Determine expected alias
        if (isset($this->aliasOverrides[$fqcn])) {
            $expected = $this->aliasOverrides[$fqcn];
        } elseif (substr($short, -9) === 'Interface') {
            $expected = substr($short, 0, -9);
        } elseif (strpos($short, 'Abstract') === 0) {
            $expected = substr($short, 8);
        } else {
            $expected = '';
        }

        // If a non-trivial alias is expected (and it would differ from the short name) but none is present, error.
        // Compare to $short (not FQCN) to avoid requiring useless aliases like `as AbstractSimpleCache`.
        if ($expected !== '' && $expected !== $short && $aliasPtr === null) {
            $error = sprintf(
                'Import of "%s" requires alias "%s".',
                $fqcn,
                $expected
            );
            $phpcsFile->addError($error, $stackPtr, 'MissingAlias');
            return;
        }

        // If an alias is present but we didn't expect any alias at all, forbid ad-hoc aliases.
        // (Opt-in behavior: flip this block on if you want to disallow arbitrary aliases.)
        if ($aliasPtr !== null && $expected === '') {
            $phpcsFile->addError(
                sprintf('Unexpected alias "%s" for "%s". All custom aliases must be defined in the phpcs.xml configuration file.', $alias, $fqcn),
                $aliasPtr,
                'UnexpectedAlias'
            );
            return;
        }

        // Only report when alias is present AND a specific alias is actually expected.
        // Otherwise Slevomat\UselessAlias would normally handle trivial "as ShortName" cases.
        if ($aliasPtr !== null && $expected !== '' && $alias !== $expected) {
            $error = sprintf(
                'Alias "%s" for "%s" is invalid; expected "%s".',
                $alias,
                $fqcn,
                $expected ?: $short
            );
            $phpcsFile->addError($error, $aliasPtr, 'InvalidAlias');
        }
    }

    /**
     * Extract the FQCN from tokens between two pointers.
     */
    private function extractFqcn(File $phpcsFile, int $start, int $end): string
    {
        $tokens = $phpcsFile->getTokens();
        $parts = [];
        for ($i = $start; $i <= $end; $i++) {
            if (in_array($tokens[$i]['code'], [T_STRING, T_NS_SEPARATOR], true)) {
                $parts[] = $tokens[$i]['content'];
            }
        }

        $fqcn = implode('', $parts);
        // Normalize leading backslashes so config matches consistently
        return ltrim($fqcn, '\\');
    }

    /**
     * Get the short class name from the FQCN.
     */
    private function getShortName(string $fqcn): string
    {
        if (false !== $pos = strrpos($fqcn, '\\')) {
            return substr($fqcn, $pos + 1);
        }

        return $fqcn;
    }

    /**
     * Detect closure imports (skip these).
     */
    private function isClosureUse(array $tokens, int $stackPtr): bool
    {
        if (empty($tokens[$stackPtr]['conditions'])) {
            return false;
        }

        foreach ($tokens[$stackPtr]['conditions'] as $condType) {
            if ($condType === T_CLOSURE) {
                return true;
            }
        }

        return false;
    }

    /**
     * Skip "use function" and "use const" statements.
     */
    private function isFunctionOrConstUse(File $phpcsFile, int $stackPtr): bool
    {
        $nextPtr = $phpcsFile->findNext([T_WHITESPACE], $stackPtr + 1, null, true);
        return in_array($phpcsFile->getTokens()[$nextPtr]['code'], [T_FUNCTION, T_CONST], true);
    }

    /**
     * Process a class declaration to enforce aliases on extends/implements.
     */
    private function processClassDeclaration(File $phpcsFile, int $stackPtr): void
    {
        $tokens    = $phpcsFile->getTokens();
        // Use the namespace we captured from the T_NAMESPACE token
        $namespace = $this->currentNamespace;

        // Walk through any EXTENDS / IMPLEMENTS clauses
        $pointer = $phpcsFile->findNext([T_EXTENDS, T_IMPLEMENTS], $stackPtr);
        while ($pointer !== false) {
            // Find the start of the type name
            $start = $phpcsFile->findNext([T_WHITESPACE], $pointer + 1, null, true);
            // Find the end (before next keyword or brace)
            $end   = $phpcsFile->findNext([T_WHITESPACE, T_EXTENDS, T_IMPLEMENTS, T_OPEN_CURLY_BRACKET], $start + 1) - 1;
            $fqcn  = $this->extractFqcn($phpcsFile, $start, $end);

            // If there's no backslash, prepend our current namespace
            if (strpos($fqcn, '\\') === false && $namespace !== '') {
                $fqcn = $namespace . '\\' . $fqcn;
            }

            $short    = $this->getShortName($fqcn);
            $expected = $this->getExpectedAlias($fqcn, $short);

            // Don't require an alias if it would be identical to the short name.
            if ($expected !== '' && $expected !== $short) {
                $error = sprintf(
                    'Type "%s" requires alias "%s".',
                    $fqcn,
                    $expected
                );
                $phpcsFile->addError($error, $start, 'MissingAlias');
            }

            // Look for the next EXTENDS or IMPLEMENTS in this declaration
            $pointer = $phpcsFile->findNext([T_EXTENDS, T_IMPLEMENTS], $pointer + 1);
        }
    }

    /**
     * Extracted from your existing logic—centralizes alias‐determination.
     */
    private function getExpectedAlias(string $fqcn, string $short): string
    {
        // Normalize to match aliasOverrides keys (which don’t include leading "\")
        $normalized = ltrim($fqcn, '\\');
        if (isset($this->aliasOverrides[$normalized])) {
            return $this->aliasOverrides[$normalized];
        } elseif (substr($short, -9) === 'Interface') {
            return substr($short, 0, -9);
        } elseif (strpos($short, 'Abstract') === 0) {
            return substr($short, 8);
        }
        return '';
    }
}
