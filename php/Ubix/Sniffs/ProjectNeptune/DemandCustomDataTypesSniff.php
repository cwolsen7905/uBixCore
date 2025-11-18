<?php

declare(strict_types=1);

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ReflectionClass;
use Ubix\DataType\AbstractDataType as DataType;
use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Model\AbstractModel as Model;

/**
 * Debug-only sniff: for each class/trait/interface method, print its return type
 * and each parameter's name & type. No enforcement yet.
 */
final class DemandCustomDataTypesSniff implements Sniff
{
    public function register(): array
    {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Only act on methods inside classes/traits/interfaces.
        $conditions = $tokens[$stackPtr]['conditions'] ?? [];
        if ($conditions === []) {
            return;
        }
        $lastCond = array_key_last($conditions);
        if ($lastCond === null) {
            return;
        }
        $condCode = $tokens[$lastCond]['code'] ?? null;
        if (!in_array($condCode, [T_CLASS, T_ANON_CLASS, T_TRAIT, T_INTERFACE], true)) {
            return;
        }

        // Method name
        $methodName = $phpcsFile->getDeclarationName($stackPtr) ?? '{anonymous}';

        // Skip __construct
        if (strtolower($methodName) === '__construct') {
            return;
        }

        // Return type
        $returnType = $this->extractReturnType($phpcsFile, $stackPtr);
        if ($returnType !== null && $returnType !== '') {
            $returnType = $this->resolveTypeToFqcn($phpcsFile, $returnType);
        }
        $returnType = ltrim($returnType, '?') ?? 'N/A';

        if (!$this->isAcceptableType($returnType, true)) {
            $phpcsFile->addError(
                sprintf(
                    '%s() return type is `%s` but must be void, bool, array, an enumeration, a VSM DataType, a VSM DTO or a VSM Model',
                    $methodName,
                    $returnType,
                ),
                $stackPtr,
                'InvalidReturnType',
            );
        }

        // Parameters: name + type
        foreach ($this->extractParameters($phpcsFile, $stackPtr) as $param) {
            $typeOut = $param['type'] ?? null;
            if ($typeOut !== null && $typeOut !== '') {
                $typeOut = $this->resolveTypeToFqcn($phpcsFile, $typeOut);
            }
            $typeOut = ltrim($typeOut, '?') ?? 'N/A';

            if (!$this->isAcceptableType($typeOut, true)) {
                $phpcsFile->addError(
                    sprintf(
                        '%s() parameter %s type is `%s` but must be bool, array, an enumeration, a VSM DataType, a VSM DTO or a VSM Model',
                        $methodName,
                        $param['name'],
                        $typeOut,
                    ),
                    $stackPtr,
                    'InvalidParamType',
                );
            }
        }
    }

    /**
     * Extract a compact return-type string (e.g., "int", "?Foo\Bar", "A|B&C", "static").
     */
    private function extractReturnType(File $phpcsFile, int $funcPtr): ?string
    {
        $tokens = $phpcsFile->getTokens();
        $closeParen = $tokens[$funcPtr]['parenthesis_closer'] ?? null;
        if ($closeParen === null) {
            return null;
        }

        // Look for ':' after the parameter list and before the body/semicolon.
        $colonPtr = $phpcsFile->findNext([T_COLON], $closeParen + 1, null, false, null, true);
        if ($colonPtr === false) {
            return null;
        }

        // Determine end boundary (before '{' or ';')
        $endBoundary = $phpcsFile->findNext([T_OPEN_CURLY_BRACKET, T_SEMICOLON], $colonPtr + 1);
        if ($endBoundary === false) {
            $endBoundary = $colonPtr + 1;
        }

        $type = $this->collectTypeTokens($tokens, $colonPtr + 1, $endBoundary - 1);
        return $type !== '' ? $type : null;
    }

    /**
     * Extract parameters with names and types.
     *
     * @return array<int, array{name: string, type: ?string}>
     */
    private function extractParameters(File $phpcsFile, int $funcPtr): array
    {
        $tokens = $phpcsFile->getTokens();
        $open  = $tokens[$funcPtr]['parenthesis_opener'] ?? null;
        $close = $tokens[$funcPtr]['parenthesis_closer'] ?? null;
        if ($open === null || $close === null) {
            return [];
        }

        $params = [];
        $ptr = $open + 1;

        while ($ptr < $close) {
            // Find next variable token; that marks a parameter.
            $varPtr = $phpcsFile->findNext(T_VARIABLE, $ptr, $close);
            if ($varPtr === false) {
                break;
            }

            $name = $tokens[$varPtr]['content']; // includes $

            // Scan backward from the variable to the previous comma or '('
            $segmentStart = $this->findParamSegmentStart($phpcsFile, $open, $varPtr);

            // Type is the part before the variable, skipping by-ref (&) and variadic (...) markers.
            $type = $this->collectTypeBeforeVariable($tokens, $segmentStart, $varPtr - 1);

            $params[] = [
                'name' => $name,
                'type' => ($type !== '' ? $type : null),
            ];

            // Advance to the next comma or the end of the parameter list.
            $nextComma = $phpcsFile->findNext([T_COMMA], $varPtr + 1, $close);
            $ptr = $nextComma === false ? $close : $nextComma + 1;
        }

        return $params;
    }

    /**
     * Find the start index of the current parameter segment (just after the previous comma, or the '(').
     */
    private function findParamSegmentStart(File $phpcsFile, int $openParen, int $varPtr): int
    {
        $prevComma = $phpcsFile->findPrevious([T_COMMA], $varPtr - 1, $openParen + 1);
        return $prevComma === false ? ($openParen + 1) : ($prevComma + 1);
    }

    /**
     * Build a compact type string from a token range, removing whitespace/comments and preserving:
     * - nullability '?'
     * - union/intersection '|' '&'
     * - qualified names '\'
     * - keywords: array, callable, self, parent, static
     * - generics-like text and array suffixes '[]' (as raw token contents if present)
     */
    private function collectTypeTokens(array $tokens, int $start, int $end): string
    {
        if ($start > $end) {
            return '';
        }

        $typeParts = [];
        for ($i = $start; $i <= $end; $i++) {
            $code = $tokens[$i]['code'];

            // Skip whitespace/comments
            if (isset(Tokens::$emptyTokens[$code]) || $code === T_COMMENT || $code === T_DOC_COMMENT) {
                continue;
            }

            // Stop if we accidentally ran into the function body opener/semicolon (safety)
            if ($code === T_OPEN_CURLY_BRACKET || $code === T_SEMICOLON) {
                break;
            }

            // Keep raw content for anything that could be part of the type declaration.
            $typeParts[] = $tokens[$i]['content'];
        }

        // Join and normalize multi-space around union/intersection (optional)
        $raw = trim(implode('', $typeParts));

        // Compact redundant spaces around pipes & ampersands (defensive)
        $raw = preg_replace('/\s*([|&])\s*/', '$1', $raw) ?? $raw;

        return $raw;
    }

    /**
     * From the start of this parameter segment to just before the variable, collect a type string.
     * Ignores by-ref '&' and variadic '...'.
     */
    private function collectTypeBeforeVariable(array $tokens, int $start, int $end): string
    {
        if ($start > $end) {
            return '';
        }

        $parts = [];
        for ($i = $start; $i <= $end; $i++) {
            $code = $tokens[$i]['code'];

            // skip whitespace/comments
            if (isset(Tokens::$emptyTokens[$code]) || $code === T_COMMENT || $code === T_DOC_COMMENT) {
                continue;
            }

            // skip by-ref and variadic markers that are not part of the type itself
            if ($code === T_BITWISE_AND || $code === T_ELLIPSIS) {
                continue;
            }

            // Everything else contributes to the type (including '?', '\', identifiers, union/intersection)
            $parts[] = $tokens[$i]['content'];
        }

        $raw = trim(implode('', $parts));
        $raw = preg_replace('/\s*([|&])\s*/', '$1', $raw) ?? $raw;

        return $raw;
    }

    /**
     * Resolve (possibly union/intersection/nullable) type string to FQCNs where applicable.
     * Examples:
     *   MpCode            -> \Vendor\Pkg\MpCode
     *   Foo\Bar           -> \Vendor\Pkg\Foo\Bar           (if relative to current namespace)
     *   ?PlatformUserId   -> ?\Vendor\Pkg\PlatformUserId
     *   A|B&null          -> \Ns\A|\Ns\B&null
     * Leaves primitives/self/parent/static untouched.
     */
    private function resolveTypeToFqcn(File $phpcsFile, string $type): string
    {
        // Split by union/intersection but keep the delimiters.
        $parts = preg_split('/([|&])/', $type, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) {
            return $type;
        }

        $ns      = $this->getNamespace($phpcsFile);
        $useMap  = $this->getUseMap($phpcsFile);

        foreach ($parts as &$p) {
            // Skip the delimiters '|' and '&'
            if ($p === '|' || $p === '&') {
                continue;
            }

            // Preserve leading '?' (nullable)
            $nullable = '';
            if (isset($p[0]) && $p[0] === '?') {
                $nullable = '?';
                $p = substr($p, 1);
            }

            // Nothing to do if empty after trimming nullable
            if ($p === '') {
                $p = $nullable . $p;
                continue;
            }

            // Strip array suffixes like Foo[] (preserve to append later)
            $arraySuffix = '';
            if (preg_match('/(\[\])+$/', $p, $m) === 1) {
                $arraySuffix = $m[0];
                $p = substr($p, 0, -strlen($arraySuffix));
            }

            // Primitives / keywords we do not FQCN-ize
            $lower = strtolower($p);
            if (in_array($lower, [
                'int','float','string','bool','array','callable','iterable','object',
                'mixed','never','null','false','true','void','self','parent','static',
            ], true)) {
                $p = $nullable . $p . $arraySuffix;
                continue;
            }

            // Already fully-qualified
            if ($p[0] === '\\') {
                $p = $nullable . $p . $arraySuffix;
                continue;
            }

            // Resolve aliases via use map
            $resolved = $this->resolveViaUseMap($p, $useMap);
            if ($resolved === null) {
                // Not imported: relative to current namespace
                $resolved = ($ns !== '')
                    ? '\\' . $ns . '\\' . $p
                    : '\\' . $p;
            }

            $p = $nullable . $resolved . $arraySuffix;
        }
        unset($p);

        return implode('', $parts);
    }

    /**
     * Try to resolve a name (which may have namespace separators) against the use map.
     * Returns FQCN beginning with "\" or null if no match.
     *
     * @param array<string,string> $useMap alias => \FQCN
     */
    private function resolveViaUseMap(string $name, array $useMap): ?string
    {
        // Take the first segment to check alias; keep the remainder to append.
        $first = $name;
        $rest  = '';
        $pos   = strpos($name, '\\');
        if ($pos !== false) {
            $first = substr($name, 0, $pos);
            $rest  = substr($name, $pos); // includes the leading backslash
        }

        if (isset($useMap[$first])) {
            return $useMap[$first] . $rest;
        }

        return null;
    }

    /**
     * Get current file namespace as string without leading "\" ("" if global namespace).
     */
    private function getNamespace(File $phpcsFile): string
    {
        $tokens = $phpcsFile->getTokens();
        $ptr    = $phpcsFile->findNext(T_NAMESPACE, 0);
        if ($ptr === false) {
            return '';
        }

        $end = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $ptr + 1);
        if ($end === false) {
            return '';
        }

        $parts = [];
        for ($i = $ptr + 1; $i < $end; $i++) {
            $code = $tokens[$i]['code'];
            if ($code === T_STRING || $code === T_NS_SEPARATOR) {
                $parts[] = $tokens[$i]['content'];
            }
        }

        $ns = trim(implode('', $parts), "\\ \t\n\r\0\x0B");
        return $ns;
    }

    /**
    * Build a map of "alias" => "\Fully\Qualified\Name" for top-level use statements.
     * Handles both simple and grouped use statements; ignores function/const imports.
     *
     * @return array<string,string>
     */
    private function getUseMap(File $phpcsFile): array
    {
        $tokens = $phpcsFile->getTokens();
        $useMap = [];

        $ptr = 0;
        while (($ptr = $phpcsFile->findNext(T_USE, $ptr)) !== false) {
            // Only consider top-level "use" (no conditions)
            $conditions = $tokens[$ptr]['conditions'] ?? [];
            if ($conditions !== []) {
                $ptr++;
                continue;
            }

            $end = $phpcsFile->findNext(T_SEMICOLON, $ptr + 1);
            if ($end === false) {
                break;
            }

            // Reconstruct the text of the use clause
            $text = '';
            for ($i = $ptr + 1; $i < $end; $i++) {
                $text .= $tokens[$i]['content'];
            }
            $text = trim($text);

            // Ignore function/const imports
            if (preg_match('/^(function|const)\b/i', $text) === 1) {
                $ptr = $end + 1;
               continue;
            }

            // Grouped use: Foo\Bar\{Baz,Qux as Alias}
            if (preg_match('/^([^{}]+)\{(.+)\}$/', $text, $m) === 1) {
                $prefix = trim($m[1], " \\");
                $inner  = $m[2];
                foreach (preg_split('/\s*,\s*/', $inner) as $part) {
                    if ($part === '') {
                        continue;
                    }
                    if (stripos($part, ' as ') !== false) {
                        [$path, $alias] = array_map('trim', preg_split('/\s+as\s+/i', $part));
                    } else {
                        $path  = trim($part);
                       $alias = ($pos = strrpos($path, '\\')) !== false ? substr($path, $pos + 1) : $path;
                    }
                    $fq = '\\' . $prefix . '\\' . ltrim($path, '\\');
                    $useMap[$alias] = $fq;
                }
            } else {
                // Simple use: Foo\Bar\Baz or Foo\Bar\Baz as Qux
                if (stripos($text, ' as ') !== false) {
                    [$path, $alias] = array_map('trim', preg_split('/\s+as\s+/i', $text));
                } else {
                    $path  = trim($text);
                    $alias = ($pos = strrpos($path, '\\')) !== false ? substr($path, $pos + 1) : $path;
                }
                $fq = '\\' . ltrim($path, '\\');
                $useMap[$alias] = $fq;
            }

            $ptr = $end + 1;
        }

        return $useMap;
    }

    private function isAcceptableType(string $type, bool $allowBool = false): bool
    {
        $type = ltrim($type, '\\');

        $reflection    = class_exists($type) ? new ReflectionClass($type) : null;
        $isUbixDataType = $reflection === null ? false : $reflection->isSubclassOf(DataType::class);
        $isUbixDto      = $reflection === null ? false : $reflection->isSubclassOf(Dto::class);
        $isUbixModel    = $reflection === null ? false : $reflection->isSubclassOf(Model::class);

        return $isUbixDataType
            || $isUbixDto
            || $isUbixModel
            || enum_exists($type)
            || $type === 'array'
            || $type === 'void'
            || $type === 'mixed' // Usually we don't want mixed but that will flag in a separate rule so if that one has been silenced we don't want this one to complain
            || ($allowBool && $type === 'bool');
    }
}
