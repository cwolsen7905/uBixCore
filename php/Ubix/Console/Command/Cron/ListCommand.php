<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Cron;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;

/**
 * Command to list all crons
 *
 * @see \Ubix\Tests\Console\Command\Cron\ListCommandTest PHPUnit test case
 */
final class ListCommand extends Command
{
    private const KEY_UNCATEGORIZED = '_';

    private const SPACING_BETWEEN_NAME_AND_DESCRIPTION = '  ';

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('List all crons')
            ->setHelp(<<<'HELP'
Help for command to list all crons
HELP);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $input is required but not used
    {
        $this->displayAsciiTrident($output);

        //
        //  Load the crons
        //
        $crons = [];

        foreach ($this->getApplication()?->all() ?? [] as $command) {
            if (strpos($command->getName() ?? '', 'cron:') === 0) {
                $secondColonPosition = strpos($command->getName() ?? '', ':', strlen('cron:'));

                $category = $secondColonPosition === false ? self::KEY_UNCATEGORIZED : substr($command->getName() ?? '', strlen('cron:'), $secondColonPosition - strlen('cron:'));

                if (!array_key_exists($category, $crons)) {
                    $crons[$category] = [];
                }

                $crons[$category][] = $command;
            }
        }

        //
        //  List the crons
        //
        $maximumCronNameLength = array_reduce(
            $crons,
            function (int $carry, $commands): int {
                $maximum = 0;
                foreach ($commands as $command) {
                    $maximum = max($maximum, strlen($command->getName() ?? ''));
                }
                return max($carry, $maximum);
            },
            0,
        );

        $output->writeln('<fg=yellow>Available crons:</>');

        foreach ($crons as $category => $commands) {
            if ($category !== self::KEY_UNCATEGORIZED) {
                $output->writeln(' <fg=yellow>' . $category . '</>');
            }

            foreach ($commands as $command) {
                $output->write('  ');
                $output->write('<fg=green>' . ($command->getName() ?? '') . '</>');
                $output->write(str_repeat(' ', $maximumCronNameLength - strlen($command->getName() ?? '')));
                $output->write(self::SPACING_BETWEEN_NAME_AND_DESCRIPTION);
                $output->writeln($command->getDescription());
            }
        }

        //
        //  Return success
        //
        return SymfonyCommand::SUCCESS;
    }
}
