<?php
declare(strict_types=1);

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class RouteSortingSniff implements Sniff
{
    /**
     * We only need to fire once per file to collect everything.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    /**
     * Scan the whole file on the very first token (T_OPEN_TAG at position 0),
     * find all single-line ->map() calls, parse their args, and track max lengths.
     *
     * @param File $phpcsFile
     * @param int  $stackPtr  Position of the T_OPEN_TAG token.
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        // only run once, on the very first token
        if ($stackPtr !== 0) {
            return;
        }

        $tokens     = $phpcsFile->getTokens();
        $tokenCount = count($tokens);
        $mapCount   = 0;
        $maxLengths = [];
        $maxValues  = [];
        $offsets    = [];
        $mapPtrs    = [];
        // load file lines so we can measure character offsets
        $fileLines = @file($phpcsFile->getFilename()) ?: [];

        for ($i = 0; $i < $tokenCount; $i++) {
            // look for ->map
            if ($tokens[$i]['code'] === T_OBJECT_OPERATOR
                && isset($tokens[$i + 1])
                && $tokens[$i + 1]['code'] === T_STRING
                && $tokens[$i + 1]['content'] === 'map'
            ) {
                // find the opening parenthesis
                $openParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $i + 2);
                if ($openParen === false) {
                    continue;
                }
                $closeParen = $tokens[$openParen]['parenthesis_closer'] ?? null;
                if ($closeParen === null) {
                    continue;
                }

                // ensure both parens are on same line as the '->map'
                $line = $tokens[$tokens[$i]['stackPtr'] ?? $i]['line'];
                if ($tokens[$openParen]['line'] !== $line
                    || $tokens[$closeParen]['line'] !== $line
                ) {
                    continue;
                }

                // count this single-line map() call
                $mapCount++;
                $mapPtrs[$mapCount] = $i;

                // parse each parameter's raw text and length
                $params = $this->getParameterData($tokens, $openParen + 1, $closeParen);
                foreach ($params as $idx => $data) {
                    $len   = $data['length'];
                    $value = $data['value'];
                    if (!isset($maxLengths[$idx]) || $len > $maxLengths[$idx]) {
                        $maxLengths[$idx] = $len;
                        $maxValues[$idx]  = $value;
                    }
                }
                // now compute each parameter's start offset (tabs = 4 spaces)
                $lineNum = $tokens[$i]['line'];
                $rawLine = $fileLines[$lineNum - 1] ?? '';
                $expandedLine = str_replace("\t", "    ", rtrim($rawLine, "\r\n"));
                $callOffsets = [];
                foreach ($params as $data) {
                    $pos = strpos($expandedLine, $data['value']);
                    $callOffsets[] = ($pos !== false ? $pos + 1 : 0);
                }
                $offsets[$mapCount] = $callOffsets;
            }
        }

        // report how many single-line map() calls we saw
        /*
        $phpcsFile->addWarning(
            sprintf('Checked %d single-line map() calls', $mapCount),
            $stackPtr,
            'MapCallCountDebug'
        );
        */


        // emit one warning per parameter index
        foreach ($maxLengths as $len) {
            /*
            $phpcsFile->addWarning(
                sprintf(
                    'map() parameter %d max length = %d chars; value: %s',
                    $idx + 1,
                    $len,
                    $maxValues[$idx]
                ),
                $stackPtr,
                'MapParamLengthDebug'
            );
            */
        }
        // finally, emit per-call parameter offsets
        foreach ($offsets as $callNo => $offs) {
            /*
            $phpcsFile->addWarning(
                sprintf(
                    'map() call %d parameter start offsets: %s',
                    $callNo,
                    implode(', ', $offs)
                ),
                $stackPtr,
                'MapParamOffsetDebug'
            );
            */
        }

        // compute the “correct” offsets:
        $firstCols  = array_column($offsets, 0);
        $expected1  = min($firstCols);
        $expected2  = $expected1 + ($maxLengths[0] ?? 0) + 2;
        $expected3  = $expected2 + ($maxLengths[1] ?? 0) + 2;

        foreach ($offsets as $callNo => $offs) {
            // pick the original pointer for this call (fallback to file-start)
            $ptr = $mapPtrs[$callNo] ?? $stackPtr;

            // parameter 1
            if (($offs[0] ?? 0) !== $expected1) {
                $phpcsFile->addError(
                    sprintf(
                        'map() parameter 1 starts at column %d but should be %d', // 'map() call %d parameter 1 starts at column %d but should be %d',
                        // $callNo,
                        $offs[0] ?? 0,
                        $expected1
                    ),
                    $ptr,
                    'MapParamOffsetError1'
                );
            }

            // parameter 2
            if (($offs[1] ?? 0) !== $expected2) {
                $phpcsFile->addError(
                    sprintf(
                        'map() parameter 2 starts at column %d but should be %d', // 'map() call %d parameter 2 starts at column %d but should be %d',
                        // $callNo,
                        $offs[1] ?? 0,
                        $expected2
                    ),
                    $ptr,
                    'MapParamOffsetError2'
                );
            }

            // parameter 3
            if (($offs[2] ?? 0) !== $expected3) {
                $phpcsFile->addError(
                    sprintf(
                        'map() parameter 3 starts at column %d but should be %d', // 'map() call %d parameter 3 starts at column %d but should be %d',
                        // $callNo,
                        $offs[2] ?? 0,
                        $expected3
                    ),
                    $ptr,
                    'MapParamOffsetError3'
                );
            }
        }
    }

    /**
     * Extract each parameter’s raw string and its length.
     *
     * @param array<int,array> $tokens
     * @param int              $start  First token inside the parens
     * @param int              $end    The closing-paren token
     * @return array<int,array{value:string,length:int}>
     */
    private function getParameterData(array $tokens, int $start, int $end): array
    {
        $params     = [];
        $buffer     = '';
        $depth      = 0;
        $paramIndex = 0;

        for ($i = $start; $i < $end; $i++) {
            $t = $tokens[$i];

            // Track parens and [ ] by content so commas inside array literals don’t split
            if ($t['code'] === T_OPEN_PARENTHESIS || $t['content'] === '[') {
                $depth++;
            } elseif ($t['code'] === T_CLOSE_PARENTHESIS || $t['content'] === ']') {
                $depth--;
            }

            // split on commas only at paren-depth zero
            if ($t['code'] === T_COMMA && $depth === 0) {
                $value = trim($buffer);
                $params[$paramIndex++] = [
                    'value'  => $value,
                    'length' => strlen($value),
                ];
                $buffer = '';
            } else {
                $buffer .= $t['content'];
            }
        }

        // final param
        $value = trim($buffer);
        $params[$paramIndex] = [
            'value'  => $value,
            'length' => strlen($value),
        ];

        return $params;
    }
}
