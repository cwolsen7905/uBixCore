<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Migrate;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Console\Command\AbstractMigrationCommand as MigrationCommand;
use Ubix\Service\Migration\MigrationConnectionTargetService;
use Ubix\Service\Migration\SchemaDiffService;

/**
 * `migrate:diff` — compare every Ubix-consumed database's live
 * schema against the checked-in `sql/<DB>.sql` baseline dump and
 * report drift.
 *
 * Per `docs/standards/migrations.md` §9 (reference-dump mode).
 * v1 ships the reference-dump path only; `--mode=replay`
 * (rebuild scratch DB from baseline + migration history, diff
 * against that) remains v2 work.
 *
 * **Advisory only under the frozen-baseline policy** (locked
 * 2026-05-14 — `cutover-runbook.md` §3.6 v1.5): once any
 * migration applies, "extra in live" rows include the entire
 * migration history's effect, so the output is structurally
 * noisy. Useful for human "did anything unexpected land?"
 * inspection; not safe to act on as a CI gate until replay-mode
 * ships.
 *
 * Exit codes:
 *
 * - `0` when every considered database matches its baseline dump.
 * - `1` when any database has drift OR a comparison error (missing
 *   reference file, mariadb-dump failure). Error results are surfaced
 *   inline so the operator sees what failed without re-running.
 *
 * @see \Ubix\Tests\Console\Command\Migrate\DiffCommandTest PHPUnit test case
 */
final class DiffCommand extends MigrationCommand
{
    /**
     * Constructor
     *
     * @param Logger                           $logger                  Logger
     * @param MigrationConnectionTargetService $connectionTargetService Resolves and applies --target / --username
     * @param SchemaDiffService                $diffService             Schema-drift service
     */
    public function __construct(
        Logger $logger,
        MigrationConnectionTargetService $connectionTargetService,
        private SchemaDiffService $diffService,
    ) {
        parent::__construct($logger, $connectionTargetService);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Compare each live database schema against its checked-in `sql/<DB>.sql` reference dump')
            ->setHelp('Diffs the live cluster schema (via `mariadb-dump --no-data`) against the checked-in `sql/<DB>.sql` for each Ubix-consumed database. Use `--database` to scope to one schema. Exits non-zero on any drift or comparison error.')
            ->addOption('database', null, InputOption::VALUE_REQUIRED, 'Restrict to one target database');
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

        $results  = $this->diffService->diffAll($databaseFilter);
        $exitCode = Command::SUCCESS;

        foreach ($results as $result) {
            if ($result->errorMessage !== null) {
                $output->writeln(sprintf(
                    '<error>%s</error>: %s',
                    $result->database,
                    $result->errorMessage,
                ));
                $exitCode = Command::FAILURE;
                continue;
            }

            if (! $result->hasDrift) {
                $output->writeln(sprintf('<info>%s</info>: clean', $result->database));
                continue;
            }

            $exitCode = Command::FAILURE;
            $output->writeln(sprintf('<error>%s</error>: drift', $result->database));
            $this->renderDiffLines($output, '+', $result->extraInLive, 'fg=red');
            $this->renderDiffLines($output, '-', $result->missingFromLive, 'fg=cyan');
        }

        return $exitCode;
    }

    /**
     * Render each line in `$lines` indented with the given marker
     * and console colour tag.
     *
     * @param Output   $output Console output
     * @param string   $marker Single-character prefix — `+` for "extra in live", `-` for "missing from live"
     * @param string[] $lines  Diff lines
     * @param string   $colour Symfony console colour tag value (e.g. `fg=red`)
     *
     * @return void
     */
    private function renderDiffLines(Output $output, string $marker, array $lines, string $colour): void
    {
        foreach ($lines as $line) {
            $output->writeln(sprintf('  <%s>%s %s</>', $colour, $marker, $line));
        }
    }
}
