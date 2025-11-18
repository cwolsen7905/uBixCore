<?php
namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ThrowSlimExceptionSniff implements Sniff
{
    /**
     * The current file namespace.
     *
     * @var string
     */
    private $currentNamespace = '';

    /**
     * Map of alias → fully-qualified class from file-scope use statements.
     *
     * @var string[]
     */
    private $useMap = [];

    /**
     * Listen for namespace declarations and throw statements.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [
            T_OPEN_TAG,
            T_NAMESPACE,
            T_USE,
            T_THROW,
        ];
    }

    /**
     * Process each relevant token.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the token in the stack.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // 1) Reset state at start of file.
        if ($tokens[$stackPtr]['code'] === T_OPEN_TAG) {
            $this->currentNamespace = '';
            $this->useMap          = [];
            return;
        }

        // 2) Track file-scope use/import statements.
        if ($tokens[$stackPtr]['code'] === T_USE) {
            // Only parse top-level uses (skip trait use).
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
            if ($prev !== false
                && in_array($tokens[$prev]['code'], [T_OPEN_TAG, T_NAMESPACE, T_SEMICOLON], true)
            ) {
                $fullName = '';
                $alias    = '';
                $end      = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
                for ($i = $stackPtr + 1; $i < $end; $i++) {
                    $code = $tokens[$i]['code'];
                    $cont = $tokens[$i]['content'];
                    if (in_array($code, [T_STRING, T_NS_SEPARATOR], true)) {
                        $fullName .= $cont;
                    } elseif ($code === T_AS) {
                        $aliasPtr = $phpcsFile->findNext(T_STRING, $i + 1, $end);
                        if ($aliasPtr) {
                            $alias = $tokens[$aliasPtr]['content'];
                        }
                    }
                }
                if ($fullName !== '') {
                    if ($alias === '') {
                        $parts = explode('\\', $fullName);
                        $alias = array_pop($parts);
                    }
                    $this->useMap[$alias] = $fullName;
                }
            }
            return;
        }

        // 3) Capture the namespace for later checks.
        if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
            $this->currentNamespace = $this->extractNamespace($phpcsFile, $stackPtr);
            return;
        }

        // 4) Only care about "throw" statements.
        if ($tokens[$stackPtr]['code'] !== T_THROW) {
            return;
        }

        // 5) If we're in Ubix\Controller\* or Ubix\Middleware\*, skip.
        if (
            $this->currentNamespace === 'Ubix\\Controller'
            || stripos($this->currentNamespace, 'Ubix\\Controller\\') === 0
            || $this->currentNamespace === 'Ubix\\Middleware'
            || stripos($this->currentNamespace, 'Ubix\\Middleware\\') === 0
        ) {
            return;
        }

        // 6) Find the "new" after "throw".
        $newPtr = $phpcsFile->findNext(T_NEW, $stackPtr + 1);
        if ($newPtr === false) {
            return;
        }

        // 7) Grab the class name tokens immediately following.
        $namePtr = $phpcsFile->findNext(
            [T_NS_SEPARATOR, T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED],
            $newPtr + 1
        );
        if ($namePtr === false) {
            return;
        }

        // 8) Reconstruct the full class name.
        $className = '';
        for ($i = $namePtr; $i < $phpcsFile->numTokens; $i++) {
            $code = $tokens[$i]['code'];
            if (! in_array($code, [T_NS_SEPARATOR, T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
                break;
            }
            $className .= $tokens[$i]['content'];
        }
        $className = ltrim($className, '\\');

        // 9) Resolve unqualified alias via useMap.
        if (strpos($className, '\\') === false
            && isset($this->useMap[$className])
        ) {
            $className = $this->useMap[$className];
        }

        // 10) If it’s a Slim\Exception\* class, report.
        if (stripos($className, 'Slim\\Exception\\') === 0) {
            $phpcsFile->addError(
                'Throwing exceptions from the Slim\Exception\* namespace is only allowed inside Ubix\Controller\* or Ubix\Middleware\* namespaces.',
                $stackPtr,
                'SlimExceptionNotAllowed'
            );
        }
    }

    /**
     * Extract the declared namespace name.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $ptr       Position of the T_NAMESPACE token.
     *
     * @return string
     */
    private function extractNamespace(File $phpcsFile, int $ptr): string
    {
        $tokens   = $phpcsFile->getTokens();
        $namespace = '';
        $end       = $phpcsFile->findNext(T_SEMICOLON, $ptr);
        for ($i = $ptr + 2; $i < $end; $i++) {
            if ($tokens[$i]['code'] === T_STRING
                || $tokens[$i]['code'] === T_NS_SEPARATOR
            ) {
                $namespace .= $tokens[$i]['content'];
            }
        }
        return $namespace;
    }
}
