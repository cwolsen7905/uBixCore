<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Code;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Service\ProcessService;

/**
 * Command to invoke machine code review
 *
 * @see \Ubix\Tests\Console\Command\Code\LocCommandTest PHPUnit test case
 */
final class LocCommand extends Command
{
    /**
     * Constructor
     *
     * @param Logger         $logger         Logger
     * @param ProcessService $processService Process service to run CLI tools
     *
     * @return void
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private ProcessService $processService,
    ) {
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Generate lines of code (LOC) report')->setHelp(
            <<<'HELP'
This command allows you to generate a report of the lines of code (LOC) in the current branch.

Usage:
  neptune code:loc
HELP,
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int  // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $input is required but not used
    {
        $this->displayAsciiTrident($output);

        $dir = realpath(__DIR__ . '/../../../../../') ?: '.';

        $res = $this->processService->executeAsSubprocess('pahp ' . $dir . '/bin/phploc.phar ' . $dir . '/php ' . $dir . '/app');

        if ($res->exitCode !== 0) {
            if (method_exists($output, 'getErrorOutput')) {
                $stdErr = $output->getErrorOutput();
                assert($stdErr instanceof Output);
                $stdErr->writeln('Failed to generate LOC report: ' . $res->stderrOutput);
            } else {
                $output->writeln('Failed to generate LOC report: ' . $res->stderrOutput);
            }
            return Command::FAILURE;
        } else {
            $output->writeln('Report generated successfully.');
            $output->writeln($res->stdoutOutput);
        }

        return Command::SUCCESS;
    }
}
