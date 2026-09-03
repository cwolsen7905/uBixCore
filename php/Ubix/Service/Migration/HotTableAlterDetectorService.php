<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;

/**
 * Scans a SQL migration body for DDL that targets a PRE-EXISTING table —
 * `ALTER TABLE` / `CREATE INDEX … ON` statements whose target is not
 * created by a `CREATE TABLE` in the same file — per
 * `docs/standards/migrations.md` §11.9.
 *
 * Rationale: inline DDL against a large, live table replays single-threaded
 * on every replica and stalls replication behind it (the 2026-07-29
 * BILLING incident — an index/column add on multi-million-row transaction
 * tables). Such statements must either carry a `RequiresDBA:` header
 * (routed out-of-band, applied via pt-online-schema-change) or an
 * `AlterAck:` header (the author explicitly vouches the table is small
 * enough for inline DDL). The parser enforces this for migrations newer
 * than the §11.9 cutoff.
 *
 * Comment-aware — `--` line comments and block comments are stripped
 * (replaced with spaces so line numbers survive) before matching, same as
 * {@see DestructiveStatementDetectorService}.
 *
 * @see \Ubix\Tests\Service\Migration\HotTableAlterDetectorServiceTest PHPUnit test case
 */
final class HotTableAlterDetectorService
{
    /**
     * Regexes whose first capture group is the (possibly backtick-quoted,
     * possibly schema-qualified) target table reference. The full MariaDB
     * grammar is covered so no valid spelling bypasses the guard:
     * `ALTER [ONLINE] [IGNORE] TABLE [IF EXISTS]` and
     * `CREATE [OR REPLACE] [UNIQUE|FULLTEXT|SPATIAL] INDEX [IF NOT EXISTS]
     * <name> [USING <type>] ON`.
     */
    private const array TARGET_PATTERNS = [
        'ALTER TABLE'  => '/\bALTER\s+(?:ONLINE\s+|IGNORE\s+)*TABLE\s+(?:IF\s+EXISTS\s+)?(`?[A-Za-z0-9_]+`?(?:\.`?[A-Za-z0-9_]+`?)?)/i',
        'CREATE INDEX' => '/\bCREATE\s+(?:OR\s+REPLACE\s+)?(?:UNIQUE\s+|FULLTEXT\s+|SPATIAL\s+)?INDEX\s+(?:IF\s+NOT\s+EXISTS\s+)?\S+\s+(?:USING\s+[A-Za-z]+\s+)?ON\s+(`?[A-Za-z0-9_]+`?(?:\.`?[A-Za-z0-9_]+`?)?)/i',
    ];

    private const string CREATED_PATTERN = '/\bCREATE\s+(?:OR\s+REPLACE\s+)?(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(`?[A-Za-z0-9_]+`?(?:\.`?[A-Za-z0-9_]+`?)?)/i';

    /**
     * Constructor
     *
     * @param Logger $logger PSR-3 logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Scan a migration body for DDL targeting tables not created in-file.
     *
     * @param string $body SQL body — typically `MigrationFile::$body`
     *
     * @return string[] Human-readable offender descriptions, e.g. `BILLING.Transaction_Stops (ALTER TABLE on line 22)`; empty when clean
     */
    public function detect(string $body): array
    {
        $sanitisedBody = $this->stripComments($body);

        $createdTables = [];
        if (preg_match_all(self::CREATED_PATTERN, $sanitisedBody, $created) > 0) {
            foreach ($created[1] as $name) {
                $createdTables[$this->normalise($name)] = true;
            }
        }

        $offenders = [];

        foreach (self::TARGET_PATTERNS as $label => $pattern) {
            if (preg_match_all($pattern, $sanitisedBody, $matches, PREG_OFFSET_CAPTURE) === 0) {
                continue;
            }

            foreach ($matches[1] as $match) {
                $table = $this->normalise($match[0]);
                if (isset($createdTables[$table])) {
                    continue;
                }

                $lineNumber  = substr_count($sanitisedBody, "\n", 0, $match[1]) + 1;
                $offenders[] = sprintf('%s (%s on line %d)', str_replace('`', '', $match[0]), $label, $lineNumber);
            }
        }

        return $offenders;
    }

    /**
     * Reduce a table reference to its bare, lower-case table name —
     * backticks stripped, schema qualifier dropped — so `BILLING`.`X`,
     * BILLING.X, and x all compare equal. Dropping the qualifier is safe
     * because a migration file targets exactly one database (the
     * `Database:` header), so a bare CREATE and a qualified ALTER of the
     * same name can only refer to the same table.
     *
     * @param string $name Raw captured table reference
     *
     * @return string The normalised name
     */
    private function normalise(string $name): string
    {
        $bare        = str_replace('`', '', $name);
        $dotPosition = strrpos($bare, '.');
        if ($dotPosition !== false) {
            $bare = substr($bare, $dotPosition + 1);
        }

        return strtolower($bare);
    }

    /**
     * Replace `--` line comments and block comments with spaces, preserving
     * newlines so line numbers stay accurate.
     *
     * @param string $body Raw SQL body
     *
     * @return string The comment-stripped body
     */
    private function stripComments(string $body): string
    {
        $withoutBlocks = preg_replace_callback(
            '#/\*[\s\S]*?\*/#',
            static function (array $m): string {
                return preg_replace('/[^\n]/', ' ', $m[0]) ?? '';
            },
            $body,
        ) ?? $body;

        return preg_replace_callback(
            '/--[^\n]*/',
            static function (array $m): string {
                return str_repeat(' ', strlen($m[0]));
            },
            $withoutBlocks,
        ) ?? $withoutBlocks;
    }
}
