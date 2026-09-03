<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\ProcessResults;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to manage processes and subprocesses
 *
 * @see \Ubix\Tests\Service\ProcessServiceTest PHPUnit test case
 */
final class ProcessService
{
    private const DESCRIPTOR_SPEC = [
        0 => [ 'pipe', 'w' ], // STDIN
        1 => [ 'pipe', 'w' ], // STDOUT
        2 => [ 'pipe', 'w' ], // STDERR
    ];

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Execute a command as a subprocess
     *
     * @param string $command The command to execute
     *
     * @throws Exception If the process could not be opened
     *
     * @return ProcessResults The results of the command execution
     */
    public function executeAsSubprocess(string $command): ProcessResults
    {
        $pipes = [];

        $process = proc_open($command, self::DESCRIPTOR_SPEC, $pipes);
        if ($process === false) {
            throw new Exception('Failed to open process', ExceptionCode::PROCESS_OPEN_FAILED->value);
        }

        assert(count($pipes) === 3 && is_resource($pipes[0]) && is_resource($pipes[1]) && is_resource($pipes[2]));

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return new ProcessResults(
            exitCode:     $exitCode,
            stdoutOutput: trim($stdout),
            stderrOutput: trim($stderr),
        );
    }
}
