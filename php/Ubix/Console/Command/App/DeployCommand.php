<?php

declare(strict_types=1);

namespace Ubix\Console\Command\App;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use ValueError;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Enum\Env;
use Ubix\Service\ProcessService;

/**
 * Command to deploy the apps in the project.
 *
 * @see \Ubix\Tests\Console\Command\App\DeployCommandTest PHPUnit test case
 */
final class DeployCommand extends Command
{
    private const APPS_PATH = __DIR__ . '/../../../../../';

    /**
     * Constructor.
     *
     * @param Logger         $logger         Logger instance
     * @param ProcessService $processService Process service instance
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
    protected function execute(Input $input, Output $output): int
    {
        $envArg = $input->getArgument('env');
        assert(is_string($envArg));

        try {
            $env = Env::from($envArg);
        } catch (ValueError $e) {
            $output->writeln('<error>Invalid environment specified.</error>');
            return Command::FAILURE;
        }

        $deployCmd = self::APPS_PATH . 'bin/deploy.sh ' . $env->value;

        $output->writeln('Building the project for environment: ' . $env->value);

        // Execute the deploy command
        $result = $this->processService->executeAsSubprocess($deployCmd);
        if ($result->exitCode !== 0) {
            $output->writeln('<error>Command failed: ' . $deployCmd . '</error>');
            $output->writeln('<error>Exit Code: ' . $result->exitCode . '</error>');
            $output->writeln('<error>STDERR: ' . $result->stderrOutput . '</error>');
            return Command::FAILURE;
        }
        $output->writeln('<info>Command succeeded: ' . $deployCmd . '</info>');

        return Command::SUCCESS;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Deploys all of the projects.')->setHelp(
            <<<'HELP'
This command allows you to deploy all the projects.

Usage:
  neptune app:deploy <env>
HELP,
        )->addArgument(
            'env',
            InputArgument::REQUIRED,
            'The environment to deploy to (e.g., staging, production)',
        );
    }
}
