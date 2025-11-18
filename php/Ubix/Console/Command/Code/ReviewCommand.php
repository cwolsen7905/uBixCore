<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Code;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Service\MachineCodeReviewService;

/**
 * Command to invoke machine code review
 *
 * @see \Ubix\Tests\Console\Command\Code\ReviewCommandTest PHPUnit test case
 */
final class ReviewCommand extends Command
{
    /**
     * Constructor
     *
     * @param Logger                   $logger                   Logger
     * @param MachineCodeReviewService $machineCodeReviewService Machine code review service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private MachineCodeReviewService $machineCodeReviewService,
    ) {
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Review source files with machine code review')
            ->setHelp(<<<'HELP'
This command provides machine code review for your source code with various tools.
By default, it checks all modified files in the current branch. If you want to review specific files, you can pass them as arguments.

Usage:
  neptune code:review [<file>...] [--modified]
HELP)
            ->addOption('modified', null, InputOption::VALUE_NONE, 'Review all the modified files in the current git branch')
            ->addArgument('file', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Files to code review');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $input is required but not used
    {
        $this->displayAsciiTrident($output);

        //
        //  Determine which files should be reviewed
        //
        $files = []; // NOT_IMPLEMENTED: take input from the command's arguments/options

        //
        //  Execute the machine code review
        //
        $review = $this->machineCodeReviewService->getReview(
            files:  $files,
            output: $output,
        );

        //
        //  Display the results of the machine code review
        //
        $this->machineCodeReviewService->displayReview(
            review: $review,
            output: $output,
            files:  $files,
        );

        //
        //  Return success
        //
        return SymfonyCommand::SUCCESS;
    }
}
