<?php

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use Ubix\DataType\AbstractDataType as DataType;
use Ubix\DataType\DateTime\UbixDateTime as UbixDateTime;
use Ubix\Model\AbstractModel as Model;

class ModelPropertiesTypeSniff implements Sniff
{
    public function register(): array
    {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $scopeOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $scopeCloser = $tokens[$stackPtr]['scope_closer'] ?? null;
        if ($scopeOpener === null || $scopeCloser === null) {
            return;
        }

        // collect namespace + use statements once per file
        $namespace   = $this->getNamespace($phpcsFile);
        $useMap      = $this->getUseStatements($phpcsFile);

        $constructorPtr = $phpcsFile->findNext(T_FUNCTION, $scopeOpener + 1, $scopeCloser);
        while ($constructorPtr !== false) {
            $namePtr = $phpcsFile->findNext(T_STRING, $constructorPtr, $scopeCloser);
            if ($namePtr !== false && strtolower($tokens[$namePtr]['content']) === '__construct') {
                $this->processConstructor($phpcsFile, $constructorPtr, $namespace, $useMap);
                break;
            }

            $constructorPtr = $phpcsFile->findNext(T_FUNCTION, $constructorPtr + 1, $scopeCloser);
        }
    }

    private function processConstructor(File $phpcsFile, int $functionPtr, ?string $namespace, array $useMap): void
    {
        $tokens     = $phpcsFile->getTokens();
        $openParen  = $tokens[$functionPtr]['parenthesis_opener'] ?? null;
        $closeParen = $tokens[$functionPtr]['parenthesis_closer'] ?? null;
        if ($openParen === null || $closeParen === null) {
            return;
        }

        $current = $openParen + 1;
        while ($current < $closeParen) {
            if ($tokens[$current]['code'] === T_VARIABLE) {
                $this->processParam($phpcsFile, $current, $openParen, $closeParen, $namespace, $useMap);
            }
            $current++;
        }
    }

    private function processParam(
        File $phpcsFile,
        int $paramVarPtr,
        int $openParen,
        int $closeParen,
        ?string $namespace,
        array $useMap
    ): void {
        $tokens = $phpcsFile->getTokens();

        $visibilityPtr = $phpcsFile->findPrevious(
            [
                T_PUBLIC,
                T_PROTECTED,
                T_PRIVATE,
                T_READONLY,
                T_STATIC,
                T_WHITESPACE,
                T_NULLABLE,
                T_STRING,
                T_NS_SEPARATOR,
                T_NAME_QUALIFIED,
                T_NAME_FULLY_QUALIFIED,
                T_NAME_RELATIVE,
                T_BITWISE_AND,
            ],
            $paramVarPtr - 1,
            $openParen + 1,
            false,
            null,
            true
        );

        if ($visibilityPtr === false) {
            return;
        }

        $maybeVisibilityPtr = $phpcsFile->findPrevious(
            [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_READONLY],
            $paramVarPtr - 1,
            $openParen + 1
        );

        if ($maybeVisibilityPtr === false) {
            return;
        }

        $propertyName = ltrim($tokens[$paramVarPtr]['content'], '$');

        $rawType = $this->getParamType($phpcsFile, $maybeVisibilityPtr, $paramVarPtr);
        $resolved = $this->resolveType($rawType, $namespace, $useMap);

        // For our domain: we already enforce nullable on these,
        // so we don't want to see a leading "?" in the debug output.
        $displayType = ltrim($resolved, '?') ?: 'untyped';

        //
        //  VSM business rules
        //
        $reflection    = class_exists($displayType) ? new ReflectionClass($displayType) : null;
        $isUbixDataType = $reflection === null ? false : $reflection->isSubclassOf(DataType::class);
        $isUbixDateTime = $reflection === null ? false : ltrim($displayType, '\\') === UbixDateTime::class || $reflection->isSubclassOf(UbixDateTime::class);
        $isUbixModel    = $reflection === null ? false : $reflection->isSubclassOf(Model::class);

        if (
            $displayType !== 'array'
            && !enum_exists($displayType)
            && !$isUbixDataType
            && !$isUbixModel
        ) {
            $phpcsFile->addError(
                'The $' . $propertyName . ' property is of type `' . $displayType . '` but must be an array, enum or a type that extends `' . DataType::class . '` or `' . Model::class . '`',
                $paramVarPtr,
                'InvalidType'
            );
        }

        if ($isUbixDateTime && !str_starts_with($propertyName, 'date')) {
            $phpcsFile->addError(
                'The $' . $propertyName . ' property type is or extends the `' . UbixDateTime::class . '` data type but does not start with `date`',
                $paramVarPtr,
                'InvalidDateTimePropertyName'
            );
        }
    }

    private function getParamType(File $phpcsFile, int $startPtr, int $endPtr): string
    {
        $tokens = $phpcsFile->getTokens();
        $parts  = [];

        for ($i = $startPtr + 1; $i < $endPtr; $i++) {
            $code = $tokens[$i]['code'];

            if ($code === T_WHITESPACE) {
                continue;
            }

            if (in_array($code, [
                T_STRING,
                T_NULLABLE,
                T_NS_SEPARATOR,
                T_NAME_QUALIFIED,
                T_NAME_FULLY_QUALIFIED,
                T_NAME_RELATIVE,
                T_ARRAY_HINT,
                T_CALLABLE,
                defined('T_PIPE') ? T_PIPE : null,
            ], true)) {
                $parts[] = $tokens[$i]['content'];
                continue;
            }

            if (in_array($code, [T_BITWISE_AND, T_ELLIPSIS], true)) {
                continue;
            }
        }

        return implode('', $parts);
    }

    /**
     * Turn a raw type token string into either:
     *   - "scalar int"
     *   - "\Full\Qualified\Class"
     *   - "?\Full\Qualified\Class"
     */
    private function resolveType(?string $rawType, ?string $namespace, array $useMap): string
    {
        if ($rawType === null || $rawType === '') {
            return '';
        }

        // Handle union very simply (first type only) – can be improved later
        if (strpos($rawType, '|') !== false) {
            $first = explode('|', $rawType, 2)[0];
            return $this->resolveType($first, $namespace, $useMap);
        }

        $isNullable = false;
        if ($rawType[0] === '?') {
            $isNullable = true;
            $rawType = substr($rawType, 1);
        }

        $lower = strtolower($rawType);
        $scalarTypes = [
            'int',
            'float',
            'string',
            'bool',
            'array',
            'callable',
            'iterable',
            'mixed',
            'object',
            'false',
            'true',
            'null',
        ];

        if (in_array($lower, $scalarTypes, true)) {
            return ($isNullable ? '?' : '') . $lower;
        }

        // fully qualified already
        if ($rawType[0] === '\\') {
            return ($isNullable ? '?' : '') . $rawType;
        }

        // imported via use?
        if (isset($useMap[$rawType])) {
            return ($isNullable ? '?' : '') . $useMap[$rawType];
        }

        // no namespace? then it's just the short name
        if ($namespace === null || $namespace === '') {
            return ($isNullable ? '?' : '') . $rawType;
        }

        // default: current namespace + short name
        return ($isNullable ? '?' : '') . '\\' . $namespace . '\\' . $rawType;
    }

    private function getNamespace(File $phpcsFile): ?string
    {
        $tokens = $phpcsFile->getTokens();
        $nsPtr  = $phpcsFile->findNext(T_NAMESPACE, 0);
        if ($nsPtr === false) {
            return null;
        }

        $nsEnd = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $nsPtr + 1);
        $name  = '';
        for ($i = $nsPtr + 1; $i < $nsEnd; $i++) {
            if (in_array($tokens[$i]['code'], [T_STRING, T_NS_SEPARATOR], true)) {
                $name .= $tokens[$i]['content'];
            }
        }

        return $name !== '' ? $name : null;
    }

    private function getUseStatements(File $phpcsFile): array
    {
        $tokens = $phpcsFile->getTokens();
        $useMap = [];

        $usePtr = $phpcsFile->findNext(T_USE, 0);
        while ($usePtr !== false) {
            // Skip trait use inside class
            $prev = $phpcsFile->findPrevious([T_CLASS, T_TRAIT, T_INTERFACE], $usePtr - 1, null, false, null, true);
            if ($prev !== false && $tokens[$prev]['line'] === $tokens[$usePtr]['line']) {
                $usePtr = $phpcsFile->findNext(T_USE, $usePtr + 1);
                continue;
            }

            $semiColon = $phpcsFile->findNext(T_SEMICOLON, $usePtr + 1);
            if ($semiColon === false) {
                break;
            }

            $full = '';
            $alias = null;
            $lastString = null;

            for ($i = $usePtr + 1; $i < $semiColon; $i++) {
                if (in_array($tokens[$i]['code'], [T_STRING, T_NS_SEPARATOR], true)) {
                    $full .= $tokens[$i]['content'];
                    $lastString = $tokens[$i]['content'];
                } elseif ($tokens[$i]['code'] === T_AS) {
                    // explicit alias
                    $aliasPtr = $phpcsFile->findNext(T_STRING, $i + 1, $semiColon);
                    if ($aliasPtr !== false) {
                        $alias = $tokens[$aliasPtr]['content'];
                    }
                    break;
                }
            }

            if ($full !== '') {
                $shortName = $alias ?: $lastString;
                if ($shortName !== null) {
                    $useMap[$shortName] = '\\' . ltrim($full, '\\');
                }
            }

            $usePtr = $phpcsFile->findNext(T_USE, $semiColon + 1);
        }

        return $useMap;
    }
}
