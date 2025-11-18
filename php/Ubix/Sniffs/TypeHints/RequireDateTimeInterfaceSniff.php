<?php declare(strict_types=1);

namespace Ubix\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class RequireDateTimeInterfaceSniff implements Sniff
{

    /**
     * @var array<string, string> Aliases for use statements (alias => FQN)
     */
    private array $useAliases = [];

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int>
     */
    public function register(): array
    {
        return [T_FUNCTION];
    }

    /**
     * Whether we've already added the DateTimeInterface import.
     *
     * @var bool
     */
    private bool $importedDateTimeInterface = false;

    public function process(File $phpcsFile, $stackPtr): void
    {
        $this->parseUseStatements($phpcsFile);

        $tokens = $phpcsFile->getTokens();

        //
        // 1) PHPDoc @param and @return tags
        //
        $docOpen = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $stackPtr, null, false);
        if ($docOpen !== false) {
            $docClose = $tokens[$docOpen]['comment_closer'] ?? null;
            if ($docClose !== null) {
                for ($i = $docOpen; $i <= $docClose; $i++) {
                    if (
                        $tokens[$i]['code'] === T_DOC_COMMENT_TAG
                        && in_array($tokens[$i]['content'], ['@param','@return'], true)
                    ) {
                        $tag  = $tokens[$i]['content'];
                        $next = $phpcsFile->findNext(
                            T_DOC_COMMENT_WHITESPACE,
                            $i + 1,
                            $docClose,
                            true
                        );
                        if (!isset($tokens[$next])) {
                            continue;
                        }

                        // The entire string token (e.g. "int|null   $modelId  description...")
                        $original = $tokens[$next]['content'];

                        // Extract just the type (stop at first space)
                        $typePart = strpos($original, ' ')
                            ? substr($original, 0, strpos($original, ' '))
                            : $original;

                        // Flag any DateTime use (including shorthand ?DateTime) via our fixer helper
                        if (stripos($typePart, 'DateTime') !== false) {
                            $this->processTypeHint(
                                $phpcsFile,
                                $tag,
                                $typePart,
                                $next,
                                $next,
                                'Doc' . ucfirst(trim($tag, '@'))
                            );
                        }
                    }
                }
            }
        }

        //
        // 2) Find the "(" that opens the param list
        //
        $open = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr + 1, null, false);
        if ($open === false) {
            return;
        }

        //
        // 3) Manually find the matching ")"
        //
        $depth = 1;
        $close = null;
        $max   = count($tokens);
        for ($i = $open + 1; $i < $max; $i++) {
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $depth++;
            } elseif ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                $depth--;
                if ($depth === 0) {
                    $close = $i;
                    break;
                }
            }
        }
        if ($close === null) {
            return;
        }

        //
        // 4) Scan each parameter and capture start/end of the union text
        //
        for ($i = $open + 1; $i < $close; $i++) {
            if ($tokens[$i]['code'] !== T_VARIABLE) {
                continue;
            }
            // Back up to skip whitespace
            $prev = $i - 1;
            while ($prev > $open && $tokens[$prev]['code'] === T_WHITESPACE) {
                $prev--;
            }

            // Gather contiguous union text and record start/end pointers
            $parts     = [];
            $hintStart = null;
            $hintEnd   = $prev;
            for ($j = $prev; $j > $open; $j--) {
                $text = $tokens[$j]['content'];
                if (preg_match('/^[\?\\\\\w\|]+$/', $text)) {
                    $hintStart = $j;
                    array_unshift($parts, $text);
                } else {
                    break;
                }
            }
            $hint = $parts ? implode('', $parts) : 'mixed';

            $this->processTypeHint(
                $phpcsFile,
                'Param',
                $hint,
                $hintStart ?? $i,
                $hintEnd,
                'Param'
            );
        }

        //
        // 5) Scan return type
        //
        $after = $close + 1;
        if (isset($tokens[$after]) && $tokens[$after]['code'] === T_COLON) {
            // Skip whitespace/comments
            $k = $after + 1;
            while (
                isset($tokens[$k])
                && in_array($tokens[$k]['code'], [
                    T_WHITESPACE,
                    T_COMMENT,
                    T_DOC_COMMENT_WHITESPACE,
                ], true)
            ) {
                $k++;
            }

            // Collect contiguous union text
            $parts     = [];
            $hintStart = $k;
            $hintEnd   = $k;
            for (; isset($tokens[$k]); $k++) {
                $text = $tokens[$k]['content'];
                if (preg_match('/^[\?\\\\\w\|]+$/', $text)) {
                    $parts[]  = $text;
                    $hintEnd  = $k;
                } else {
                    break;
                }
            }
            $retType = $parts ? implode('', $parts) : 'mixed';

            $this->processTypeHint(
                $phpcsFile,
                'Return type',
                $retType,
                $hintStart,
                $hintEnd,
                'Return'
            );
        } else {
            // no explicit return → mixed
            $this->processTypeHint(
                $phpcsFile,
                'Return type',
                'mixed',
                $stackPtr,
                $stackPtr,
                'Return'
            );
        }
    }

    /**
     * @param File   $phpcsFile
     * @param string $context    'Param', '@param', 'Return type', etc.
     * @param string $typeHint   the raw hint text (e.g. "string|null")
     * @param int    $startPtr   first token to replace
     * @param int    $endPtr     last  token to replace
     * @param string $errorLabel PHPCS code
     */
    private function processTypeHint(
        File $phpcsFile,
        string $context,
        string $typeHint,
        int $startPtr,
        int $endPtr,
        string $errorLabel
    ): void {
        $usesDateTimeType = false;
        foreach (explode('|', ltrim($typeHint, '?')) as $type) {
                // Check if DateTime is an alias for Ubix\DataType\DateTime\DateTime


                if (
                    ($type === 'DateTime' || $type === '\DateTime')
                    && isset($this->useAliases['DateTime'])
                    && $this->useAliases['DateTime'] === 'Ubix\DataType\DateTime\DateTime'
                ) {
                    // Skip replacement for this case
                    continue;
                }

            switch ($type) { // TODO: should I be including more types that implement the DateTimeInterface? eg. DateTimeImmutable?
                case 'DateTime':
                case '\DateTime':
                    $usesDateTimeType = true;
                    break;
            }
        }

        if ($usesDateTimeType) {
            $expected = '';
            if (str_starts_with($typeHint, '?')) {
                $expected .= '?';
            }
            foreach (explode('|', ltrim($typeHint, '?')) as $type) {
                if (strlen($expected) > 0 && $expected !== '?') {
                    $expected .= '|';
                }

                switch ($type) {
                    case 'DateTime':
                    case '\DateTime':
                        $expected .= 'DateTimeInterface';
                        break;

                    default:
                        $expected .= $type;
                        break;
                }
            }

            $fix = $phpcsFile->addFixableError(
                "{$context} must use DateTimeInterface isntead of DateTime, change `{$typeHint}` to `{$expected}`",
                $startPtr,
                $errorLabel
            );

            if ($fix) {
                // on first fix, ensure `use DateTimeInterface;` is present
                if (!$this->importedDateTimeInterface) {
                    $this->addDateTimeInterfaceImport($phpcsFile);
                    $this->importedDateTimeInterface = true;
                }
                $phpcsFile->fixer->beginChangeset();

                // Doc-block fixes: only swap out the type, preserve var name & description
                if (strpos($errorLabel, 'Doc') === 0) {
                    $tokens  = $phpcsFile->getTokens();
                    $content = $tokens[$startPtr]['content'];

                    // Try to split "Type   $var  desc..." into [lead][type][spaces][rest]
                    if (preg_match(
                        '/^(\s*)' . preg_quote($typeHint, '/') . '(\s+)(.*)$/',
                        $content,
                        $m
                    )) {
                        $new = $m[1] . $expected . $m[2] . $m[3];
                    } else {
                        // Fallback: replace first occurrence of the type
                        $new = preg_replace(
                            '/' . preg_quote($typeHint, '/') . '/',
                            $expected,
                            $content,
                            1
                        );
                    }

                    $phpcsFile->fixer->replaceToken($startPtr, $new);
                } else {
                    // Real code hints: replace the entire union text
                    for ($ptr = $startPtr; $ptr <= $endPtr; $ptr++) {
                        $phpcsFile->fixer->replaceToken(
                            $ptr,
                            $ptr === $startPtr ? $expected : ''
                        );
                    }
                }

                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Insert a `use DateTimeInterface;` at the top of the file if it's missing.
     */
    private function addDateTimeInterfaceImport(File $phpcsFile): void
    {
        $tokens = $phpcsFile->getTokens();
        // Quick scan: if already imported, do nothing
        $allCode = $phpcsFile->getTokensAsString(0, $phpcsFile->numTokens);
        if (strpos($allCode, 'use DateTimeInterface;') !== false) {
            return;
        }

        // Determine where to insert: after namespace (if any), otherwise after open tag
        $insertPtr = $phpcsFile->findNext(T_NAMESPACE, 0, null, false);
        if ($insertPtr !== false) {
            // after `namespace X;`
            $insertPtr = $phpcsFile->findNext(T_SEMICOLON, $insertPtr, null, false);
        } else {
            // after `<?php`
            $insertPtr = $phpcsFile->findNext(T_OPEN_TAG, 0, null, false);
        }

        // Add the import line
        $phpcsFile->fixer->addContent($insertPtr, "\n" . 'use DateTimeInterface;' . "\n");
    }

    /**
     * Parse use statements and fill $useAliases.
     */
    private function parseUseStatements(File $phpcsFile): void
    {
        $tokens = $phpcsFile->getTokens();
        $this->useAliases = [];

        $ptr = 0;
        while (($ptr = $phpcsFile->findNext(T_USE, $ptr)) !== false) {
            $end = $phpcsFile->findNext([T_SEMICOLON], $ptr);
            $useFqn = '';
            $alias = '';
            $asPtr = $phpcsFile->findNext(T_AS, $ptr, $end);

            // Collect FQN
            for ($i = $ptr + 1; $i < ($asPtr ?: $end); $i++) {
                if (in_array($tokens[$i]['code'], [T_STRING, T_NS_SEPARATOR])) {
                    $useFqn .= $tokens[$i]['content'];
                }
            }

            // Collect alias
            if ($asPtr) {
                $aliasPtr = $phpcsFile->findNext(T_STRING, $asPtr, $end);
                if ($aliasPtr) {
                    $alias = $tokens[$aliasPtr]['content'];
                }
            } else {
                // If no alias, use last part of FQN
                $parts = explode('\\', $useFqn);
                $alias = end($parts);
            }

            if ($alias && $useFqn) {
                $this->useAliases[$alias] = $useFqn;
            }

            $ptr = $end + 1;
        }
    }
}
