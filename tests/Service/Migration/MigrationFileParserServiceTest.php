<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use InvalidArgumentException;
use Psr\Log\NullLogger;
use Ubix\Service\Migration\DestructiveStatementDetectorService;
use Ubix\Service\Migration\HotTableAlterDetectorService;
use Ubix\Service\Migration\MigrationFileParserService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\Service\Migration\MigrationFileParserService
 *
 * @coversDefaultClass \Ubix\Service\Migration\MigrationFileParserService
 * @coversDefaultClass \Ubis\Service\Migration\MigrationFileParserService
 */
final class MigrationFileParserServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const string MIGRATION_ID = '20260507120000_slice_one_five_fixture';

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationFileParserService::class);
    }

    /**
     * A clean migration with the four required headers parses
     * cleanly, with `destructiveReason` null.
     *
     * @return void
     * @covers ::parse
     */
    public function testParseSucceedsForCleanMigration(): void
    {
        $body   = 'CREATE TABLE VSCASH.Foo (id INT PRIMARY KEY);';
        $path   = $this->writeFixture(self::MIGRATION_ID, $body, null);
        $parser = $this->buildParser();

        $migration = $parser->parse($path);

        $this->assertSame(self::MIGRATION_ID, $migration->id);
        $this->assertSame('VSCASH', $migration->targetDatabase);
        $this->assertNull($migration->destructiveReason);
    }

    /**
     * A destructive body WITH a `Destructive:` header parses cleanly
     * and forwards the reason to `MigrationFile::$destructiveReason`.
     *
     * @return void
     * @covers ::parse
     */
    public function testParseAcceptsDestructiveMigrationWithHeader(): void
    {
        $body   = 'DROP TABLE VSCASH._deprecated_Old;';
        $reason = 'Verified zero reads against legacy + Ubix for 30 days.';
        $path   = $this->writeFixture(self::MIGRATION_ID, $body, $reason);
        $parser = $this->buildParser();

        $migration = $parser->parse($path);

        $this->assertSame($reason, $migration->destructiveReason);
    }

    /**
     * A destructive body WITHOUT a `Destructive:` header throws,
     * with a message that quotes the offending statement so the
     * engineer knows where to look.
     *
     * @return void
     * @covers ::parse
     */
    public function testParseRejectsDestructiveMigrationMissingHeader(): void
    {
        $body   = 'DROP TABLE VSCASH._deprecated_Old;';
        $path   = $this->writeFixture(self::MIGRATION_ID, $body, null);
        $parser = $this->buildParser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Destructive:/');
        $parser->parse($path);
    }

    /**
     * A destructive keyword that lives only inside a SQL comment
     * does NOT trigger the lint — the parser+detector are
     * comment-aware.
     *
     * @return void
     * @covers ::parse
     */
    public function testParseAcceptsBodyWhereDestructiveKeywordIsCommentedOut(): void
    {
        $body   = "-- We will eventually DROP TABLE Foo, but not yet.\nCREATE TABLE VSCASH.Foo (id INT PRIMARY KEY);";
        $path   = $this->writeFixture(self::MIGRATION_ID, $body, null);
        $parser = $this->buildParser();

        $migration = $parser->parse($path);

        $this->assertNull($migration->destructiveReason);
    }

    /**
     * A CRLF-terminated file parses to the same body content as its
     * LF-terminated twin. Regression test: the previous
     * implementation's `$byteOffset` accounting assumed a 1-byte
     * delimiter, so a CRLF file's body would start mid-token in the
     * last header value — e.g. `man\n\nALTER…` instead of
     * `ALTER…`, which then died as `ERROR 1064 near 'man…'` when
     * piped through the mariadb CLI.
     *
     * @return void
     * @covers ::parse
     */
    public function testParseNormalizesCrlfLineEndings(): void
    {
        $body         = "ALTER TABLE VSCASH.Foo\n    ADD bar VARCHAR(22) DEFAULT NULL;";
        $expectedBody = $body;

        $lfPath   = $this->writeFixture(self::MIGRATION_ID, $body, null);
        $crlfPath = $this->writeFixtureWithCrlf(self::MIGRATION_ID, $body, null);

        $parser = $this->buildParser();

        $lfMigration   = $parser->parse($lfPath);
        $crlfMigration = $parser->parse($crlfPath);

        $this->assertSame($expectedBody, $lfMigration->body);
        $this->assertSame($expectedBody, $crlfMigration->body);
        $this->assertSame($lfMigration->checksum, $crlfMigration->checksum);
    }

    /**
     * A `RequiresDBA:` header (§11.8) is captured verbatim into
     * `requiresDbaReason`. It is header-declared only, so a
     * non-destructive body carrying the marker still parses cleanly
     * with `destructiveReason` null.
     *
     * @return void
     * @covers ::parse
     */
    public function testParseCapturesRequiresDbaHeader(): void
    {
        $reason = 'Full-table rewrite on a hot table; run via pt-osc with throttling.';
        $path   = $this->writeFixtureWithRequiresDba(self::MIGRATION_ID, 'CREATE TABLE VSCASH.Foo (id INT PRIMARY KEY);', null, $reason);
        $parser = $this->buildParser();

        $migration = $parser->parse($path);

        $this->assertSame($reason, $migration->requiresDbaReason);
        $this->assertNull($migration->destructiveReason);
    }

    /**
     * `RequiresDBA:` and `Destructive:` are orthogonal — a migration
     * may carry both, and the parser forwards each to its own field.
     *
     * @return void
     * @covers ::parse
     */
    public function testParseCapturesBothRequiresDbaAndDestructive(): void
    {
        $destructive = 'Dropping the legacy column after a full rewrite.';
        $dba         = 'Rewrite is online-DDL; coordinate with #databases.';
        $path        = $this->writeFixtureWithRequiresDba(self::MIGRATION_ID, 'ALTER TABLE VSCASH.Foo DROP COLUMN bar;', $destructive, $dba);
        $parser      = $this->buildParser();

        $migration = $parser->parse($path);

        $this->assertSame($destructive, $migration->destructiveReason);
        $this->assertSame($dba, $migration->requiresDbaReason);
    }

    /**
     * §11.9: a post-cutoff migration ALTERing a table it did not create,
     * with neither `RequiresDBA:` nor `AlterAck:`, fails to parse — the
     * 2026-07-29 replication-lag incident shape is rejected at the gate.
     *
     * @return void
     *
     * @covers ::parse
     */
    public function testHotTableAlterWithoutAckThrows(): void
    {
        $id  = '20260801000000_hot_alter_guard_fixture';
        $dir = sys_get_temp_dir() . '/ubix-migration-fixtures-' . getmypid();
        if (! is_dir($dir)) {
            mkdir($dir, 0o777, true);
        }
        $path = $dir . '/' . $id . '.sql';
        file_put_contents($path, '-- Migration: ' . $id . "\n-- Database: VSCASH\n-- Description: Guard fixture.\n-- Author: Test\n\nALTER TABLE VSCASH.Big_Existing ADD COLUMN x int;\n");

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/AlterAck/');

        $this->buildParser()->parse($path);
    }

    /**
     * §11.9: the same ALTER parses when the author vouches via `AlterAck:`,
     * and the reason is captured onto the DTO.
     *
     * @return void
     *
     * @covers ::parse
     */
    public function testHotTableAlterWithAlterAckHeaderParses(): void
    {
        $id  = '20260801000001_hot_alter_acked_fixture';
        $dir = sys_get_temp_dir() . '/ubix-migration-fixtures-' . getmypid();
        if (! is_dir($dir)) {
            mkdir($dir, 0o777, true);
        }
        $path = $dir . '/' . $id . '.sql';
        file_put_contents($path, '-- Migration: ' . $id . "\n-- Database: VSCASH\n-- Description: Guard fixture.\n-- Author: Test\n-- AlterAck: tiny lookup table, seconds to alter\n\nALTER TABLE VSCASH.Tiny_Lookup ADD COLUMN x int;\n");

        $migration = $this->buildParser()->parse($path);

        $this->assertSame('tiny lookup table, seconds to alter', $migration->alterAckReason);
    }

    /**
     * A multi-line header continuation containing `word:` substrings (e.g.
     * "`migrate:reconcile.`" or "pipeline-safe: …") stays part of the open
     * header instead of spawning bogus `migrate` / `pipeline-safe` headers —
     * the truncation corrupted the §11.8 hold banner operators read (Claude
     * review of 04d1fe2b, finding 2). Only the known header vocabulary
     * starts a header line.
     *
     * @return void
     *
     * @covers ::parse
     */
    public function testHeaderContinuationLinesMayContainColons(): void
    {
        $id  = '20260730000100_colon_continuation_fixture';
        $dir = sys_get_temp_dir() . '/ubix-migration-fixtures-' . getmypid();
        if (! is_dir($dir)) {
            mkdir($dir, 0o777, true);
        }
        $path    = $dir . '/' . $id . '.sql';
        $header  = '-- Migration: ' . $id . "\n";
        $header .= "-- Database: VSCASH\n";
        $header .= "-- Description: Guard fixture.\n";
        $header .= "-- Author: Test\n";
        $header .= "-- RequiresDBA: Applied out-of-band by the MariaDB team, then recorded per tier with\n";
        $header .= "--              migrate:reconcile. The body below matches EXACTLY what they will run.\n";
        $header .= "--              Not pipeline-safe: the index build forces INPLACE at prod scale.\n";
        file_put_contents($path, $header . "\nCREATE TABLE VSCASH.Foo (id INT PRIMARY KEY);\n");

        $migration = $this->buildParser()->parse($path);

        $this->assertSame(
            'Applied out-of-band by the MariaDB team, then recorded per tier with migrate:reconcile. The body below matches EXACTLY what they will run. Not pipeline-safe: the index build forces INPLACE at prod scale.',
            $migration->requiresDbaReason,
        );
    }

    /**
     * Build a parser with a null logger and a real detector — same
     * graph the runtime DI builds.
     *
     * @return MigrationFileParserService
     */
    private function buildParser(): MigrationFileParserService
    {
        return new MigrationFileParserService(
            new NullLogger(),
            new DestructiveStatementDetectorService(new NullLogger()),
            new HotTableAlterDetectorService(new NullLogger()),
        );
    }

    /**
     * Write a fixture migration file in a per-process temp directory
     * and return its absolute path. The header always declares
     * `Database: VSCASH` and `Author: Test`.
     *
     * @param string  $id     Migration ID — also the filename without `.sql`
     * @param string  $body   SQL body
     * @param ?string $reason Destructive reason; null omits the 5th header line
     *
     * @return string Absolute path to the written fixture file
     */
    private function writeFixture(string $id, string $body, ?string $reason): string
    {
        $dir = sys_get_temp_dir() . '/ubix-migration-fixtures-' . getmypid();
        if (! is_dir($dir)) {
            mkdir($dir, 0o777, true);
        }

        $header  = '-- Migration: ' . $id . "\n";
        $header .= "-- Database: VSCASH\n";
        $header .= "-- Description: Slice 1.5 fixture.\n";
        $header .= "-- Author: Test\n";
        if ($reason !== null) {
            $header .= '-- Destructive: ' . $reason . "\n";
        }

        $path = $dir . '/' . $id . '.sql';
        file_put_contents($path, $header . "\n" . $body);
        return $path;
    }

    /**
     * Sibling of `writeFixture()` that emits CRLF line terminators
     * everywhere — used by the regression test for the offset bug.
     *
     * @param string  $id     Migration ID — also the filename without `.sql`
     * @param string  $body   SQL body (LF — converted to CRLF here)
     * @param ?string $reason Destructive reason; null omits the 5th header line
     *
     * @return string Absolute path to the written fixture file
     */
    private function writeFixtureWithCrlf(string $id, string $body, ?string $reason): string
    {
        $dir = sys_get_temp_dir() . '/ubix-migration-fixtures-crlf-' . getmypid();
        if (! is_dir($dir)) {
            mkdir($dir, 0o777, true);
        }

        $header  = '-- Migration: ' . $id . "\r\n";
        $header .= "-- Database: VSCASH\r\n";
        $header .= "-- Description: Slice 1.5 fixture.\r\n";
        $header .= "-- Author: Test\r\n";
        if ($reason !== null) {
            $header .= '-- Destructive: ' . $reason . "\r\n";
        }

        $crlfBody = str_replace("\n", "\r\n", $body);
        $path     = $dir . '/' . $id . '.sql';
        file_put_contents($path, $header . "\r\n" . $crlfBody);
        return $path;
    }

    /**
     * Sibling of `writeFixture()` that can additionally emit a
     * `RequiresDBA:` header line — used by the §11.8 parse tests.
     *
     * @param string  $id                Migration ID — also the filename without `.sql`
     * @param string  $body              SQL body
     * @param ?string $destructiveReason Destructive reason; null omits the `Destructive:` line
     * @param ?string $requiresDbaReason RequiresDBA reason; null omits the `RequiresDBA:` line
     *
     * @return string Absolute path to the written fixture file
     */
    private function writeFixtureWithRequiresDba(string $id, string $body, ?string $destructiveReason, ?string $requiresDbaReason): string
    {
        $dir = sys_get_temp_dir() . '/ubix-migration-fixtures-dba-' . getmypid();
        if (! is_dir($dir)) {
            mkdir($dir, 0o777, true);
        }

        $header  = '-- Migration: ' . $id . "\n";
        $header .= "-- Database: VSCASH\n";
        $header .= "-- Description: Slice 1.5 fixture.\n";
        $header .= "-- Author: Test\n";
        if ($destructiveReason !== null) {
            $header .= '-- Destructive: ' . $destructiveReason . "\n";
        }
        if ($requiresDbaReason !== null) {
            $header .= '-- RequiresDBA: ' . $requiresDbaReason . "\n";
        }

        $path = $dir . '/' . $id . '.sql';
        file_put_contents($path, $header . "\n" . $body);
        return $path;
    }
}
