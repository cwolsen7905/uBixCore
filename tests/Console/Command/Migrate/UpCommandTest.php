<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Migrate;

use ReflectionClass;
use Ubix\Console\Command\Migrate\UpCommand;
use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\Enum\Env;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Migrate\UpCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Migrate\UpCommand
 * @coversDefaultClass \Ubis\Console\Command\Migrate\UpCommand
 */
final class UpCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(UpCommand::class);
    }

    /**
     * The destructive / RequiresDBA hold exit codes are a contract with CI:
     * `.gitlab-ci.yml`'s migrate-apply-{staging,prod} jobs list them under
     * `allow_failure.exit_codes` so an expected hold keeps the pipeline green
     * while a real apply error (exit 1) still reds it. Pin the values so a
     * change here can't silently desync the pipeline gating.
     *
     * @return void
     */
    public function testHoldExitCodesMatchCiContract(): void
    {
        $this->assertSame(3, UpCommand::EXIT_DESTRUCTIVE_PENDING);
        $this->assertSame(4, UpCommand::EXIT_REQUIRES_DBA_PENDING);
    }

    /**
     * A hold dams only its OWN database's later migrations: a pending
     * migration of the same database sorting after the held one waits
     * (strict per-database ordering — it may depend on the held DDL),
     * while migrations of other databases flow. Pre-2026-07-30 the
     * whole run aborted on the first hold, freezing every unrelated
     * database's queue behind an out-of-band migration for days.
     *
     * @return void
     */
    public function testPartitionDamsOnlySameDatabaseBehindHold(): void
    {
        $held      = $this->migrationFile(id: '20260730000001_alter_big_hot_table', database: 'ntl_db', requiresDbaReason: 'Millions of rows; runs out-of-band.');
        $sameDb    = $this->migrationFile(id: '20260730000002_add_index_to_big_hot_table', database: 'ntl_db');
        $otherDb   = $this->migrationFile(id: '20260730000003_create_events_table', database: 'SYSTEMS');
        $partition = $this->invokePartitionPending([$held, $sameDb, $otherDb], Env::DEV, false);

        $this->assertSame([$held], $partition['dbaHeld']);
        $this->assertSame([$otherDb], $partition['applicable']);
        $this->assertCount(1, $partition['dammed']);
        $this->assertSame($sameDb, $partition['dammed'][0]['file']);
        $this->assertSame($held, $partition['dammed'][0]['behind']);
    }

    /**
     * Destructive migrations hold on staging / prod without the
     * acknowledgement flag, apply with it, and always apply on the
     * relaxed tiers (§11.3).
     *
     * @return void
     */
    public function testPartitionHoldsUnacknowledgedDestructiveOnStagingAndProd(): void
    {
        $file = $this->migrationFile(id: '20260730000004_drop_legacy_table', database: 'VSCASH', destructiveReason: 'Drops a soft-deleted table.');

        foreach ([Env::STAGING, Env::PROD] as $environment) {
            $this->assertSame([$file], $this->invokePartitionPending([$file], $environment, false)['destructiveHeld']);
            $this->assertSame([$file], $this->invokePartitionPending([$file], $environment, true)['applicable']);
        }

        $this->assertSame([$file], $this->invokePartitionPending([$file], Env::DEV, false)['applicable']);
    }

    /**
     * The TEST tier applies RequiresDBA migrations inline (§11.8): the
     * unit-test database is rebuilt from scratch with zero rows on every
     * run, so the out-of-band hold's rationale (long online DDL on big
     * hot tables) cannot apply — and holding would leave the test schema
     * permanently missing the columns real tiers gain at reconcile time.
     *
     * @return void
     */
    public function testPartitionRequiresDbaAppliesInlineOnTestTier(): void
    {
        $file      = $this->migrationFile(id: '20260730000001_alter_big_hot_table', database: 'ntl_db', requiresDbaReason: 'Millions of rows; runs out-of-band.');
        $partition = $this->invokePartitionPending([$file], Env::TEST, false);

        $this->assertSame([$file], $partition['applicable']);
        $this->assertSame([], $partition['dbaHeld']);
    }

    /**
     * On every real tier a pending `RequiresDBA:` migration is held
     * (no acknowledgement flag overrides it — §11.8), and a file
     * carrying BOTH headers holds as RequiresDBA (the stronger gate).
     *
     * @return void
     */
    public function testPartitionRequiresDbaHeldOnRealTiers(): void
    {
        $file = $this->migrationFile(
            id:                '20260730000001_alter_big_hot_table',
            database:          'ntl_db',
            requiresDbaReason: 'Millions of rows; runs out-of-band.',
            destructiveReason: 'Also drops a column.',
        );

        foreach ([Env::DEV, Env::SANDBOX, Env::STAGING, Env::PROD] as $environment) {
            $partition = $this->invokePartitionPending([$file], $environment, true);

            $this->assertSame([$file], $partition['dbaHeld']);
            $this->assertSame([], $partition['destructiveHeld']);
            $this->assertSame([], $partition['applicable']);
        }
    }

    /**
     * Invoke the private `partitionPending()` on a dependency-free
     * instance — it reads only its arguments, so the (final,
     * unmockable) constructor collaborators are never touched.
     *
     * @param MigrationFile[] $pending     Pending migration list
     * @param Env             $environment Tier under test
     * @param bool            $ack         Whether `--i-acknowledge-destructive` was passed
     *
     * @return array{applicable: MigrationFile[], dbaHeld: MigrationFile[], destructiveHeld: MigrationFile[], dammed: array<array{file: MigrationFile, behind: MigrationFile}>} The partition
     */
    private function invokePartitionPending(array $pending, Env $environment, bool $ack): array
    {
        $reflection = new ReflectionClass(UpCommand::class);
        $command    = $reflection->newInstanceWithoutConstructor();

        /**
         * `invoke()` returns `mixed`; `partitionPending()` guarantees
         * the partition shape.
         *
         * @var array{applicable: MigrationFile[], dbaHeld: MigrationFile[], destructiveHeld: MigrationFile[], dammed: array<array{file: MigrationFile, behind: MigrationFile}>} $partition
         */
        $partition = $reflection->getMethod('partitionPending')->invoke($command, $pending, $environment, $ack);
        assert(is_array($partition));
        return $partition;
    }

    /**
     * Fabricate a pending migration DTO.
     *
     * @param string  $id                Migration id (filename sans extension)
     * @param string  $database          Target database
     * @param ?string $requiresDbaReason Optional `RequiresDBA:` reason
     * @param ?string $destructiveReason Optional `Destructive:` reason
     *
     * @return MigrationFile Fabricated migration DTO
     */
    private function migrationFile(string $id, string $database, ?string $requiresDbaReason = null, ?string $destructiveReason = null): MigrationFile
    {
        $body = 'ALTER TABLE ' . $database . '.example_table ADD example_column int(11) unsigned NULL DEFAULT NULL;';

        return new MigrationFile(
            id:                $id,
            targetDatabase:    $database,
            description:       'Fabricated migration for UpCommandTest',
            author:            'UpCommandTest',
            body:              $body,
            checksum:          hash('sha256', $body),
            filePath:          '/dev/null',
            destructiveReason: $destructiveReason,
            requiresDbaReason: $requiresDbaReason,
        );
    }
}
