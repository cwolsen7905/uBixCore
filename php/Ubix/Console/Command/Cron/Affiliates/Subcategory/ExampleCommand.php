<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Cron\Affiliates\Subcategory;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\Cron\AbstractCronCommand as CronCommand;

/**
 * Cron that is the first affiliates example
 *
 * @see \Ubix\Tests\Console\Command\Cron\Affiliates\Subcategory\ExampleCommandTest PHPUnit test case
 */
final class ExampleCommand extends CronCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('The affiliates subcategory example')
            ->setHelp(<<<'HELP'
Help for cron that is the affiliates subcategory example
HELP);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $input is required but not used
    {
        $this->displayAsciiTrident($output);

        $output->writeln('AFFILIATES SUBCATEGORY EXAMPLE');

        return SymfonyCommand::SUCCESS;
    }
}
