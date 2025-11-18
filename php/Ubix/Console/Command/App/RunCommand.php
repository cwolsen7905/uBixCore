<?php

declare(strict_types=1);

namespace Ubix\Console\Command\App;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Service\ProcessService;

/**
 * Command to run the application.
 *
 * @see \Ubix\Tests\Console\Command\App\RunCommandTest PHPUnit test case
 */
final class RunCommand extends Command
{
    private string $appPath = __DIR__ . '/../../../../app/';

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
        // Call the parent constructor with the command name
        // This is necessary to ensure the command is registered correctly
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If the app_name argument is not a string
     */
    protected function execute(Input $input, Output $output): int
    {
        // Set the APP_NAME environment variable
        // This is used by the application to determine which app to run
        // It can be used in the application logic to load specific configurations or resources
        $appName = $input->getArgument('app_name');
        if (!is_string($appName)) {
            throw new Exception('Argument "app_name" must be a string.');
        }

        putenv('APP_NAME=' . $appName);

        // Determine if this is a SSvelteKit or PHP application
        if (file_exists($this->appPath . $appName . '/svelte.config.js')) {
            // If SvelteKit configuration file exists, run the SvelteKit application
            $result = $this->processService->executeAsSubprocess('npm install --prefix ' . escapeshellarg($this->appPath . $appName));
            if ($result->exitCode !== 0) {
                $output->writeln('<error>Failed to install npm dependencies for SvelteKit app: ' . $appName . '</error>');
                return Command::FAILURE;
            }
            return Command::SUCCESS;
        }


        // Get the (int) port and (string) host from the input options and ensure they are valid
        if (!is_numeric($input->getOption('port'))) {
            $output->writeln('<error>Invalid port specified. Please provide a valid port number.</error>');
            return Command::FAILURE;
        }
        if (!is_string($input->getOption('host'))) {
            $output->writeln('<error>Invalid host specified. Please provide a valid host name.</error>');
            return Command::FAILURE;
        }

        $port = (int)$input->getOption('port');
        $host = (string)$input->getOption('host');
        $host = '0.0.0.0'; // TEMPORARY: this was needed to run app:run on Andrew's old sandbox
        $output->writeln('Running the project on host: ' . $host . ', port: ' . $port);

        $result = $this->processService->executeAsSubprocess('php -S ' . escapeshellarg($host) . ':' . $port . ' -t public');
        if ($result->exitCode !== 0) {
            $output->writeln('<error>Failed to run the PHP application.</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Runs the project.')->setHelp(
            <<<'HELP'
This command allows you to run the project.

Usage:
  neptune run
HELP,
        )->addOption(
            'port',
            null,
            InputOption::VALUE_REQUIRED,
            'The port to use for the application.',
            8888,
        )->addOption(
            'host',
            null,
            InputOption::VALUE_REQUIRED,
            'The host to bind the application to.',
            'localhost',
        )->addArgument(
            'app_name',
            InputArgument::REQUIRED,
            'The name of the app',
        );
    }
}
