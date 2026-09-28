<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;

/**
 * Reduce a `mariadb-dump` schema dump to comparable lines
 *
 * `SchemaDiffService` compares two dumps by line. The two dumps come from two
 * different servers — in replay mode the tier is dumped from the tier and the
 * expected schema from the TEST connection, deliberately, so a drift check can
 * read production without holding DDL on it. Two servers of different versions
 * render the *same* schema differently, so anything version-dependent has to be
 * canonicalised here or every comparison reports drift that is not there.
 *
 * What is dropped, and why each one is safe:
 *
 * - **Comments, conditional-compatibility banners, `DROP TABLE IF EXISTS`,
 *   `AUTO_INCREMENT=N`.** Dump chrome and a counter that moves as rows are
 *   inserted. None of it is schema state.
 * - **A column-level `CHARACTER SET`/`COLLATE` that merely repeats the table's
 *   own default.** Redundant by definition: the column already inherits it. One
 *   server prints it, another does not. A column whose collation genuinely
 *   *differs* from its table — `utf8mb4_bin` on a JSON column, say — keeps it
 *   and is still compared, which is the whole point of doing this per table
 *   rather than stripping collation everywhere.
 * - **`CHECK (json_valid(<the column itself>))`.** MariaDB generates this for a
 *   `JSON` column, because `JSON` is an alias for `longtext` plus that
 *   constraint. Some versions render it inline on the column, some as a named
 *   `CONSTRAINT` line, some not at all. Only the auto-generated shape is
 *   dropped — a `CHECK` a human wrote is left alone.
 * - **`ROW_FORMAT=DYNAMIC` on an InnoDB table.** DYNAMIC has been InnoDB's
 *   default since MariaDB 10.2, so stating it changes nothing; a table created
 *   under an older default records it and every later dump repeats it, so a
 *   long-lived tier disagrees with a fresh rebuild on every such table.
 *   COMPACT, COMPRESSED and REDUNDANT are meaningful and stay compared.
 *
 * Every remaining line is **prefixed with its table**. That is not decoration:
 * the diff is line-based, so without it a reported difference cannot be traced
 * to a table (a stray `creator_name varchar(255)` names no table), and worse,
 * identical lines in different tables cancel each other out in `array_diff` —
 * a column dropped from one table and present in another would read as clean.
 *
 * @see \Ubix\Tests\Service\Migration\SchemaDumpNormaliserServiceTest PHPUnit test case
 */
final class SchemaDumpNormaliserService
{
    /**
     * Constructor
     *
     * @param Logger $logger PSR-3 logger (Ubix standards-test requires every service to accept one)
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Normalise a dump into comparable, table-attributed lines
     *
     * @param string $dump The raw `mariadb-dump` output
     *
     * @return list<string> Comparable lines, each prefixed `<table>: ` where one applies
     */
    public function normalise(string $dump): array
    {
        $stripped = preg_replace('/\s+AUTO_INCREMENT=\d+/i', '', $dump) ?? $dump;
        $lines    = preg_split('/\r\n|\n|\r/', $stripped) ?: [];

        $clean = [];
        $table = '';
        $body  = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || $this->isIgnorableLine($trimmed)) {
                continue;
            }

            $opened = $this->tableBeingCreated($trimmed);
            if ($opened !== null) {
                // A dump that never closed the previous table (truncated capture)
                // must not silently lose its columns.
                $clean   = array_merge($clean, $this->attributeAll($table, $body));
                $table   = $opened;
                $body    = [];
                $clean[] = $this->attribute($table, $trimmed);
                continue;
            }

            if ($table !== '' && str_starts_with($trimmed, ')')) {
                // The closing line carries the table's own charset and collation,
                // which is what makes a column-level copy of them redundant, so
                // the body can only be canonicalised once it has been read.
                $canonical = $this->canonicaliseBody($body, $trimmed);
                $clean     = array_merge($clean, $this->attributeAll($table, $canonical));
                $clean[]   = $this->attribute($table, $this->canonicaliseClosing($trimmed));
                $table     = '';
                $body      = [];
                continue;
            }

            if ($table !== '') {
                $body[] = $trimmed;
                continue;
            }

            $clean[] = $trimmed;
        }

        return array_merge($clean, $this->attributeAll($table, $body));
    }

    /**
     * Drop the dump chrome that varies by dump source and dump flags
     *
     * @param string $trimmed One trimmed line
     *
     * @return bool True when the line carries no schema state
     */
    private function isIgnorableLine(string $trimmed): bool
    {
        // All `--` SQL comment lines — covers the mariadb-dump banner header
        // (`-- MySQL dump`, `-- Host:`, `-- Server version`, `-- Dump completed
        // on …`), per-table narration (`-- Table structure for table …`), and
        // any other commentary. Schema structure lives in CREATE / ALTER.
        if (str_starts_with($trimmed, '--')) {
            return true;
        }
        // MariaDB conditional-compatibility comment lines: `/*!N SET …*/;`
        // (MySQL-style) and `/*M! … */` (MariaDB-only banner like "enable the
        // sandbox mode"). Both vary by dump-source server version.
        if (str_starts_with($trimmed, '/*!') && str_ends_with($trimmed, '*/;')) {
            return true;
        }
        if (str_starts_with($trimmed, '/*M!') && (str_ends_with($trimmed, '*/') || str_ends_with($trimmed, '*/;'))) {
            return true;
        }
        // `DROP TABLE IF EXISTS` setup chrome — present when the dump is taken
        // without `--skip-add-drop-table` and absent when it is.
        return preg_match('/^DROP\s+TABLE\s+IF\s+EXISTS\b/i', $trimmed) === 1;
    }

    /**
     * The table name when this line opens a `CREATE TABLE`
     *
     * @param string $trimmed One trimmed line
     *
     * @return ?string The table name, or null when the line opens no table
     */
    private function tableBeingCreated(string $trimmed): ?string
    {
        $matches = [];
        if (preg_match('/^CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?([^\s(]+)/i', $trimmed, $matches) !== 1) {
            return null;
        }
        // A qualified name (`db`.`table`) attributes to the table, so the same
        // table compares equal whichever way the two dumps happen to qualify it.
        $name  = (string) $matches[1];
        $parts = explode('.', str_replace('`', '', $name));

        return (string) end($parts);
    }

    /**
     * Canonicalise a table body against that table's own defaults
     *
     * @param array<int, string> $body    The table's buffered lines
     * @param string             $closing The `) ENGINE=… DEFAULT CHARSET=… COLLATE=…;` line
     *
     * @return list<string> The body with redundant and auto-generated clauses removed
     */
    private function canonicaliseBody(array $body, string $closing): array
    {
        $charset   = $this->firstMatch('/DEFAULT\s+CHARSET=([A-Za-z0-9_]+)/i', $closing);
        $collation = $this->firstMatch('/\bCOLLATE=([A-Za-z0-9_]+)/i', $closing);

        $canonical = [];
        foreach ($body as $line) {
            if ($this->isGeneratedJsonConstraintLine($line)) {
                continue;
            }

            $column = $this->firstMatch('/^`([^`]+)`/', $line);
            if ($column !== '') {
                $line = $this->withoutGeneratedJsonCheck($line, $column);
            }
            if ($charset !== '') {
                $line = $this->withoutClause($line, 'CHARACTER SET', $charset);
            }
            if ($collation !== '') {
                $line = $this->withoutClause($line, 'COLLATE', $collation);
            }

            $canonical[] = $line;
        }

        return $canonical;
    }

    /**
     * Canonicalise a table's closing line
     *
     * Drops `ROW_FORMAT=DYNAMIC` on an InnoDB table. DYNAMIC has been InnoDB's
     * default row format since MariaDB 10.2 / MySQL 5.7, so stating it changes
     * nothing — but a table created under an older default, or restored from a
     * dump taken then, records it explicitly and every later dump repeats it.
     * A tier with a long history therefore disagrees with a freshly rebuilt
     * schema on every such table, for no difference in behaviour.
     *
     * Only DYNAMIC, and only on InnoDB. COMPACT, COMPRESSED and REDUNDANT say
     * something real about how rows are stored, and stay compared.
     *
     * The charset and collation on this line are left alone: a table-level
     * collation difference is real drift, and this is where it would show.
     *
     * @param string $closing The `) ENGINE=… ;` line
     *
     * @return string The closing line without a redundant row format
     */
    private function canonicaliseClosing(string $closing): string
    {
        if (stripos($closing, 'ENGINE=InnoDB') === false) {
            return $closing;
        }

        return (string) (preg_replace('/\s+ROW_FORMAT=DYNAMIC\b/i', '', $closing) ?? $closing);
    }

    /**
     * Whether the line is the named-constraint rendering of a `JSON` column's check
     *
     * @param string $line One table-body line
     *
     * @return bool True when the line is an auto-generated `json_valid` constraint
     */
    private function isGeneratedJsonConstraintLine(string $line): bool
    {
        return preg_match('/^CONSTRAINT\s+`?[^`\s]+`?\s+CHECK\s*\(\s*json_valid\(/i', $line) === 1;
    }

    /**
     * Remove the `CHECK (json_valid(<column>))` MariaDB adds for a `JSON` column
     *
     * Only when the checked column is the column being declared. A check naming
     * anything else was written by a person and is real schema.
     *
     * @param string $line   One table-body line
     * @param string $column The column this line declares
     *
     * @return string The line without its auto-generated check
     */
    private function withoutGeneratedJsonCheck(string $line, string $column): string
    {
        $pattern = sprintf('/\s*CHECK\s*\(\s*json_valid\(\s*`?%s`?\s*\)\s*\)/i', preg_quote($column, '/'));

        return (string) (preg_replace($pattern, '', $line) ?? $line);
    }

    /**
     * Remove a column-level clause that only repeats the table default
     *
     * @param string $line   One table-body line
     * @param string $clause Either `CHARACTER SET` or `COLLATE`
     * @param string $value  The table's own value for that clause
     *
     * @return string The line without the redundant clause
     */
    private function withoutClause(string $line, string $clause, string $value): string
    {
        $pattern = sprintf(
            '/\s+%s\s+%s\b/i',
            preg_quote($clause, '/'),
            preg_quote($value, '/'),
        );

        return (string) (preg_replace($pattern, '', $line) ?? $line);
    }

    /**
     * First capture group of a pattern, or an empty string
     *
     * @param string $pattern The pattern, with one capture group
     * @param string $subject The subject to match against
     *
     * @return string The captured value, or `''` when it does not match
     */
    private function firstMatch(string $pattern, string $subject): string
    {
        $matches = [];
        if (preg_match($pattern, $subject, $matches) !== 1) {
            return '';
        }

        return (string) $matches[1];
    }

    /**
     * Prefix a line with the table it belongs to
     *
     * @param string $table The table name, or `''` outside any table
     * @param string $line  The line
     *
     * @return string The attributed line
     */
    private function attribute(string $table, string $line): string
    {
        if ($table === '') {
            return $line;
        }

        return sprintf('%s: %s', $table, $line);
    }

    /**
     * Prefix every line with the table it belongs to
     *
     * @param string             $table The table name, or `''` outside any table
     * @param array<int, string> $lines The lines
     *
     * @return list<string> The attributed lines
     */
    private function attributeAll(string $table, array $lines): array
    {
        $attributed = [];
        foreach ($lines as $line) {
            $attributed[] = $this->attribute($table, $line);
        }

        return $attributed;
    }
}
