<?php declare(strict_types=1);

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class UbixConcreteClassOrEnumTestCaseSniff implements Sniff
{
    /**
     * Listen for class and enum declarations.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [\T_CLASS, \T_ENUM];
    }

    /**
     * Processes the class or enum token to ensure a @coversDefaultClass annotation.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the class/enum token.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // 0) Find namespace early
        $namespacePtr = $phpcsFile->findPrevious(\T_NAMESPACE, $stackPtr);
        $namespace = '';
        if ($namespacePtr !== false) {
            $nsEnd = $phpcsFile->findNext(\T_SEMICOLON, $namespacePtr);
            $namespace = trim($phpcsFile->getTokensAsString($namespacePtr + 2, $nsEnd - $namespacePtr - 2));
        }

        // 1) Resolve use statements to map aliases to FQCNs
        $uses = [];
        foreach ($tokens as $idx => $token) {
            if ($token['code'] === \T_USE) {
                $nextString = $phpcsFile->findNext(\T_STRING, $idx + 1);
                if ($nextString === false) {
                    continue;
                }
                $endPtr = $phpcsFile->findNext([\T_AS, \T_SEMICOLON], $nextString + 1);
                $alias = $tokens[$nextString]['content'];
                if ($endPtr !== false && $tokens[$endPtr]['code'] === \T_AS) {
                    $aliasPtr = $phpcsFile->findNext(\T_STRING, $endPtr + 1);
                    if ($aliasPtr !== false) {
                        $alias = $tokens[$aliasPtr]['content'];
                    }
                }
                $fullUse = trim($phpcsFile->getTokensAsString($idx + 1, ($endPtr - ($idx + 1))));
                $uses[$alias] = ltrim($fullUse, '\\');
            }
        }

        $resolveFqcn = static function (string $name) use ($uses, $namespace): string {
            if (isset($uses[$name])) {
                return '\\' . $uses[$name];
            }
            return '\\' . ltrim($namespace . '\\' . $name, '\\');
        };

        // 2) Check extends
        $extendsOk = false;
        $extendsPtr = $phpcsFile->findNext(\T_EXTENDS, $stackPtr);
        if ($extendsPtr !== false) {
            $namePtr = $phpcsFile->findNext(\T_STRING, $extendsPtr + 1);
            if ($namePtr !== false) {
                $fqcn = $resolveFqcn($tokens[$namePtr]['content']);
                if ($fqcn === '\\Ubix\\Tests\\AbstractUbixConcreteClassOrEnumTestCase') {
                    $extendsOk = true;
                }
            }
        }

        // 3) Check implements
        $implementsOk = false;
        $implementsPtr = $phpcsFile->findNext(\T_IMPLEMENTS, $stackPtr);
        if ($implementsPtr !== false) {
            for ($ptr = $implementsPtr + 1; isset($tokens[$ptr]); $ptr++) {
                if ($tokens[$ptr]['code'] === \T_STRING) {
                    $fqcn = $resolveFqcn($tokens[$ptr]['content']);
                    if ($fqcn === '\\Ubix\\Tests\\UbixConcreteClassOrEnumTestCaseInterface') {
                        $implementsOk = true;
                        break;
                    }
                } elseif ($tokens[$ptr]['code'] === \T_OPEN_CURLY_BRACKET) {
                    break;
                }
            }
        }

        if (!$extendsOk || !$implementsOk) {
            return;
        }

        // 1) Find the DOC comment opener immediately before the class/enum.
        $docStart = $phpcsFile->findPrevious(\T_DOC_COMMENT_OPEN_TAG, $stackPtr - 1);
        if ($docStart === false) {
            $phpcsFile->addError(
                'Missing PHPDoc block with @coversDefaultClass annotation.',
                $stackPtr,
                'MissingCoversDefaultClass'
            );
            return;
        }

        $docEnd = $tokens[$docStart]['comment_closer'] ?? null;
        if ($docEnd === null) {
            // Malformed docblock; bail.
            return;
        }

        // 2) Compute expected test case FQCN
        $namespacePtr = $phpcsFile->findPrevious(\T_NAMESPACE, $stackPtr);
        $namespace = '';
        if ($namespacePtr !== false) {
            $nsEnd = $phpcsFile->findNext(\T_SEMICOLON, $namespacePtr);
            $namespace = trim($phpcsFile->getTokensAsString($namespacePtr + 2, $nsEnd - $namespacePtr - 2));
        }
        $namespace = '\\' . ltrim($namespace, '\\');
        if (str_starts_with($namespace, '\\Ubix\\Tests\\')) {
            $namespace = substr($namespace, 0, 4) . substr($namespace, 10);
        }
        $namePtr   = $phpcsFile->findNext(\T_STRING, $stackPtr);
        $className = $tokens[$namePtr]['content'];
        if (substr($className, -4) === 'Test') {
            $className = substr($className, 0, -4);
        }
        $expected  = sprintf(
            '@coversDefaultClass %s\\%s',
            $namespace,
            $className
        );

        // 3a) Enforce strict short description
        $expectedShortDescription = sprintf('PHPUnit test case for %s\\%s', $namespace, $className);

        // Find the first T_DOC_COMMENT_STRING after the doc start
        $shortDescPtr = $phpcsFile->findNext(\T_DOC_COMMENT_STRING, $docStart + 1, $docEnd);

        if ($shortDescPtr === false || trim($tokens[$shortDescPtr]['content']) !== $expectedShortDescription) {
            $fix = $phpcsFile->addFixableError(
                'Incorrect or missing short description; expected: "' . $expectedShortDescription . '"',
                $docStart,
                'FixShortDescription'
            );
            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                if ($shortDescPtr !== false) {
                    // Replace existing content
                    $phpcsFile->fixer->replaceToken($shortDescPtr, $expectedShortDescription);
                } else {
                    // Insert new short description after /** 
                    $indent = isset($tokens[$docStart + 1]['column'])
                        ? str_repeat(' ', $tokens[$docStart + 1]['column'])
                        : ' ';
                    $insert = $indent . '* ' . $expectedShortDescription . "\n";
                    $phpcsFile->fixer->addContent($docStart, $insert);
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        // 3) Scan @see tags in the docblock
        $foundValid = false;
        for ($i = $docStart; $i <= $docEnd; $i++) {
            if (
                $tokens[$i]['code'] === \T_DOC_COMMENT_TAG &&
                $tokens[$i]['content'] === '@coversDefaultClass'
            ) {
                $stringPtr = $phpcsFile->findNext(\T_DOC_COMMENT_STRING, $i + 1, $docEnd);
                $coversDefaultClassText = $stringPtr !== false ? trim($tokens[$stringPtr]['content']) : '';
                $lineText  = '@coversDefaultClass ' . $coversDefaultClassText;

                if ($lineText === $expected) {
                    $foundValid = true;
                    break;
                }
            }
        }

        // 4) If missing, report fixable error to add it
        if (!$foundValid) {
            $fix = $phpcsFile->addFixableError(
                'Missing @coversDefaultClass annotation: ' . $expected,
                $docStart,
                'AddTestCaseCoversDefaultClass'
            );
            if ($fix) {
                // Determine indent from the line after the open tag
                $indent = isset($tokens[$docStart + 1]['column'])
                    ? str_repeat(' ', $tokens[$docStart + 1]['column'])
                    : ' ';
                $insert = $indent . '* ' . $expected . "\n";

                $phpcsFile->fixer->beginChangeset();
                // Insert just before the closing doc comment token
                $phpcsFile->fixer->addContentBefore($docEnd, $insert);
                $phpcsFile->fixer->endChangeset();
            }
        }

        // 5) Ensure testFollowingUbixStandards() calls $this->testClassFollowingUbixStandards(...)
        //    We reuse $namespace and $className computed above to build the expected ::class target.
        $expectedCoveredFqcn = sprintf('\\%s\\%s', ltrim($namespace, '\\'), $className);

        $classScopeOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $classScopeCloser = $tokens[$stackPtr]['scope_closer'] ?? null;
        if ($classScopeOpener !== null && $classScopeCloser !== null) {
            // Find the testFollowingUbixStandards method within the class scope
            $methodPtr = $phpcsFile->findNext(\T_FUNCTION, $classScopeOpener + 1, $classScopeCloser);
            while ($methodPtr !== false && $methodPtr < $classScopeCloser) {
                $namePtr = $phpcsFile->findNext(\T_STRING, $methodPtr + 1, $classScopeCloser);
                if ($namePtr !== false && $tokens[$namePtr]['content'] === 'testFollowingUbixStandards') {
                    $methodOpen  = $tokens[$methodPtr]['scope_opener'] ?? null;
                    $methodClose = $tokens[$methodPtr]['scope_closer'] ?? null;
                    if ($methodOpen !== null && $methodClose !== null) {
                        // Scan for: $this -> testClassFollowingUbixStandards
                        $callFound = false;
                        for ($i = $methodOpen + 1; $i < $methodClose; $i++) {
                            if ($tokens[$i]['code'] === \T_VARIABLE && $tokens[$i]['content'] === '$this') {
                                $objOp = $phpcsFile->findNext(\T_OBJECT_OPERATOR, $i + 1, $methodClose);
                                if ($objOp !== false) {
                                    $callName = $phpcsFile->findNext(\T_STRING, $objOp + 1, $methodClose);
                                    if ($callName !== false && $tokens[$callName]['content'] === 'testClassFollowingUbixStandards') {
                                        $callFound = true;
                                        break;
                                    }
                                }
                            }
                        }

                        if (!$callFound) {
                            $fix = $phpcsFile->addFixableError(
                                'testFollowingUbixStandards() must call $this->testClassFollowingUbixStandards(<CoveredClass>::class).',
                                $namePtr,
                                'MissingTestClassFollowingCall'
                            );
                            if ($fix) {
                                // Determine indentation from the next token after the method opener
                                $indent = isset($tokens[$methodOpen + 1]['column'])
                                    ? str_repeat(' ', $tokens[$methodOpen + 1]['column'])
                                    : '    ';

                                $insert = sprintf(
                                    "%s\$this->testClassFollowingUbixStandards(%s::class);\n",
                                    $indent,
                                    $expectedCoveredFqcn
                                );

                                $phpcsFile->fixer->beginChangeset();
                                $phpcsFile->fixer->addContentBefore($methodClose, $insert);
                                $phpcsFile->fixer->endChangeset();
                            }
                        }
                    }
                    // We found (and handled) the method; no need to keep searching.
                    break;
                }

                // Keep searching for the next T_FUNCTION inside this class
                $methodPtr = $phpcsFile->findNext(\T_FUNCTION, $methodPtr + 1, $classScopeCloser);
            }
        }

        // 6) If @coversDefaultClass is correct, verify the first argument of
        //    $this->testClassFollowingUbixStandards(...) matches it and uses ::class
        if ($foundValid && isset($methodOpen, $methodClose) && $callFound) {
            // Expected FQCN from the earlier computation.
            $expectedCoveredFqcn = sprintf('\\%s\\%s', ltrim($namespace, '\\'), $className);

            // Find the exact call site again so we can parse its arguments.
            $callPtr = null;
            for ($i = $methodOpen + 1; $i < $methodClose; $i++) {
                if ($tokens[$i]['code'] === \T_VARIABLE && $tokens[$i]['content'] === '$this') {
                    $objOp = $phpcsFile->findNext(\T_OBJECT_OPERATOR, $i + 1, $methodClose);
                    if ($objOp !== false) {
                        $callName = $phpcsFile->findNext(\T_STRING, $objOp + 1, $methodClose);
                        if ($callName !== false && $tokens[$callName]['content'] === 'testClassFollowingUbixStandards') {
                            $callPtr = $callName;
                            break;
                        }
                    }
                }
            }

            if ($callPtr !== null) {
                // Get "(" and matching ")"
                $openParen = $phpcsFile->findNext(\T_OPEN_PARENTHESIS, $callPtr + 1, $methodClose);
                if ($openParen !== false) {
                    $closeParen = $tokens[$openParen]['parenthesis_closer'] ?? null;
                    if ($closeParen !== null) {
                        // Identify first-argument token range: [argStart, argEnd]
                        $argStart = $phpcsFile->findNext([T_WHITESPACE, T_COMMENT], $openParen + 1, $closeParen, true);
                        $argEnd   = $argStart;
                        if ($argStart !== false) {
                            // First arg ends at a top-level comma or the close paren.
                            $depth = 0;
                            for ($j = $argStart; $j < $closeParen; $j++) {
                                if ($tokens[$j]['code'] === \T_OPEN_PARENTHESIS) {
                                    $depth++;
                                } elseif ($tokens[$j]['code'] === \T_CLOSE_PARENTHESIS) {
                                    if ($depth === 0) {
                                        break;
                                    }
                                    $depth--;
                                } elseif ($tokens[$j]['code'] === \T_COMMA && $depth === 0) {
                                    break;
                                }
                                $argEnd = $j;
                            }

                            // Helper to capture the raw first-arg string for diagnostics
                            $rawFirstArg = trim($phpcsFile->getTokensAsString($argStart, $argEnd - $argStart + 1));

                            // 6a) Reject quoted strings outright.
                            if ($tokens[$argStart]['code'] === \T_CONSTANT_ENCAPSED_STRING) {
                                $fix = $phpcsFile->addFixableError(
                                    'First argument to testClassFollowingUbixStandards() must be <CoveredClass>::class, not a quoted string.',
                                    $argStart,
                                    'FirstArgMustBeClassConst'
                                );
                                if ($fix) {
                                    $phpcsFile->fixer->beginChangeset();
                                    $phpcsFile->fixer->replaceTokenRange(
                                        $argStart,
                                        $argEnd,
                                        $expectedCoveredFqcn . '::class'
                                    );
                                    $phpcsFile->fixer->endChangeset();
                                }
                            } else {
                                // 6b) Parse foo\bar::class (possibly with leading "\" or imported alias)
                                // Find the "::"
                                $doubleColon = $phpcsFile->findNext(\T_DOUBLE_COLON, $argStart, $argEnd + 1);
                                if ($doubleColon === false) {
                                    // No "::" → not ::class usage.
                                    $fix = $phpcsFile->addFixableError(
                                        'First argument to testClassFollowingUbixStandards() must use ::class.',
                                        $argStart,
                                        'FirstArgMustUseClassConst'
                                    );
                                    if ($fix) {
                                        $phpcsFile->fixer->beginChangeset();
                                        $phpcsFile->fixer->replaceTokenRange(
                                            $argStart,
                                            $argEnd,
                                            $expectedCoveredFqcn . '::class'
                                        );
                                        $phpcsFile->fixer->endChangeset();
                                    }
                                } else {
                                    // Confirm right side is "class"
                                    $rightIdent = $phpcsFile->findNext([T_STRING, T_WHITESPACE], $doubleColon + 1, $argEnd + 1, true);
                                    $classWord  = $phpcsFile->findNext(T_STRING, $doubleColon + 1, $argEnd + 1);
                                    $isClassKeyword = ($classWord !== false && strtolower($tokens[$classWord]['content']) === 'class');

                                    if (!$isClassKeyword) {
                                        $fix = $phpcsFile->addFixableError(
                                            'First argument must end with ::class.',
                                            $argStart,
                                            'FirstArgMustEndWithClassConst'
                                        );
                                        if ($fix) {
                                            $phpcsFile->fixer->beginChangeset();
                                            $phpcsFile->fixer->replaceTokenRange(
                                                $argStart,
                                                $argEnd,
                                                $expectedCoveredFqcn . '::class'
                                            );
                                            $phpcsFile->fixer->endChangeset();
                                        }
                                    } else {
                                        // Collect the left side tokens forming the class name (could be "\Foo\Bar" or alias "Bar")
                                        $leftStart = $argStart;
                                        // Skip leading whitespace/comments
                                        while ($leftStart < $doubleColon && in_array($tokens[$leftStart]['code'], [T_WHITESPACE, T_COMMENT], true)) {
                                            $leftStart++;
                                        }
                                        $leftEnd = $doubleColon - 1;
                                        while ($leftEnd > $leftStart && in_array($tokens[$leftEnd]['code'], [T_WHITESPACE, T_COMMENT], true)) {
                                            $leftEnd--;
                                        }

                                        $leftRaw = $leftStart <= $leftEnd
                                            ? trim($phpcsFile->getTokensAsString($leftStart, $leftEnd - $leftStart + 1))
                                            : '';

                                        // Resolve to FQCN using the same strategy as earlier:
                                        $resolveNameToFqcn = function (string $name) use ($uses, $namespace): string {
                                            // Absolute?
                                            if ($name !== '' && $name[0] === '\\') {
                                                return $name;
                                            }
                                            // If qualified (Foo\Bar), try replacing first segment via use-alias map.
                                            if (strpos($name, '\\') !== false) {
                                                [$first, $rest] = explode('\\', $name, 2);
                                                if (isset($uses[$first])) {
                                                    return '\\' . $uses[$first] . '\\' . $rest;
                                                }
                                                // Not an alias → treat as relative to current namespace
                                                return '\\' . ltrim($namespace . '\\' . $name, '\\');
                                            }
                                            // Single segment: alias or current namespace
                                            if (isset($uses[$name])) {
                                                return '\\' . $uses[$name];
                                            }
                                            return '\\' . ltrim($namespace . '\\' . $name, '\\');
                                        };

                                        $resolvedArgFqcn = $resolveNameToFqcn($leftRaw);

                                        if ($resolvedArgFqcn !== $expectedCoveredFqcn) {
                                            $fix = $phpcsFile->addFixableError(
                                                sprintf(
                                                    'First argument must reference the covered class (%s::class). Found %s::class.',
                                                    $expectedCoveredFqcn,
                                                    $resolvedArgFqcn
                                                ),
                                                $argStart,
                                                'FirstArgWrongCoveredClass'
                                            );
                                            if ($fix) {
                                                $phpcsFile->fixer->beginChangeset();
                                                $phpcsFile->fixer->replaceTokenRange(
                                                    $argStart,
                                                    $argEnd,
                                                    $expectedCoveredFqcn . '::class'
                                                );
                                                $phpcsFile->fixer->endChangeset();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
