<?php

declare(strict_types=1);

namespace Ubix\Console\Command\K8s;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use ValueError;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Enum\Env;
use Ubix\Service\ProcessService;

/**
 * Command to build the project.
 *
 * @see \Ubix\Tests\Console\Command\K8s\DeployMariadbCommandTest PHPUnit test case
 */
final class DeployMariadbCommand extends Command
{
    private const DEPLOY_COMMANDS = [
        'dev'     => [],
        'prod'    => [],
        'sandbox' => [
            'microk8s kubectl get storageclass | grep microk8s-hostpath >/dev/null 2>&1 || microk8s enable hostpath-storage',
            'microk8s kubectl apply -f ./config/devops/sandbox-mariadb-namespace.yaml',
            'microk8s kubectl apply -f ./config/devops/sandbox-mariadb-pvc.yaml',
            'microk8s kubectl apply -f ./config/devops/sandbox-mariadb-secret.yaml',
            'microk8s kubectl apply -f ./config/devops/sandbox-mariadb.yaml',
            'microk8s kubectl apply -f ./config/devops/sandbox-mariadb-service.yaml',
        ],
        'staging' => [],
    ];

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

        $output->writeln('Building the project for environment: ' . $env->value);
        // Execute the build commands
        foreach (self::DEPLOY_COMMANDS[$env->value] as $command) {
            $result = $this->processService->executeAsSubprocess($command);
            if ($result->exitCode !== 0) {
                $output->writeln('<error>Command failed: ' . $command . '</error>');
                $output->writeln('<error>Exit Code: ' . $result->exitCode . '</error>');
                $output->writeln('<error>STDERR: ' . $result->stderrOutput . '</error>');
                return Command::FAILURE;
            }
            $output->writeln('<info>Command succeeded: ' . $command . '</info>');
        }

        return Command::SUCCESS;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Builds the project.')->setHelp(
            <<<'HELP'
This command allows you to build the project.

Usage:
  neptune app:build <env>
HELP,
        )->addArgument(
            'env',
            InputArgument::REQUIRED,
            'The environment to build',
        );
    }
}
