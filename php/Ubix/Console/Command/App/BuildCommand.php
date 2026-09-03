<?php

declare(strict_types=1);

namespace Ubix\Console\Command\App;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Enum\Env;
use Ubix\Service\ProcessService;
use Ubix\Service\ProjectRootService;
use ValueError;

/**
 * Command to build the project.
 *
 * @see \Ubix\Tests\Console\Command\App\BuildCommandTest PHPUnit test case
 */
final class BuildCommand extends Command
{
    /**
     * Constructor.
     *
     * @param Logger             $logger         Logger instance
     * @param ProcessService     $processService Process service instance
     * @param ProjectRootService $projectRoot    Resolves paths in the host project
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private ProcessService $processService,
        private ProjectRootService $projectRoot,
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
        foreach ($this->getBuildCommands() as $command) {
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

    /**
     * Get the build commands, in execution order
     *
     * @return string[]
     */
    private function getBuildCommands(): array
    {
        $root = $this->projectRoot->getRoot();

        return [
            'docker image rm -f registry.lan.vsmedia.net/k8s/baseimages/nginx-php8-fpm-memcache:latest || true',
            'docker build -f ' . $root . '/Dockerfile_Sandbox -t project-neptune:sandbox ' . $root . '/',
            'docker save project-neptune:sandbox > /tmp/project-neptune_sandbox.tar',
            'microk8s ctr image import /tmp/project-neptune_sandbox.tar',
            'microk8s kubectl get namespace webservices-sandbox >/dev/null 2>&1 || microk8s kubectl create namespace webservices-sandbox',
        ];
    }
}
