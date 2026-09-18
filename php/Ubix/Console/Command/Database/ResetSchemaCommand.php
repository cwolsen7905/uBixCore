<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Database;

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
 * @see \Ubix\Tests\Console\Command\Database\ResetSchemaCommandTest PHPUnit test case
 */
final class ResetSchemaCommand extends Command
{
    /**
     * Constructor.
     *
     * @param Logger             $logger         Logger instance
     * @param ProcessService     $processService Process service instance
     * @param ProjectRootService $projectRoot    Resolves paths in the host project
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
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

        $mysqlHost     = getenv('TEST_MYSQL_WRITE_HOST');
        $mysqlUser     = getenv('TEST_MYSQL_WRITE_USERNAME');
        $mysqlPassword = getenv('TEST_MYSQL_WRITE_PASSWORD');
        $mysqlPort     = getenv('TEST_MYSQL_WRITE_PORT');

        $output->writeln('Rebuilding the database schema for environment: ' . $env->value);

        //
        //  The databases are the host's: every sql/<name>.sql it ships is one schema
        //  baseline. The framework names none of them. The TEST_MYSQL_WRITE_*
        //  connection is always the target (this command never touches a runtime DB).
        //
        $databases = [];
        foreach (glob($this->projectRoot->getPath('sql', '*.sql')) ?: [] as $baseline) {
            $databases[] = basename($baseline, '.sql');
        }
        if (count($databases) === 0) {
            $output->writeln('<error>No schema baselines found: expected one or more ' . $this->projectRoot->getPath('sql', '<database>.sql') . '</error>');

            return Command::FAILURE;
        }

        //
        //  Credentials go in a 0600 defaults file, never on the command line: argv is
        //  world-readable through the process list, and this command echoes the command
        //  it ran when one fails. Neither may ever carry the password.
        //
        $defaultsFile = $this->writeDefaultsFile(
            is_string($mysqlUser) ? $mysqlUser : '',
            is_string($mysqlPassword) ? $mysqlPassword : '',
            is_string($mysqlHost) ? $mysqlHost : '',
            is_string($mysqlPort) ? $mysqlPort : '',
        );
        if ($defaultsFile === null) {
            $output->writeln('<error>Could not create a temporary credentials file.</error>');

            return Command::FAILURE;
        }

        try {
            $client = $this->clientBinary();

            foreach ($databases as $database) {
                // Create database if it does not exist
                $command = sprintf(
                    '%s --defaults-extra-file=%s -e %s',
                    $client,
                    escapeshellarg($defaultsFile),
                    escapeshellarg('CREATE DATABASE IF NOT EXISTS ' . $database),
                );
                $result  = $this->processService->executeAsSubprocess($command);
                if ($result->exitCode !== 0) {
                    $output->writeln('<error>Command failed: ' . $command . '</error>');
                    $output->writeln('<error>Exit Code: ' . $result->exitCode . '</error>');
                    $output->writeln('<error>STDERR: ' . $result->stderrOutput . '</error>');
                    return Command::FAILURE;
                }

                $command = sprintf(
                    '%s --defaults-extra-file=%s %s < %s',
                    $client,
                    escapeshellarg($defaultsFile),
                    escapeshellarg($database),
                    escapeshellarg($this->projectRoot->getPath('sql', $database . '.sql')),
                );
                $result  = $this->processService->executeAsSubprocess($command);
                if ($result->exitCode !== 0) {
                    $output->writeln('<error>Command failed: ' . $command . '</error>');
                    $output->writeln('<error>Exit Code: ' . $result->exitCode . '</error>');
                    $output->writeln('<error>STDERR: ' . $result->stderrOutput . '</error>');
                    return Command::FAILURE;
                }
                $output->writeln('<info>Successfully rebuilt schema: ' . $database . '</info>');
            }
        } finally {
            unlink($defaultsFile);
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
  ubix app:build <env>
HELP,
        )->addArgument(
            'env',
            InputArgument::REQUIRED,
            'The environment to build',
        );
    }

    /**
     * Writes the connection settings to a private file the client reads instead of argv.
     *
     * @param string $user     Database user
     * @param string $password Database password
     * @param string $host     Database host
     * @param string $port     Database port
     *
     * @return ?string Path to the file, or null when it could not be created
     */
    private function writeDefaultsFile(string $user, string $password, string $host, string $port): ?string
    {
        $path = tempnam(sys_get_temp_dir(), 'ubix-client-');
        if ($path === false) {
            return null;
        }

        //  Narrow the permissions before anything sensitive is written.
        chmod($path, 0600);

        $settings = sprintf("[client]\nuser=%s\npassword=%s\nhost=%s\nport=%s\n", $user, $password, $host, $port);

        if (file_put_contents($path, $settings) === false) {
            unlink($path);

            return null;
        }

        return $path;
    }

    /**
     * Resolves the command-line client to call.
     *
     * MariaDB renamed the binary and its `mysql` shim prints a deprecation notice, while
     * MySQL ships no `mariadb`. The framework names neither: hosts set MYSQL_CLIENT_BINARY,
     * and failing that whichever of the two is on PATH wins.
     *
     * @return string The client binary name
     */
    private function clientBinary(): string
    {
        $configured = getenv('MYSQL_CLIENT_BINARY');
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        $lookup = $this->processService->executeAsSubprocess('command -v mariadb');

        return $lookup->exitCode === 0 ? 'mariadb' : 'mysql';
    }
}
