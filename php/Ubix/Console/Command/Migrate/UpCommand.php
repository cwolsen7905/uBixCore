<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Migrate;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Throwable;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Console\Command\AbstractMigrationCommand as MigrationCommand;
use Ubix\DataTransferObject\Migration\MigrationFile;
use Ubix\DataTransferObject\PdoError;
use Ubix\Enum\Env;
use Ubix\Exception\DtoException;
use Ubix\Service\Migration\DestructiveBackupService;
use Ubix\Service\Migration\MigrationApplyService;
use Ubix\Service\Migration\MigrationConnectionTargetService;
use Ubix\Service\Migration\MigrationNotificationService;
use Ubix\Service\Migration\MigrationRunnerService;

/**
 * `migrate:up` — apply every pending migration in filename order.
 *
 * Per `docs/standards/migrations.md` §4.1. The pending set is
 * partitioned per migration (§10): held and dammed migrations are
 * skipped while everything else applies in the same run. Hold and
 * failure modes:
 *
 * - **RequiresDBA migration pending (every tier but test)** → that
 *   migration is HELD (skipped, banner with its `migrate:reconcile`
 *   command) and routed to the MariaDB team (§11.8); everything else
 *   pending still applies, except same-database migrations dammed
 *   behind it. No flag overrides this hold.
 * - **Destructive migration pending without ack on staging / prod**
 *   → that migration is HELD the same way (banner of IDs + reasons).
 *   Re-running with `--i-acknowledge-destructive` applies it and
 *   stamps a `+destructive-ack` suffix into
 *   `Schema_Migrations.applied_by`.
 * - **Concurrent runner already holds the advisory lock** → exits
 *   non-zero with the lock-busy message; no rows mutated.
 * - **Pre-flight or apply failure** → no tracker row written; the
 *   loop exits at the first failure (forward-fix only per §1).
 *
 * @see \Ubix\Tests\Console\Command\Migrate\UpCommandTest PHPUnit test case
 */
final class UpCommand extends MigrationCommand
{
    /**
     * Exit code when a `Destructive:` migration was HELD (skipped, applied
     * nothing of it) because it is pending on staging / prod without
     * `--i-acknowledge-destructive` — every other applicable migration was
     * still applied this run. Distinct from Command::FAILURE (1, a real error)
     * so the `.migrate_apply` CI wrapper can convert the expected hold to a
     * green job; the `-destructive` button remains the only way the held DDL
     * runs. See `.gitlab-ci.yml` and `migrations.md` §11.3.1.
     */
    public const int EXIT_DESTRUCTIVE_PENDING = 3;

    /**
     * Exit code when a `RequiresDBA:` migration was HELD (every tier but
     * test) — every other applicable migration was still applied this run.
     * Wins over EXIT_DESTRUCTIVE_PENDING when both classes are held in one
     * run (the stronger gate names the exit; the job log banners both).
     * Distinct from Command::FAILURE for the same CI-wrapper reason as
     * EXIT_DESTRUCTIVE_PENDING; the held migration is applied out-of-band by
     * the MariaDB team then recorded via `migrate:reconcile` — `migrations.md`
     * §11.8.
     */
    public const int EXIT_REQUIRES_DBA_PENDING = 4;

    /**
     * Constructor
     *
     * @param Logger                           $logger                  Logger
     * @param MigrationConnectionTargetService $connectionTargetService Resolves and applies --target / --username
     * @param MigrationRunnerService           $runnerService           Pending list + advisory lock
     * @param MigrationApplyService            $applyService            Applies a single migration end-to-end
     * @param DestructiveBackupService         $backupService           Pre-apply snapshot for destructive migrations on staging / prod
     * @param MigrationNotificationService     $notificationService     Posts a #databases Slack notice after applying on dev / staging / prod
     */
    public function __construct(
        Logger $logger,
        MigrationConnectionTargetService $connectionTargetService,
        private MigrationRunnerService $runnerService,
        private MigrationApplyService $applyService,
        private DestructiveBackupService $backupService,
        private MigrationNotificationService $notificationService,
    ) {
        parent::__construct($logger, $connectionTargetService);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Apply every pending migration in filename order')
            ->setHelp('Iterates the migrations directory, applies each pending file via the mariadb CLI, and records a `Schema_Migrations` row per success. Use `--dry-run` to print the plan without touching the DB.')
            ->addOption('database', null, InputOption::VALUE_REQUIRED, 'Restrict to one target database')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Print the plan without applying anything')
            ->addOption('i-acknowledge-destructive', null, InputOption::VALUE_NONE, 'Required on staging / prod when any pending migration carries a `Destructive:` header')
            ->addOption('yes', 'y', InputOption::VALUE_NONE, 'Skip the interactive target-confirmation prompt (required for CI / non-interactive runs)');
        $this->configureTargetOptions();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int
    {
        if (! $this->applyTargetOptions($input, $output)) {
            return Command::FAILURE;
        }

        $databaseFilter = $this->stringOption($input, 'database');
        $isDryRun       = (bool) $input->getOption('dry-run');
        $destructiveAck = (bool) $input->getOption('i-acknowledge-destructive');
        $environment    = $this->resolveRuntimeEnvironment();

        $pending = $this->runnerService->getPendingMigrations($databaseFilter);
        if ($pending === []) {
            $output->writeln('<info>No pending migrations.</info>');
            return Command::SUCCESS;
        }

        $partition = $this->partitionPending($pending, $environment, $destructiveAck);
        $this->renderHoldBanners($partition, $output);

        $holdExit = Command::SUCCESS;
        if ($partition['dbaHeld'] !== []) {
            $holdExit = self::EXIT_REQUIRES_DBA_PENDING;
        } elseif ($partition['destructiveHeld'] !== []) {
            $holdExit = self::EXIT_DESTRUCTIVE_PENDING;
        }

        $applicable = $partition['applicable'];
        if ($applicable === []) {
            $output->writeln('<comment>No applicable migrations — everything pending is held or dammed behind a hold.</comment>');
            return $holdExit;
        }

        $this->renderApplyBanner($output, $environment, $applicable);

        if ($isDryRun) {
            $output->writeln('<comment>--dry-run: nothing applied.</comment>');
            return $holdExit;
        }

        if (! $this->confirmApply($input, $output)) {
            $output->writeln('<comment>Aborted by user.</comment>');
            return Command::SUCCESS;
        }

        if (! $this->runnerService->acquireAdvisoryLock()) {
            $output->writeln(sprintf(
                '<error>Migration advisory lock `%s` is busy or unavailable. Another runner may be in progress.</error>',
                MigrationRunnerService::ADVISORY_LOCK_NAME,
            ));
            return Command::FAILURE;
        }

        try {
            $applyResult = $this->applyAll($applicable, $environment, $destructiveAck, $output);
        } finally {
            $this->runnerService->releaseAdvisoryLock();
        }

        return $applyResult === Command::SUCCESS ? $holdExit : $applyResult;
    }

    /**
     * Partition the pending list into what this run may apply and what
     * it must hold, per `migrations.md` §10 / §11.3 / §11.8.
     *
     * Holds are per-migration, not per-run (the pre-2026-07-30 model
     * aborted the whole run on the first hold, which dammed every
     * unrelated migration behind a `RequiresDBA:` hold for however
     * long the out-of-band apply took):
     *
     * - **dbaHeld** — carries `RequiresDBA:`; held on every tier
     *   except TEST, where the unit-test database is rebuilt from
     *   scratch with zero rows so the hold's rationale (long online
     *   DDL on big hot tables) cannot apply and holding would leave
     *   the test schema missing reconcile-time columns.
     * - **destructiveHeld** — carries `Destructive:` without
     *   `--i-acknowledge-destructive` on staging / prod (§11.3; the
     *   other tiers apply destructive migrations inline). A file
     *   carrying both headers holds as dbaHeld (the stronger gate).
     * - **dammed** — sorts after a held migration of the SAME target
     *   database. Strict filename ordering is preserved per database
     *   (a later migration may depend on the held one); migrations of
     *   other databases flow. Cross-database dependencies are already
     *   discouraged by the one-database-per-migration rule (§2.1).
     * - **applicable** — everything else, in original order.
     *
     * @param MigrationFile[] $pending     Pending migration list, ascending id order
     * @param Env             $environment Resolved runtime environment
     * @param bool            $ack         Whether `--i-acknowledge-destructive` was passed
     *
     * @return array{applicable: MigrationFile[], dbaHeld: MigrationFile[], destructiveHeld: MigrationFile[], dammed: array<array{file: MigrationFile, behind: MigrationFile}>} The partition
     */
    private function partitionPending(array $pending, Env $environment, bool $ack): array
    {
        $holdDba         = $environment !== Env::TEST;
        $holdDestructive = ($environment === Env::STAGING || $environment === Env::PROD) && ! $ack;

        $applicable      = [];
        $dbaHeld         = [];
        $destructiveHeld = [];
        $dammed          = [];
        $heldByDatabase  = [];

        foreach ($pending as $file) {
            $blocker = $heldByDatabase[$file->targetDatabase] ?? null;
            if ($blocker !== null) {
                $dammed[] = ['behind' => $blocker, 'file' => $file];
                continue;
            }

            if ($holdDba && $file->requiresDbaReason !== null) {
                $dbaHeld[]                             = $file;
                $heldByDatabase[$file->targetDatabase] = $file;
                continue;
            }

            if ($holdDestructive && $file->destructiveReason !== null) {
                $destructiveHeld[]                     = $file;
                $heldByDatabase[$file->targetDatabase] = $file;
                continue;
            }

            $applicable[] = $file;
        }

        return [
            'applicable'      => $applicable,
            'dammed'          => $dammed,
            'dbaHeld'         => $dbaHeld,
            'destructiveHeld' => $destructiveHeld,
        ];
    }

    /**
     * Print the hold banners for a partition: the RequiresDBA table
     * with its per-migration `migrate:reconcile` command (§11.8), the
     * destructive table with the acknowledgement hint (§11.3), and the
     * dammed list (same-database migrations waiting behind a hold).
     * Purely informational — the partition already decided what runs.
     *
     * @param array{applicable: MigrationFile[], dbaHeld: MigrationFile[], destructiveHeld: MigrationFile[], dammed: array<array{file: MigrationFile, behind: MigrationFile}>} $partition Partitioned pending list
     * @param Output                                                                                                                                                           $output    Console output
     *
     * @return void
     */
    private function renderHoldBanners(array $partition, Output $output): void
    {
        if ($partition['dbaHeld'] !== []) {
            $output->writeln('<error>REQUIRES-DBA MIGRATIONS PENDING (held, not applied):</error>');
            foreach ($partition['dbaHeld'] as $file) {
                $output->writeln(sprintf('  %s (%s)', $file->id, $file->targetDatabase));
                $output->writeln(sprintf('    Reason: %s', $file->requiresDbaReason ?? ''));
            }
            $output->writeln('');
            $output->writeln('<comment>Apply out-of-band via the MariaDB team (online DDL), then record each with:</comment>');
            foreach ($partition['dbaHeld'] as $file) {
                $output->writeln(sprintf('  php bin/ubix migrate:reconcile %s --reason="..."', $file->id));
            }
            $output->writeln('');
        }

        if ($partition['destructiveHeld'] !== []) {
            $output->writeln('<error>DESTRUCTIVE MIGRATIONS PENDING (held, not applied):</error>');
            foreach ($partition['destructiveHeld'] as $file) {
                $output->writeln(sprintf('  %s (%s)', $file->id, $file->targetDatabase));
                $output->writeln(sprintf('    Reason: %s', $file->destructiveReason ?? ''));
            }
            $output->writeln('');
            $output->writeln('<comment>Re-run with --i-acknowledge-destructive to apply them.</comment>');
            $output->writeln('');
        }

        if ($partition['dammed'] !== []) {
            $output->writeln('<comment>DAMMED (same database as a held migration; re-evaluated once it clears):</comment>');
            foreach ($partition['dammed'] as $entry) {
                $ownHold = '';
                if ($entry['file']->requiresDbaReason !== null) {
                    $ownHold = ' (itself RequiresDBA — will hold in its own right when re-evaluated)';
                } elseif ($entry['file']->destructiveReason !== null) {
                    $ownHold = ' (itself Destructive — subject to §11.3 acknowledgement when re-evaluated)';
                }
                $output->writeln(sprintf(
                    '  %s (%s) — behind %s%s',
                    $entry['file']->id,
                    $entry['file']->targetDatabase,
                    $entry['behind']->id,
                    $ownHold,
                ));
            }
            $output->writeln('');
        }
    }

    /**
     * Apply each pending migration in order. Emits a per-file line
     * to the console and returns FAILURE the first time apply
     * throws; remaining migrations are NOT attempted.
     *
     * @param MigrationFile[] $pending     Pending migration list
     * @param Env             $environment Resolved environment
     * @param bool            $ack         Whether `--i-acknowledge-destructive` flag was passed
     * @param Output          $output      Console output
     *
     * @return int Symfony exit code
     */
    private function applyAll(array $pending, Env $environment, bool $ack, Output $output): int
    {
        $applied = [];
        try {
            foreach ($pending as $file) {
                $appliedBy = $this->buildAppliedBy($environment, $file, $ack);
                try {
                    if ($file->destructiveReason !== null) {
                        $snapshotPath = $this->backupService->snapshot($file, $environment);
                        if ($snapshotPath !== null) {
                            $output->writeln(sprintf('  <info>backup</info> %s -> %s', $file->id, $snapshotPath));
                        }
                    }

                    $appliedMigration = $file->id === MigrationApplyService::BOOTSTRAP_MIGRATION_ID ? $this->applyService->applyBootstrap($file, $appliedBy) : $this->applyService->apply($file, $appliedBy);
                    $applied[]        = $appliedMigration;
                    $output->writeln(sprintf(
                        '  <info>applied</info> %s (%s) in %d ms',
                        $appliedMigration->id,
                        $appliedMigration->targetDatabase,
                        $appliedMigration->durationMs,
                    ));
                } catch (Throwable $exception) {
                    $output->writeln(sprintf(
                        '  <error>FAILED</error> %s (%s): %s',
                        $file->id,
                        $file->targetDatabase,
                        $this->renderExceptionDetail($exception),
                    ));
                    return Command::FAILURE;
                }
            }
            return Command::SUCCESS;
        } finally {
            $this->notificationService->notifyApplied($environment, $applied);
        }
    }

    /**
     * Produce the `applied_by` value for a given migration. CI runs
     * are detected via the `CI` / `GITLAB_CI` env vars and stamped
     * `ci:<gitlab-user-login>`; local runs stamp `cli:<unix-username>`
     * (see `resolveActorIdentity()` / `isCiRun()` on the base class).
     * Destructive applies append the `+destructive-ack` suffix
     * per §11.3.
     *
     * @param Env           $environment Resolved environment
     * @param MigrationFile $file        Migration about to apply
     * @param bool          $ack         Whether `--i-acknowledge-destructive` was passed
     *
     * @return string Actor identifier matching the §3 vocabulary
     */
    private function buildAppliedBy(Env $environment, MigrationFile $file, bool $ack): string
    {
        $identity = $this->resolveActorIdentity();
        $base     = $this->isCiRun() ? 'ci:' . $identity : 'cli:' . $identity;

        $shouldStampAck = $ack
        && $file->destructiveReason !== null
        && ($environment === Env::STAGING || $environment === Env::PROD);
        return $shouldStampAck ? $base . '+destructive-ack' : $base;
    }

    /**
     * Read a string option, normalising empty strings to null.
     *
     * @param Input  $input Symfony console input
     * @param string $name  Option name
     *
     * @return ?string Option value, or null when absent / empty
     */
    private function stringOption(Input $input, string $name): ?string
    {
        $raw = $input->getOption($name);
        return is_string($raw) && $raw !== '' ? $raw : null;
    }

    /**
     * Surface the deepest useful detail from an apply-time
     * exception. `DtoException` carries a `PdoError` DTO that
     * includes the actual driver message (e.g. "Table 'X.Y'
     * already exists") which is far more useful than the
     * generic "The query execution failed." outer wrapper.
     *
     * @param Throwable $exception The caught exception
     *
     * @return string One-line detail for the operator
     */
    private function renderExceptionDetail(Throwable $exception): string
    {
        $message = $exception->getMessage();
        if ($exception instanceof DtoException) {
            $dto = $exception->getDto();
            if ($dto instanceof PdoError && $dto->driverMessage !== null && $dto->driverMessage !== '') {
                $message .= ' (' . $dto->driverMessage . ')';
            }
        }
        return $message;
    }

    /**
     * Print the pre-apply banner — resolved target host / port,
     * environment, and a per-database breakdown of what's about to
     * apply. Always prints (audit trail), independent of whether
     * the run is interactive.
     *
     * @param Output          $output      Console output
     * @param Env             $environment Resolved environment
     * @param MigrationFile[] $pending     Pending migration list
     *
     * @return void
     */
    private function renderApplyBanner(Output $output, Env $environment, array $pending): void
    {
        $host = (string) getenv('MYSQL_WRITE_HOST');
        $port = (string) getenv('MYSQL_WRITE_PORT');

        $perDatabase = [];
        foreach ($pending as $file) {
            $perDatabase[$file->targetDatabase] = ($perDatabase[$file->targetDatabase] ?? 0) + 1;
        }
        ksort($perDatabase);

        $output->writeln('');
        $output->writeln(sprintf(
            '<info>Target:</info> %s:%s  (ENV=%s)',
            $host === '' ? '<unset>' : $host,
            $port === '' ? '<unset>' : $port,
            $environment->value,
        ));
        $output->writeln(sprintf(
            '<info>About to apply:</info> %d migration(s) across %d database(s)',
            count($pending),
            count($perDatabase),
        ));
        foreach ($perDatabase as $database => $count) {
            $output->writeln(sprintf('  - %-12s × %d', $database, $count));
        }
        foreach ($pending as $file) {
            $marker = $file->destructiveReason !== null ? '🔥 ' : '';
            $output->writeln(sprintf('    %s%s (%s)', $marker, $file->id, $file->targetDatabase));
        }
        $output->writeln('');
    }

    /**
     * Confirm the apply with the operator. `--yes` skips the
     * prompt; non-interactive runs without `--yes` are refused so
     * a misconfigured CI run can't silently mutate state.
     *
     * @param Input  $input  Symfony console input
     * @param Output $output Symfony console output
     *
     * @return bool True when the runner is cleared to proceed
     */
    private function confirmApply(Input $input, Output $output): bool
    {
        if ((bool) $input->getOption('yes')) {
            $output->writeln('<comment>--yes: skipping confirmation.</comment>');
            return true;
        }

        if (! $input->isInteractive()) {
            $output->writeln('<error>Refusing to apply in a non-interactive run without --yes.</error>');
            return false;
        }

        $helper = $this->getHelper('question');
        assert($helper instanceof QuestionHelper);
        $question = new ConfirmationQuestion('Proceed? [y/N] ', false);
        return (bool) $helper->ask($input, $output, $question);
    }
}
