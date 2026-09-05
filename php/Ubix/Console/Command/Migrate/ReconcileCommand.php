<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Migrate;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Console\Command\AbstractMigrationCommand as MigrationCommand;
use Ubix\Service\Migration\MigrationApplyService;
use Ubix\Service\Migration\MigrationConnectionTargetService;
use Ubix\Service\Migration\MigrationNotificationService;
use Ubix\Service\Migration\MigrationRunnerService;

/**
 * `migrate:reconcile <migration_id> --reason="…"` — record a
 * migration as applied without running it. Used after an emergency
 * out-of-band manual application per
 * `docs/standards/migrations.md` §8.
 *
 * The recorded row's `applied_by` is stamped `manual:<username>`
 * so dashboards can filter for the manual recovery path; the
 * file's `Description:` is augmented with the supplied reason in
 * the recorded row so reviewers don't have to chase down a
 * separate ticket to learn WHY.
 *
 * Refuses to overwrite an existing tracker row — duplicate
 * reconcile calls bail with a clear error.
 *
 * @see \Ubix\Tests\Console\Command\Migrate\ReconcileCommandTest PHPUnit test case
 */
final class ReconcileCommand extends MigrationCommand
{
    /**
     * Constructor
     *
     * @param Logger                           $logger                  Logger
     * @param MigrationConnectionTargetService $connectionTargetService Resolves and applies --target / --username
     * @param MigrationRunnerService           $runnerService           Looks up the on-disk file + checks the tracker for an existing row
     * @param MigrationApplyService            $applyService            Carries the writer; performs the row insert
     * @param MigrationNotificationService     $notificationService     Posts a #databases Slack notice after reconciling on dev / staging / prod
     */
    public function __construct(
        Logger $logger,
        MigrationConnectionTargetService $connectionTargetService,
        private MigrationRunnerService $runnerService,
        private MigrationApplyService $applyService,
        private MigrationNotificationService $notificationService,
    ) {
        parent::__construct($logger, $connectionTargetService);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Record a migration as applied without running it (manual recovery path)')
            ->setHelp('Used after an emergency out-of-band manual application. The tracker row is stamped with `applied_by = manual:<unix-username>` and the description is augmented with the `--reason` text.')
            ->addArgument('migration_id', InputArgument::REQUIRED, 'Migration ID — filename without `.sql`')
            ->addOption('reason', null, InputOption::VALUE_REQUIRED, 'Free-form rationale for the manual application; appended to the recorded description');
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

        $idArg = $input->getArgument('migration_id');
        if (! is_string($idArg) || $idArg === '') {
            $output->writeln('<error>Missing migration ID argument.</error>');
            return Command::FAILURE;
        }

        $reasonRaw = $input->getOption('reason');
        $reason    = is_string($reasonRaw) && $reasonRaw !== '' ? $reasonRaw : null;
        if ($reason === null) {
            $output->writeln('<error>--reason="…" is required so the manual application has a permanent rationale on file.</error>');
            return Command::FAILURE;
        }

        $file = $this->runnerService->getMigrationById($idArg);
        if ($file === null) {
            $output->writeln(sprintf(
                '<error>No migration file found on disk with ID `%s`. Reconcile expects the file to be checked in.</error>',
                $idArg,
            ));
            return Command::FAILURE;
        }

        if ($this->runnerService->isMigrationApplied($idArg)) {
            $output->writeln(sprintf(
                '<error>Migration `%s` already has a `Schema_Migrations` row; nothing to reconcile.</error>',
                $idArg,
            ));
            return Command::FAILURE;
        }

        $applied = $this->applyService->reconcile($file, $this->resolveActorIdentity(), $reason);
        $output->writeln(sprintf(
            '<info>Reconciled</info> %s (%s) as %s',
            $applied->id,
            $applied->targetDatabase,
            $applied->appliedBy,
        ));

        $this->notificationService->notifyReconciled($this->resolveRuntimeEnvironment(), $applied);
        return Command::SUCCESS;
    }
}
