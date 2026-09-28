<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\Migration\SchemaDumpNormaliserService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Migration\SchemaDumpNormaliserService
 *
 * The fixtures are the two renderings actually observed in the field: a drift
 * run over one host's `dev` tier reported 108 differences of which 103 were the
 * same schema printed two ways by two MariaDB versions. Each test below pins
 * one of those renderings so it cannot come back.
 *
 * @coversDefaultClass \Ubix\Service\Migration\SchemaDumpNormaliserService
 * @see                \Ubix\Tests\Tests\Service\Migration\SchemaDumpNormaliserServiceTestTest PHPUnit test case
 */
final class SchemaDumpNormaliserServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SchemaDumpNormaliserService::class);
    }

    /**
     * A column-level collation that only repeats the table default is dropped,
     * so the two renderings of one schema compare equal.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testRedundantColumnCollationIsDropped(): void
    {
        $withClause = $this->table('creators', [
            '`slug` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,',
            '`display_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,',
        ]);
        $without    = $this->table('creators', [
            '`slug` varchar(64) NOT NULL,',
            '`display_name` varchar(120) NOT NULL,',
        ]);

        $service = $this->service();

        $this->assertSame(
            $service->normalise($without),
            $service->normalise($withClause),
            'A collation repeating the table default is redundant and must not read as drift.',
        );
    }

    /**
     * A redundant `CHARACTER SET` is dropped while a collation that genuinely
     * differs from the table is kept — the reason this is done per table rather
     * than by stripping collation everywhere.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testCollationDifferingFromTheTableIsKept(): void
    {
        $lines = $this->service()->normalise($this->table('webhook_events', [
            '`payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,',
        ]));

        $this->assertContains(
            'webhook_events: `payload` longtext COLLATE utf8mb4_bin NOT NULL,',
            $lines,
            'utf8mb4_bin differs from the table default and is real schema — it must survive.',
        );
    }

    /**
     * MariaDB's auto-generated `json_valid` check is dropped in both renderings
     * — inline on the column, and as a named constraint line.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testGeneratedJsonValidCheckIsDropped(): void
    {
        $inline = $this->table('creators', [
            '`external_links` longtext DEFAULT NULL CHECK (json_valid(`external_links`)),',
        ]);
        $named  = $this->table('creators', [
            '`external_links` longtext DEFAULT NULL,',
            'CONSTRAINT `external_links` CHECK (json_valid(`external_links`)),',
        ]);
        $absent = $this->table('creators', [
            '`external_links` longtext DEFAULT NULL,',
        ]);

        $service  = $this->service();
        $expected = $service->normalise($absent);

        $this->assertSame($expected, $service->normalise($inline), 'Inline json_valid is generated, not schema.');
        $this->assertSame($expected, $service->normalise($named), 'The named rendering is the same constraint.');
    }

    /**
     * A check a person wrote is schema and is kept.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testHumanWrittenCheckIsKept(): void
    {
        $lines = $this->service()->normalise($this->table('tiers', [
            '`price` int(10) unsigned NOT NULL CHECK (`price` > 0),',
        ]));

        $this->assertContains(
            'tiers: `price` int(10) unsigned NOT NULL CHECK (`price` > 0),',
            $lines,
            'Only the auto-generated json_valid shape may be dropped.',
        );
    }

    /**
     * Every line names its table, so a reported difference can be traced.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testLinesAreAttributedToTheirTable(): void
    {
        $lines = $this->service()->normalise($this->table('users', [
            '`creator_name` varchar(255) NOT NULL DEFAULT \'\',',
        ]));

        $this->assertContains(
            'users: `creator_name` varchar(255) NOT NULL DEFAULT \'\',',
            $lines,
            'A stray column with no table named is a finding nobody can act on.',
        );
    }

    /**
     * The same column in two different tables must not cancel out. Without
     * attribution `array_diff` treats the two as one line, so a column dropped
     * from one table while present in another reads as no drift at all.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testIdenticalColumnsInDifferentTablesStayDistinct(): void
    {
        $posts       = $this->table('posts', ['`title` varchar(200) NOT NULL,']);
        $collections = $this->table('collections', ['`title` varchar(200) NOT NULL,']);
        $dump        = $posts . $collections;

        $lines = $this->service()->normalise($dump);

        $this->assertContains('posts: `title` varchar(200) NOT NULL,', $lines);
        $this->assertContains('collections: `title` varchar(200) NOT NULL,', $lines);
    }

    /**
     * Real drift still reads as drift: an index whose *name* differs is a
     * difference, even though the indexed column is identical.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testAnIndexNameDifferenceSurvivesNormalisation(): void
    {
        $service = $this->service();

        $live  = $service->normalise($this->table('users', ['UNIQUE KEY `username` (`display_name`),']));
        $built = $service->normalise($this->table('users', ['UNIQUE KEY `display_name` (`display_name`),']));

        $this->assertNotSame($live, $built, 'A differently named index is real drift and must be reported.');
    }

    /**
     * Dump chrome that varies by server version and dump flags is dropped.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testDumpChromeIsDropped(): void
    {
        $chrome = implode("\n", [
            '-- MySQL dump 10.19  Distrib 10.3.39-MariaDB',
            '-- Host: 10.50.90.54    Database: sowingme',
            '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;',
            '/*M!100616 SET @@SESSION.sandbox=1 */;',
            'DROP TABLE IF EXISTS `creators`;',
            '',
        ]);
        $table  = $this->table('creators', ['`id` int(10) unsigned NOT NULL AUTO_INCREMENT,']);
        $dump   = $chrome . $table;

        $lines = $this->service()->normalise($dump);

        foreach ($lines as $line) {
            $this->assertStringNotContainsString('MySQL dump', $line);
            $this->assertStringNotContainsString('Host:', $line);
            $this->assertStringNotContainsString('/*!', $line);
            $this->assertStringNotContainsString('/*M!', $line);
            $this->assertStringNotContainsString('DROP TABLE', $line);
        }
    }

    /**
     * An `AUTO_INCREMENT=N` counter moves as rows are inserted and is not schema.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testAutoIncrementCounterIsDropped(): void
    {
        $dump = implode("\n", [
            'CREATE TABLE `creators` (',
            '`id` int(10) unsigned NOT NULL,',
            ') ENGINE=InnoDB AUTO_INCREMENT=4127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
            '',
        ]);

        $lines = $this->service()->normalise($dump);

        $this->assertContains(
            'creators: ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
            $lines,
        );
    }

    /**
     * A dump truncated mid-table keeps the columns it did capture, rather than
     * discarding them because no closing line arrived.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testTruncatedDumpKeepsItsColumns(): void
    {
        $lines = $this->service()->normalise("CREATE TABLE `creators` (\n`slug` varchar(64) NOT NULL,\n");

        $this->assertContains('creators: `slug` varchar(64) NOT NULL,', $lines);
    }

    /**
     * A qualified table name attributes to the table, so two dumps that qualify
     * differently still compare equal.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testQualifiedTableNameAttributesToTheTable(): void
    {
        $dump = implode("\n", [
            'CREATE TABLE `sowingme`.`creators` (',
            '`slug` varchar(64) NOT NULL,',
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
            '',
        ]);

        $lines = $this->service()->normalise($dump);

        $this->assertContains('creators: `slug` varchar(64) NOT NULL,', $lines);
    }

    /**
     * An explicit `ROW_FORMAT=DYNAMIC` is InnoDB's own default and is dropped,
     * so a long-lived tier matches a freshly rebuilt schema.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testRedundantDynamicRowFormatIsDropped(): void
    {
        $service = $this->service();

        $stated = implode("\n", [
            'CREATE TABLE `users` (',
            '`id` int(10) unsigned NOT NULL,',
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;',
            '',
        ]);
        $silent = $this->table('users', ['`id` int(10) unsigned NOT NULL,']);

        $this->assertSame(
            $service->normalise($silent),
            $service->normalise($stated),
            'DYNAMIC is InnoDB\'s default; stating it is not a schema difference.',
        );
    }

    /**
     * A row format that is *not* the default says something real about storage
     * and is kept.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testNonDefaultRowFormatIsKept(): void
    {
        $dump = implode("\n", [
            'CREATE TABLE `archive` (',
            '`id` int(10) unsigned NOT NULL,',
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPRESSED;',
            '',
        ]);

        $lines = $this->service()->normalise($dump);

        $this->assertContains(
            'archive: ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPRESSED;',
            $lines,
            'COMPRESSED is not the default and must still be compared.',
        );
    }

    /**
     * Only InnoDB. DYNAMIC is not every engine's default, so it is left alone
     * elsewhere.
     *
     * @return void
     *
     * @covers ::normalise
     */
    public function testRowFormatIsKeptOnOtherEngines(): void
    {
        $dump = implode("\n", [
            'CREATE TABLE `legacy` (',
            '`id` int(10) unsigned NOT NULL,',
            ') ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;',
            '',
        ]);

        $lines = $this->service()->normalise($dump);

        $this->assertContains(
            'legacy: ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;',
            $lines,
            'The default-row-format argument is an InnoDB one.',
        );
    }

    /**
     * Build a one-table dump fragment with the project's usual table defaults
     *
     * @param string             $table The table name
     * @param array<int, string> $body  The column and key lines, already trimmed
     *
     * @return string A `CREATE TABLE` fragment
     */
    private function table(string $table, array $body): string
    {
        $lines = array_merge(
            [sprintf('CREATE TABLE `%s` (', $table)],
            $body,
            [') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;', ''],
        );

        return implode("\n", $lines);
    }

    /**
     * The service under test
     *
     * @return SchemaDumpNormaliserService
     */
    private function service(): SchemaDumpNormaliserService
    {
        return new SchemaDumpNormaliserService($this->createStub(Logger::class));
    }
}
