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
use Ubix\Enum\Git\GitFileStatus;
use Ubix\Service\GitService;
use Ubix\Service\MachineCodeReviewService;

/**
 * Command to invoke machine code review
 *
 * @see \Ubix\Tests\Console\Command\Code\CommitCommandTest PHPUnit test case
 */
final class CommitCommand extends Command
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
        $this->setDescription('Commits changes to current branch.')->setHelp(
            <<<'HELP'
This command allows you to commit changes to the current branch.

Usage:
  neptune code:commit
HELP,
        )->addOption('allowViolations', null, InputOption::VALUE_NONE, 'Allow to commit with violations. This will bypass the machine code review check. Use with caution.');
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

        /**
         * @var array{staged: array<array{status: string, file: string, originalFile: string}>, unstaged: array<array{status: string, file: string, originalFile: string}>, untracked: array<array{status: string, file: string, originalFile: string}>} $files
         */
        $files = $this->gitService->getFiles();

        $output->writeln('Changes to be committed:');

        foreach ($files['staged'] as $fileData) {
            $statusName   = GitFileStatus::tryFrom($fileData['status'])->name ?? 'UNKNOWN' . ':';
            $statusPadded = str_pad($statusName, 12);
            if ($fileData['status'] === 'R') {
                $output->writeln('        ' . sprintf(
                    '<fg=green>%s</> %s (from %s)',
                    $statusPadded,
                    $fileData['file'],
                    $fileData['originalFile'],
                ));
            } else {
                $output->writeln('        ' . sprintf(
                    '<fg=green>%s</> %s',
                    $statusPadded,
                    $fileData['file'],
                ));
            }
        }


        $output->writeln(PHP_EOL . 'Changes not staged for commit:');

        foreach ($files['unstaged'] as $fileData) {
            $statusName   = GitFileStatus::tryFrom($fileData['status'])->name ?? 'UNKNOWN' . ':';
            $statusPadded = str_pad($statusName, 12);
            $output->writeln('        ' . sprintf(
                '<fg=red>%s</> %s',
                $statusPadded,
                $fileData['file'],
            ));
            $output->write('Stage this file? (y/n): ');

            $userInput = fgets(STDIN) ?: '';
            if (strtolower(trim($userInput)) === 'y') {
                try {
                    $this->gitService->add($fileData['file']);
                } catch (Exception $e) {
                    $output->writeln('<error>Failed to stage ' . $fileData['file'] . ': ' . $e->getMessage() . '</error>');
                    continue;
                }
                $output->writeln('<info>Staged ' . $fileData['file'] . '</info>');
            } else {
                $output->writeln('<comment>Skipped ' . $fileData['file'] . '</comment>');
            }
        }


        $output->writeln(PHP_EOL . 'Untracked files:');

        foreach ($files['untracked'] as $fileData) {
            $statusName   = GitFileStatus::tryFrom($fileData['status'])->name ?? 'UNKNOWN' . ':';
            $statusPadded = str_pad($statusName, 12);
            $output->writeln('        ' . sprintf(
                '<fg=red>%s</> %s',
                $statusPadded,
                $fileData['file'],
            ));
            $output->write('Add this file? (y/n): ');

            $userInput = fgets(STDIN) ?: '';

            if (strtolower(trim($userInput)) === 'y') {
                try {
                    $this->gitService->add($fileData['file']);
                } catch (Exception $e) {
                    $output->writeln('<error>Failed to stage ' . $fileData['file'] . ': ' . $e->getMessage() . '</error>');
                    continue;
                }
                $output->writeln('<info>Staged ' . $fileData['file'] . '</info>');
            } else {
                $output->writeln('<comment>Skipped ' . $fileData['file'] . '</comment>');
            }
        }

        $output->writeln(PHP_EOL . 'Final staged changes:');

        /**
         * @var array{staged: array<array{status: string, file: string, originalFile: string}>, unstaged: array<array{status: string, file: string, originalFile: string}>, untracked: array<array{status: string, file: string, originalFile: string}>} $files
         */
        $files = $this->gitService->getFiles();

        foreach ($files['staged'] as $fileData) {
            $statusName   = GitFileStatus::tryFrom($fileData['status'])->name ?? 'UNKNOWN' . ':';
            $statusPadded = str_pad($statusName, 12);
            if ($fileData['status'] === 'R') {
                $output->writeln('        ' . sprintf(
                    '<fg=green>%s</> %s (from %s)',
                    $statusPadded,
                    $fileData['file'],
                    $fileData['originalFile'],
                ));
            } else {
                $output->writeln('        ' . sprintf(
                    '<fg=green>%s</> %s',
                    $statusPadded,
                    $fileData['file'],
                ));
            }
        }

        // Ask users for commit title must be at least 5 characters
        $commitTitle = '';
        while (true) {
            $output->write(PHP_EOL . 'Enter commit title (at least 5 characters): ');
            $commitTitle = fgets(STDIN) ?: '';
            $commitTitle = trim($commitTitle);
            if (strlen($commitTitle) >= 5) {
                break;
            }
        }

        // Ask users for commit message (optional) that can be multiple lines and ask them to review it or re-enter
        $commitMessage = '';
        do {
            $output->writeln(PHP_EOL . 'Enter commit message (optional, end with a single dot on a line): ');
            $lines = [];
            while (true) {
                $line = fgets(STDIN) ?: '';
                $line = trim($line);
                if ($line === '.') {
                    break;
                }
                $lines[] = $line;
            }
            $commitMessage = implode(PHP_EOL, $lines);
            $output->writeln(PHP_EOL . 'Review commit message:');
            $output->writeln($commitMessage);
            $output->write('Is this correct? (y/n): ');
            $userInput = fgets(STDIN) ?: '';
            if (strtolower(trim($userInput)) === 'y') {
                break;
            }
        } while (true);

        if ($input->getOption('allowViolations') && $review->getViolationsCount() > 0) {
            $bufferedOutput = new BufferedOutput();
            $this->machineCodeReviewService->displayLineByLine($review, $bufferedOutput);
            $commitMessage .= PHP_EOL . PHP_EOL . '[WARNING] Commit made with code violations:' . PHP_EOL . $bufferedOutput->fetch();
        }

        try {
            $this->gitService->commit($commitMessage ? [$commitTitle, $commitMessage] : $commitTitle);
        } catch (Exception $e) {
            $output->writeln('<error>Failed to commit changes: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Changes committed successfully.</info>');

        // Ask user if they want to push changes now
        $output->write('Do you want to push the changes now? (y/n): ');
        $userInput = fgets(STDIN) ?: '';
        if (strtolower(trim($userInput)) === 'y') {
            try {
                $this->gitService->push();
            } catch (Exception $e) {
                $output->writeln('<error>Failed to push changes: ' . $e->getMessage() . '</error>');
                return Command::FAILURE;
            }
            $output->writeln('<info>Changes pushed successfully.</info>');
        } else {
            $output->writeln('<comment>Skipped pushing changes.</comment>');
        }

        return Command::SUCCESS;
    }
}
