<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\DestructiveStatementMatch;
use Ubix\Enum\Migration\DestructiveStatementKind;

/**
 * Scans a SQL migration body for destructive statements per
 * `docs/standards/migrations.md` §11.1. Comment-aware — both
 * `--` line comments and `/* ... *\/` block comments are stripped
 * before regex matching so a column comment containing the literal
 * word `DROP TABLE` doesn't trigger a false positive. Comment
 * stripping replaces the comment bytes with spaces so line numbers
 * survive intact.
 *
 * Detected patterns:
 * - DROP TABLE / DROP DATABASE / DROP VIEW / DROP INDEX
 * - TRUNCATE TABLE
 * - RENAME TABLE
 * - ALTER TABLE … DROP COLUMN
 * - ALTER TABLE … MODIFY (covers MODIFY COLUMN; type changes can lose data)
 * - DELETE FROM without a WHERE clause in the same statement
 *
 * @see \Ubix\Tests\Service\Migration\DestructiveStatementDetectorServiceTest PHPUnit test case
 */
final class DestructiveStatementDetectorService
{
    /**
     * Map of regex (case-insensitive) → statement kind. Order is
     * irrelevant; matches accumulate. `ALTER TABLE … DROP COLUMN`
     * and `ALTER TABLE … MODIFY` are deliberately checked
     * independently because a single statement can hit both.
     */
    private const array PATTERNS = [
        '/\bALTER\s+TABLE\b[\s\S]*?\bDROP\s+COLUMN\b/i' => DestructiveStatementKind::ALTER_TABLE_DROP_COLUMN,
        '/\bALTER\s+TABLE\b[\s\S]*?\bMODIFY\b/i'        => DestructiveStatementKind::ALTER_TABLE_MODIFY,
        '/\bDROP\s+DATABASE\b/i'                        => DestructiveStatementKind::DROP_DATABASE,
        '/\bDROP\s+INDEX\b/i'                           => DestructiveStatementKind::DROP_INDEX,
        '/\bDROP\s+TABLE\b/i'                           => DestructiveStatementKind::DROP_TABLE,
        '/\bDROP\s+VIEW\b/i'                            => DestructiveStatementKind::DROP_VIEW,
        '/\bRENAME\s+TABLE\b/i'                         => DestructiveStatementKind::RENAME_TABLE,
        '/\bTRUNCATE\s+TABLE\b/i'                       => DestructiveStatementKind::TRUNCATE_TABLE,
    ];

    /**
     * Constructor
     *
     * @param Logger $logger PSR-3 logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Scan a migration body for destructive statements.
     *
     * @param string $body SQL body — typically `MigrationFile::$body`
     *
     * @return DestructiveStatementMatch[]
     */
    public function detect(string $body): array
    {
        $sanitisedBody = $this->stripComments($body);

        $matches = [];

        foreach (self::PATTERNS as $pattern => $kind) {
            if (preg_match_all($pattern, $sanitisedBody, $hits, PREG_OFFSET_CAPTURE) === false) {
                continue;
            }
            foreach ($hits[0] as $hit) {
                $matches[] = new DestructiveStatementMatch(
                    kind:        $kind,
                    lineNumber:  $this->lineNumberAtOffset($sanitisedBody, $hit[1]),
                    matchedText: $hit[0],
                );
            }
        }

        // DELETE FROM without WHERE — handled separately because the
        // pattern is "presence of one keyword + absence of another in
        // the same statement" rather than a simple regex match.
        foreach ($this->getDeleteFromWithoutWhereMatches($sanitisedBody) as $match) {
            $matches[] = $match;
        }

        return $matches;
    }

    /**
     * Replace SQL comments with spaces (preserving offsets and line
     * counts) so the destructive-statement regex doesn't trip on
     * commented-out SQL or column comments containing literal
     * destructive keywords.
     *
     * Handles `-- ...` line comments (to end-of-line) and `/* ... *\/`
     * block comments. String literal awareness is not implemented —
     * a destructive keyword inside a quoted string IS treated as a
     * match. Migration authors do not put DDL keywords in string
     * literals; the false-positive case is theoretical.
     *
     * @param string $body Raw migration body
     *
     * @return string Same length as `$body` with all comment bytes turned into spaces (newlines preserved)
     */
    private function stripComments(string $body): string
    {
        $sanitised = preg_replace_callback(
            '/\/\*[\s\S]*?\*\//',
            function (array $match): string {
                return $this->blankPreservingNewlines($match[0]);
            },
            $body,
        ) ?? $body;

        $sanitised = preg_replace_callback(
            '/--[^\r\n]*/',
            function (array $match): string {
                return str_repeat(' ', strlen($match[0]));
            },
            $sanitised,
        ) ?? $sanitised;

        return $sanitised;
    }

    /**
     * Replace every non-newline character in `$text` with a space
     * while keeping `\n` and `\r` intact. Used by the block-comment
     * stripper so line numbers survive the rewrite.
     *
     * @param string $text Source text
     *
     * @return string Rewritten text — same length, same line breaks
     */
    private function blankPreservingNewlines(string $text): string
    {
        return preg_replace('/[^\r\n]/', ' ', $text) ?? $text;
    }

    /**
     * Compute the 1-indexed line number for a byte offset in `$body`.
     *
     * @param string $body   Source text
     * @param int    $offset Zero-indexed byte offset
     *
     * @return int 1-indexed line number
     */
    private function lineNumberAtOffset(string $body, int $offset): int
    {
        if ($offset <= 0) {
            return 1;
        }
        $prefix = substr($body, 0, $offset);
        return substr_count($prefix, "\n") + 1;
    }

    /**
     * Locate every `DELETE FROM …` whose statement (up to the next
     * `;` or end of body) does NOT contain a `WHERE` clause.
     *
     * This is a pragmatic, comment-stripped, statement-naive scan —
     * it doesn't parse SQL. Migrations that author `DELETE FROM` with
     * a `WHERE` in a separate statement are flagged; that's the
     * conservative direction for a safety lint.
     *
     * @param string $sanitisedBody Comment-stripped SQL body
     *
     * @return DestructiveStatementMatch[]
     */
    private function getDeleteFromWithoutWhereMatches(string $sanitisedBody): array
    {
        if (preg_match_all('/\bDELETE\s+FROM\b/i', $sanitisedBody, $hits, PREG_OFFSET_CAPTURE) === false) {
            return [];
        }

        $matches = [];
        foreach ($hits[0] as $hit) {
            $statementStart = $hit[1];
            $semicolonPos   = strpos($sanitisedBody, ';', $statementStart);
            $statementEnd   = $semicolonPos === false ? strlen($sanitisedBody) : $semicolonPos;
            $statement      = substr($sanitisedBody, $statementStart, $statementEnd - $statementStart);

            if (preg_match('/\bWHERE\b/i', $statement) === 1) {
                continue;
            }

            $matches[] = new DestructiveStatementMatch(
                kind:        DestructiveStatementKind::DELETE_FROM_NO_WHERE,
                lineNumber:  $this->lineNumberAtOffset($sanitisedBody, $statementStart),
                matchedText: $hit[0],
            );
        }

        return $matches;
    }
}
