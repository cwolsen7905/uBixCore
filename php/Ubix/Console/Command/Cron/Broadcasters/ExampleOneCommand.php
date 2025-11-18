<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Cron\Broadcasters;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\Cron\AbstractCronCommand as CronCommand;

/**
 * Cron that is the first broadcasters example
 *
 * @see \Ubix\Tests\Console\Command\Cron\Broadcasters\ExampleOneCommandTest PHPUnit test case
 */
final class ExampleOneCommand extends CronCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('The first broadcasters example')
            ->setHelp(<<<'HELP'
Help for cron that is the first broadcasters example
HELP);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $input is required but not used
    {
        $this->displayAsciiTrident($output);

        $output->writeln('BROADCASTERS EXAMPLE ONE');

        return SymfonyCommand::SUCCESS;
    }
}
