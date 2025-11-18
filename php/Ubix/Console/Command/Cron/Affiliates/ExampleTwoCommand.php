<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Cron\Affiliates;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\Cron\AbstractCronCommand as CronCommand;

/**
 * Cron that is the second affiliates example
 *
 * @see \Ubix\Tests\Console\Command\Cron\Affiliates\ExampleTwoCommandTest PHPUnit test case
 */
final class ExampleTwoCommand extends CronCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('The second affiliates example')
            ->setHelp(<<<'HELP'
Help for cron that is the second affiliates example
HELP);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $input is required but not used
    {
        $this->displayAsciiTrident($output);

        $output->writeln('AFFILIATES EXAMPLE TWO');

        return SymfonyCommand::SUCCESS;
    }
}
