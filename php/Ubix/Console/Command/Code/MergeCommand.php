<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Code;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Service\GitService;
use Ubix\Service\MachineCodeReviewService;

/**
 * Command to invoke machine code review
 *
 * @see \Ubix\Tests\Console\Command\Code\MergeCommandTest PHPUnit test case
 */
final class MergeCommand extends Command
{
    /**
     * Constructor
     *
     * @param Logger                   $logger                   Logger
     * @param GitService               $gitService               Git service
     * @param MachineCodeReviewService $machineCodeReviewService Machine code review service
     *
     * @return void
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private GitService $gitService,
        private MachineCodeReviewService $machineCodeReviewService,
    ) {
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Create a merge request from the current branch to the target branch')->setHelp(
            <<<'HELP'
This command allows you to create a merge request from the current branch to the target branch.

Usage:
  neptune code:merge
HELP,
        )->addOption('allowViolations', null, InputOption::VALUE_NONE, 'Allow to create a merge request with violations. This will bypass the machine code review check. Use with caution.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int  // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $input is required but not used
    {
        // First run machine code review on ALL files
        $this->displayAsciiTrident($output);

        //
        //  Execute the machine code review
        //
        $review = $this->machineCodeReviewService->getReview(
            files:  [], // All files
            output: $output,
        );

        //
        //  Display the results of the machine code review
        //
        $this->machineCodeReviewService->displayReview(
            review: $review,
            output: $output,
            files:  [], // All files,
        );

        if (!$input->getOption('allowViolations') && $review->getViolationsCount() > 0) {
            $output->writeln(PHP_EOL . '<error>Code review found violations. Please fix them before committing.</error>');
            return Command::FAILURE;
        }

        $currentBranch = $this->gitService->getCurrentBranch();
        $output->writeln(PHP_EOL . 'Creating Merge Request for: ' . $currentBranch);

        while (true) {
            $output->writeln(PHP_EOL . 'Where branched to merge into? [dev] : ');

            $targetBranch = fgets(STDIN) ?: '';
            $targetBranch = trim($targetBranch);
            if ($targetBranch === '') {
                $targetBranch = 'dev';
            }
            if (!in_array($targetBranch, $this->gitService->getBranches(), true)) {
                $output->writeln('<error>Cannot merge into a non-existent branch.</error>');
            } else {
                break;
            }
        }


        // Ask user for a merge title must be at least 5 characters
        $mergeTitle = '';
        while (true) {
            $output->write(PHP_EOL . 'Enter merge title (at least 5 characters): ');
            $mergeTitle = fgets(STDIN) ?: '';
            $mergeTitle = trim($mergeTitle);
            if (strlen($mergeTitle) >= 5) {
                break;
            }
        }

        // Ask user for a merge message (optional) that can be multiple lines and ask them to review it or re-enter
        $mergeMessage = '';
        do {
            $output->writeln(PHP_EOL . 'Enter merge message (optional, end with a single dot on a line): ');
            $lines = [];
            while (true) {
                $line = fgets(STDIN) ?: '';
                $line = trim($line);
                if ($line === '.') {
                    break;
                }
                $lines[] = $line;
            }
            $mergeMessage = implode(PHP_EOL, $lines);
            $output->writeln(PHP_EOL . 'Review merge message:');
            $output->writeln($mergeMessage);
            $output->write('Is this correct? (y/n): ');
            $userInput = fgets(STDIN) ?: '';
            if (strtolower(trim($userInput)) === 'y') {
                break;
            }
        } while (true);

        if ($input->getOption('allowViolations') && $review->getViolationsCount() > 0) {
            $bufferedOutput = new BufferedOutput();
            $this->machineCodeReviewService->displayLineByLine($review, $bufferedOutput);
            $mergeMessage .= PHP_EOL . PHP_EOL . '[WARNING] Merge request made with code violations:' . PHP_EOL . $bufferedOutput->fetch();
        }

        try {
            $this->gitService->createMergeRequest($currentBranch, $targetBranch, $mergeTitle, $mergeMessage);
        } catch (Exception $e) {
            $output->writeln('<error>Failed to create merge request: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Merge request successful.</info>');

        return Command::SUCCESS;
    }
}
