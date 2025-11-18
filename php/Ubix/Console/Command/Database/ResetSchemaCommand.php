<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Database;

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
 * @see \Ubix\Tests\Console\Command\Database\ResetSchemaCommandTest PHPUnit test case
 */
final class ResetSchemaCommand extends Command
{
    private const SQL_PATH = __DIR__ . '/../../../../../sql/';

    private const MYSQL_DATABASES = [
        'dev'     => [],
        'prod'    => [],
        'sandbox' => [
            'ADSERVER',
            'BILLING',
            'CHAT_SYSTEM_LOG',
            'CHAT_SYSTEM',
            'FLIRT_REWARDS',
            'flirt4free',
            'MAILINGS',
            'MESSAGING',
            'ntl_db',
            'STUDIOS_STATS',
            'STUDIOS',
            'SYSTEMS',
            'VSCASH_STATS',
            'VSCASH',
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

        $mysqlHost     = getenv('TEST_MYSQL_WRITE_HOST');
        $mysqlUser     = getenv('TEST_MYSQL_WRITE_USERNAME');
        $mysqlPassword = getenv('TEST_MYSQL_WRITE_PASSWORD');
        $mysqlPort     = getenv('TEST_MYSQL_WRITE_PORT');

        $output->writeln('Rebuilding the database schema for environment: ' . $env->value);

        // Execute the build commands
        foreach (self::MYSQL_DATABASES[$env->value] as $database) {
            // Create database if it does not exist
            $command = 'mysql --user=' . $mysqlUser . ' --password=' . $mysqlPassword . ' --port=' . $mysqlPort . ' --host=' . $mysqlHost . " -e 'CREATE DATABASE IF NOT EXISTS " . $database . "'";
            $result  = $this->processService->executeAsSubprocess($command);
            if ($result->exitCode !== 0) {
                $output->writeln('<error>Command failed: ' . $command . '</error>');
                $output->writeln('<error>Exit Code: ' . $result->exitCode . '</error>');
                $output->writeln('<error>STDERR: ' . $result->stderrOutput . '</error>');
                return Command::FAILURE;
            }

            $command = 'mysql --user=' . $mysqlUser . ' --password=' . $mysqlPassword . ' --port=' . $mysqlPort . ' --host=' . $mysqlHost . ' ' . $database . ' < ' . self::SQL_PATH . $database . '.sql';
            $result  = $this->processService->executeAsSubprocess($command);
            if ($result->exitCode !== 0) {
                $output->writeln('<error>Command failed: ' . $command . '</error>');
                $output->writeln('<error>Exit Code: ' . $result->exitCode . '</error>');
                $output->writeln('<error>STDERR: ' . $result->stderrOutput . '</error>');
                return Command::FAILURE;
            }
            $output->writeln('<info>Successfully rebuilt schema: ' . $database . '</info>');
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
