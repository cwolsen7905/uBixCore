<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Migrate;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Console\Command\AbstractMigrationCommand as MigrationCommand;
use Ubix\Service\Migration\MigrationConnectionTargetService;
use Ubix\Service\Migration\MigrationStatusService;

/**
 * `migrate:status` — render every on-disk migration alongside its
 * `SYSTEMS.Schema_Migrations` state.
 *
 * Per `docs/standards/migrations.md` §4.2 + §7. With `--verify` the
 * command recomputes each applied file's SHA-256 against the
 * recorded checksum; any drift surfaces as red `DRIFT` and the
 * command exits non-zero. CI runs `migrate:status --verify` per
 * §10's tiered policy so unauthorised after-apply edits fail the
 * deploy.
 *
 * @see \Ubix\Tests\Console\Command\Migrate\StatusCommandTest PHPUnit test case
 */
final class StatusCommand extends MigrationCommand
{
    /**
     * Constructor
     *
     * @param Logger                           $logger                  Logger
     * @param MigrationConnectionTargetService $connectionTargetService Resolves and applies --target / --username
     * @param MigrationStatusService           $statusService           Cross-references on-disk files against the tracker
     */
    public function __construct(
        Logger $logger,
        MigrationConnectionTargetService $connectionTargetService,
        private MigrationStatusService $statusService,
    ) {
        parent::__construct($logger, $connectionTargetService);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('List every on-disk migration with its applied / pending state')
            ->setHelp('Renders one row per file in sql/migrations/ joined to its SYSTEMS.Schema_Migrations row when one exists. Use `--database` to filter to a single target schema. Use `--verify` to fail the command on any after-apply edit (drift).')
            ->addOption('database', null, InputOption::VALUE_REQUIRED, 'Filter to one target database (e.g. `VSCASH`, `SYSTEMS`)')
            ->addOption('verify', null, InputOption::VALUE_NONE, 'Recompute each applied file\'s checksum and exit non-zero on any drift')
            ->addOption('destructive-pending', null, InputOption::VALUE_NONE, 'Machine-readable mode: print only `DESTRUCTIVE_MIGRATION=true|false` for whether any PENDING (unapplied) migration is destructive, then exit. Unlike the git-diff-based CI advisory, a destructive migration pending from an earlier push is reported here even though it is not in the latest git diff.');
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

        $databaseFilterRaw = $input->getOption('database');
        $databaseFilter    = is_string($databaseFilterRaw) && $databaseFilterRaw !== '' ? $databaseFilterRaw : null;
        $verify            = (bool) $input->getOption('verify');

        $entries = $this->statusService->getStatus($databaseFilter);

        if ((bool) $input->getOption('destructive-pending')) {
            $hasPendingDestructive = false;
            foreach ($entries as $entry) {
                if (! $entry->isApplied && $entry->isDestructive) {
                    $hasPendingDestructive = true;
                    break;
                }
            }
            $output->writeln('DESTRUCTIVE_MIGRATION=' . ($hasPendingDestructive ? 'true' : 'false'));
            return Command::SUCCESS;
        }

        if ($entries === []) {
            $output->writeln('<comment>No migrations on disk.</comment>');
            return Command::SUCCESS;
        }

        $hasDrift = false;
        $rows     = [];
        foreach ($entries as $entry) {
            $statusCell      = $entry->isApplied ? '<info>applied</info>' : '<comment>pending</comment>';
            $destructiveCell = $entry->isDestructive ? '<fg=red>🔥</>' : '';
            $checksumCell    = $this->checksumCell($entry->isApplied, $entry->checksumMatches);
            if ($entry->isApplied && ! $entry->checksumMatches) {
                $hasDrift = true;
            }
            $rows[] = [
                $entry->id,
                $entry->targetDatabase,
                $statusCell,
                $destructiveCell,
                $checksumCell,
                $entry->appliedAt?->format('Y-m-d H:i:s') ?? '—',
                $entry->appliedBy ?? '—',
                $entry->description,
            ];
        }

        $table = new Table($output);
        $table->setHeaders(['ID', 'Database', 'Status', '🔥', 'Checksum', 'Applied at', 'Applied by', 'Description']);
        $table->setRows($rows);
        $table->render();

        if ($verify && $hasDrift) {
            $output->writeln('');
            $output->writeln('<error>One or more applied migrations have drifted from their on-disk file. Forward-fix only — do not edit applied migrations in place.</error>');
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    /**
     * Render the per-row checksum cell.
     *
     * @param bool $isApplied       Whether the migration has a `Schema_Migrations` row
     * @param bool $checksumMatches Whether the on-disk checksum still matches the recorded one
     *
     * @return string Console-formatted cell content
     */
    private function checksumCell(bool $isApplied, bool $checksumMatches): string
    {
        if (! $isApplied) {
            return '—';
        }
        return $checksumMatches ? '<info>OK</info>' : '<fg=red>DRIFT</>';
    }
}
