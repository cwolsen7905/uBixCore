<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Database;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Console\Command\AbstractMigrationCommand as MigrationCommand;
use Ubix\Enum\Env;
use Ubix\Service\DatabaseEnvironment\DatabaseEnvironmentService;
use Ubix\Service\Migration\MigrationConnectionTargetService;

/**
 * Change a database's environment label on purpose (`SYSTEMS.Database_Environment`)
 *
 * For a server whose data was cloned from another environment -- staging
 * rebuilt from production arrives labelled `prod`, and `migrate:up
 * --target=staging` then refuses it. Connects like the migrate commands
 * (`--target` and the same credentials, which need DDL rights), and requires
 * `--from` to name the label that is there now: the command will not guess.
 *
 * Relabelling clears the "sanitised" stamp. Sanitise the data (the host's
 * reset or scrub) after relabelling, not before.
 *
 * @see \Ubix\Tests\Console\Command\Database\LabelCommandTest PHPUnit test case
 */
final class LabelCommand extends MigrationCommand
{
    /**
     * Constructor
     *
     * @param Logger                           $logger                     Logger
     * @param MigrationConnectionTargetService $connectionTargetService    Applies `--target`
     * @param DatabaseEnvironmentService       $databaseEnvironmentService Reads and writes the label
     */
    public function __construct(
        Logger $logger,
        MigrationConnectionTargetService $connectionTargetService,
        private DatabaseEnvironmentService $databaseEnvironmentService,
    ) {
        parent::__construct($logger, $connectionTargetService);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Change the database\'s environment label (SYSTEMS.Database_Environment)')
            ->setHelp('Relabels the database --target connects to as --target, after checking that its current label is --from. For a server cloned from another environment. Clears the sanitised stamp. Needs the migration (DDL) credentials.')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'The label the database carries now (dev, staging, prod, ...)');
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

        $fromRaw = $input->getOption('from');
        $from    = is_string($fromRaw) && $fromRaw !== '' ? Env::tryFrom($fromRaw) : null;
        if ($from === null) {
            $output->writeln('<error>--from is required: name the label the database carries now.</error>');

            return Command::FAILURE;
        }

        $to = $this->resolveRuntimeEnvironment();

        try {
            $this->databaseEnvironmentService->relabel($from, $to, $this->resolveActorIdentity());
        } catch (InvalidArgumentException $e) {
            $output->writeln('<error>' . $e->getMessage() . '. Nothing changed.</error>');

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>Relabelled `%s` → `%s`. Its data is now unsanitised until a reset or scrub runs.</info>', $from->value, $to->value));

        return Command::SUCCESS;
    }
}
