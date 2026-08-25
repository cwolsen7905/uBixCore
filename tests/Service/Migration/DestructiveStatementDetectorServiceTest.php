<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\NullLogger;
use Ubix\Enum\Migration\DestructiveStatementKind;
use Ubix\Service\Migration\DestructiveStatementDetectorService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\Service\Migration\DestructiveStatementDetectorService
 *
 * @coversDefaultClass \Ubix\Service\Migration\DestructiveStatementDetectorService
 * @coversDefaultClass \Ubis\Service\Migration\DestructiveStatementDetectorService
 */
final class DestructiveStatementDetectorServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(DestructiveStatementDetectorService::class);
    }

    /**
     * A clean CREATE TABLE body matches no destructive patterns.
     *
     * @return void
     * @covers ::detect
     */
    public function testDetectReturnsEmptyForCreateTable(): void
    {
        $detector = new DestructiveStatementDetectorService(new NullLogger());
        $body     = "CREATE TABLE VSCASH.Foo (\n    id INT PRIMARY KEY\n);";
        $this->assertSame([], $detector->detect($body));
    }

    /**
     * Each destructive statement pattern from `migrations.md` §11.1
     * fires exactly once with the right `kind`. Lines are 1-indexed.
     *
     * @return void
     * @covers ::detect
     */
    public function testDetectFiresForEveryStandardKind(): void
    {
        $cases = [
            'ALTER TABLE VSCASH.Foo DROP COLUMN bar;'   => DestructiveStatementKind::ALTER_TABLE_DROP_COLUMN,
            'ALTER TABLE VSCASH.Foo MODIFY bar BIGINT;' => DestructiveStatementKind::ALTER_TABLE_MODIFY,
            'DELETE FROM VSCASH.Foo;'                   => DestructiveStatementKind::DELETE_FROM_NO_WHERE,
            'DROP DATABASE Junk;'                       => DestructiveStatementKind::DROP_DATABASE,
            'DROP INDEX idx_old ON VSCASH.Foo;'         => DestructiveStatementKind::DROP_INDEX,
            'DROP TABLE VSCASH.Foo;'                    => DestructiveStatementKind::DROP_TABLE,
            'DROP VIEW VSCASH.Old_View;'                => DestructiveStatementKind::DROP_VIEW,
            'RENAME TABLE VSCASH.Foo TO VSCASH.Bar;'    => DestructiveStatementKind::RENAME_TABLE,
            'TRUNCATE TABLE VSCASH.Foo;'                => DestructiveStatementKind::TRUNCATE_TABLE,
        ];

        $detector = new DestructiveStatementDetectorService(new NullLogger());

        foreach ($cases as $body => $expectedKind) {
            $matches = $detector->detect($body);
            $this->assertNotEmpty($matches, sprintf('Expected detection for body: %s', $body));
            $kinds = array_map(static fn ($match) => $match->kind, $matches);   // phpcs:ignore SlevomatCodingStandard.Functions.DisallowArrowFunction.DisallowedArrowFunction
            $this->assertContains($expectedKind, $kinds, sprintf('Expected kind %s for body: %s', $expectedKind->value, $body));
        }
    }

    /**
     * `DELETE FROM` with a `WHERE` clause in the same statement is
     * not flagged. The statement boundary is the next semicolon.
     *
     * @return void
     * @covers ::detect
     */
    public function testDetectIgnoresDeleteFromWithWhere(): void
    {
        $detector = new DestructiveStatementDetectorService(new NullLogger());
        $body     = 'DELETE FROM VSCASH.Foo WHERE id = 1;';
        $this->assertSame([], $detector->detect($body));
    }

    /**
     * A line-comment containing a destructive keyword is stripped
     * before scanning — a comment annotating future work does not
     * trigger the lint.
     *
     * @return void
     * @covers ::detect
     */
    public function testDetectIgnoresLineCommentedDestructiveKeyword(): void
    {
        $detector = new DestructiveStatementDetectorService(new NullLogger());
        $body     = "-- We will eventually DROP TABLE Foo, but not yet.\nCREATE TABLE VSCASH.Foo (id INT);";
        $this->assertSame([], $detector->detect($body));
    }

    /**
     * Block comments around a destructive keyword are stripped while
     * preserving newlines so subsequent line numbers stay accurate.
     *
     * @return void
     * @covers ::detect
     */
    public function testDetectIgnoresBlockCommentedDestructiveKeyword(): void
    {
        $detector = new DestructiveStatementDetectorService(new NullLogger());
        $body     = "/* Note: we used to DROP TABLE Foo here.\nNo longer needed. */\nCREATE TABLE VSCASH.Foo (id INT);";
        $matches  = $detector->detect($body);
        $this->assertSame([], $matches);
    }

    /**
     * Multiple destructive statements in the same body all surface,
     * each with its own line number.
     *
     * @return void
     * @covers ::detect
     */
    public function testDetectReportsMultipleStatementsWithLineNumbers(): void
    {
        $detector = new DestructiveStatementDetectorService(new NullLogger());
        $body     = "DROP TABLE VSCASH.Foo;\nCREATE TABLE VSCASH.Bar (id INT);\nTRUNCATE TABLE VSCASH.Baz;";
        $matches  = $detector->detect($body);

        $this->assertCount(2, $matches);

        $byKind = [];
        foreach ($matches as $match) {
            $byKind[$match->kind->value] = $match->lineNumber;
        }
        $this->assertSame(1, $byKind[DestructiveStatementKind::DROP_TABLE->value] ?? null);
        $this->assertSame(3, $byKind[DestructiveStatementKind::TRUNCATE_TABLE->value] ?? null);
    }

    /**
     * `ALTER TABLE … MODIFY` matches even when MODIFY appears on a
     * separate line from the ALTER TABLE clause.
     *
     * @return void
     * @covers ::detect
     */
    public function testDetectMatchesAlterTableModifyAcrossLines(): void
    {
        $detector = new DestructiveStatementDetectorService(new NullLogger());
        $body     = "ALTER TABLE VSCASH.Foo\n    MODIFY COLUMN bar BIGINT NOT NULL;";
        $matches  = $detector->detect($body);

        $kinds = array_map(static fn ($match) => $match->kind, $matches);   // phpcs:ignore SlevomatCodingStandard.Functions.DisallowArrowFunction.DisallowedArrowFunction
        $this->assertContains(DestructiveStatementKind::ALTER_TABLE_MODIFY, $kinds);
    }
}
