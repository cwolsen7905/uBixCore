<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\NullLogger;
use Ubix\Service\Migration\HotTableAlterDetectorService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Migration\HotTableAlterDetectorService
 *
 * @coversDefaultClass \Ubix\Service\Migration\HotTableAlterDetectorService
 * @coversDefaultClass \Ubis\Service\Migration\HotTableAlterDetectorService
 * @see                \Ubix\Tests\Tests\Service\Migration\HotTableAlterDetectorServiceTestTest PHPUnit test case
 */
final class HotTableAlterDetectorServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(HotTableAlterDetectorService::class);
    }

    /**
     * The 2026-07-29 incident shape: a migration creates one table and
     * ALTERs three pre-existing ones — all three are flagged, the
     * in-file-created table is not.
     *
     * @return void
     *
     * @covers ::detect
     */
    public function testFlagsAltersOfTablesNotCreatedInFile(): void
    {
        $body = <<<'SQL'
        CREATE TABLE LEDGER.Payment_Attempts (
            id int unsigned NOT NULL AUTO_INCREMENT
        );
        ALTER TABLE LEDGER.Payment_Attempts ADD KEY idx_own (id);
        ALTER TABLE LEDGER.Order_Holds ADD COLUMN attempt_id int NULL;
        ALTER TABLE LEDGER.Order_Hold_Events ADD COLUMN attempt_id int NULL;
        CREATE INDEX idx_attempt ON LEDGER.Gateway_Response_Codes (attempt_id);
        SQL;

        $offenders = (new HotTableAlterDetectorService(new NullLogger()))->detect($body);

        $this->assertCount(3, $offenders);
        $this->assertStringContainsString('Order_Holds', $offenders[0]);
        $this->assertStringContainsString('Order_Hold_Events', $offenders[1]);
        $this->assertStringContainsString('Gateway_Response_Codes', $offenders[2]);
    }

    /**
     * Altering only tables created in the same file (any quoting/case) is
     * clean, and SQL comments never produce false positives.
     *
     * @return void
     *
     * @covers ::detect
     */
    public function testOwnTablesAndCommentsAreClean(): void
    {
        $body = <<<'SQL'
        -- ALTER TABLE LEDGER.Should_Not_Match ADD COLUMN x int;
        /* CREATE INDEX nope ON LEDGER.Also_No (x); */
        CREATE TABLE IF NOT EXISTS `LEDGER`.`New_Thing` (id int);
        ALTER TABLE ledger.new_thing ADD COLUMN y int;
        SQL;

        $this->assertSame([], (new HotTableAlterDetectorService(new NullLogger()))->detect($body));
    }

    /**
     * A backtick-quoted, schema-qualified target renders as a clean
     * `SCHEMA.Table` in the offender string — no leaked backticks.
     *
     * @return void
     *
     * @covers ::detect
     */
    public function testBacktickQualifiedOffenderRendersClean(): void
    {
        $body = 'ALTER TABLE `LEDGER`.`Order_Holds` ADD COLUMN attempt_id int NULL;';

        $offenders = (new HotTableAlterDetectorService(new NullLogger()))->detect($body);

        $this->assertSame(['LEDGER.Order_Holds (ALTER TABLE on line 1)'], $offenders);
    }

    /**
     * A bare CREATE TABLE matches a schema-qualified ALTER of the same
     * table (and vice versa) — a migration targets a single database, so
     * qualifier asymmetry must not produce a false positive.
     *
     * @return void
     *
     * @covers ::detect
     */
    public function testQualifierAsymmetryIsNotFlagged(): void
    {
        $body = <<<'SQL'
        CREATE TABLE Bare_Created (id int);
        ALTER TABLE LEDGER.Bare_Created ADD COLUMN x int;
        CREATE TABLE LEDGER.Qualified_Created (id int);
        ALTER TABLE Qualified_Created ADD COLUMN y int;
        SQL;

        $this->assertSame([], (new HotTableAlterDetectorService(new NullLogger()))->detect($body));
    }

    /**
     * The full MariaDB index/alter grammar is detected — index-type
     * clauses, FULLTEXT/SPATIAL kinds, IF (NOT) EXISTS, OR REPLACE, and
     * ONLINE/IGNORE alters must not bypass the guard.
     *
     * @return void
     *
     * @covers ::detect
     */
    public function testSyntaxVariantsDoNotBypassDetection(): void
    {
        $body = <<<'SQL'
        CREATE INDEX idx_a USING BTREE ON LEDGER.Table_A (x);
        CREATE FULLTEXT INDEX idx_b ON LEDGER.Table_B (x);
        CREATE SPATIAL INDEX idx_c ON LEDGER.Table_C (x);
        CREATE INDEX IF NOT EXISTS idx_d ON LEDGER.Table_D (x);
        CREATE OR REPLACE UNIQUE INDEX idx_e ON LEDGER.Table_E (x);
        ALTER ONLINE TABLE LEDGER.Table_F ADD COLUMN x int;
        ALTER IGNORE TABLE LEDGER.Table_G ADD COLUMN x int;
        ALTER TABLE IF EXISTS LEDGER.Table_H ADD COLUMN x int;
        SQL;

        $offenders = (new HotTableAlterDetectorService(new NullLogger()))->detect($body);

        $this->assertCount(8, $offenders);
        foreach (['Table_F', 'Table_G', 'Table_H', 'Table_A', 'Table_B', 'Table_C', 'Table_D', 'Table_E'] as $index => $table) {
            $this->assertStringContainsString($table, $offenders[$index]);
        }
    }
}
