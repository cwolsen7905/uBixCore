<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Ubix\DataTransferObject\ProcessResults;

/**
 * Interface for running an external command
 *
 * Any code that shells out depends on this, never on a concrete process runner.
 * The reason is testability rather than vendor-neutrality: a service that builds
 * a command line and interprets an exit code has real logic worth asserting on,
 * and the only way to assert on it without running the binary is for the runner
 * to be a doubleable seam. Before this interface existed, `ProcessService` was
 * `final` with no abstraction, so such a test either invoked the real program —
 * which makes the suite depend on what is installed on the machine — or was not
 * written at all.
 *
 * The contract is deliberately thin: hand it a command line, get back an exit
 * code and the two output streams. Quoting is the caller's job (`escapeshellarg()`
 * exists for exactly that), and so is deciding what a non-zero exit means. This
 * seam knows nothing about which program is being run.
 *
 * The implementation is named for *how* it runs the command, the way
 * `S3MediaStorageService` is named for its store:
 * {@see \Ubix\Service\NativeProcessService} uses `proc_open()` on the machine the
 * code is running on. It was called `ProcessService` until v0.32.0 — the rename is
 * what lets this interface be imported as `ProcessService`, which is the house
 * standard for a service interface and keeps every call site's type hint unchanged.
 *
 * @see \Ubix\Service\NativeProcessService The `proc_open()` implementation
 */
interface ProcessServiceInterface
{
    /**
     * Execute a command as a subprocess and wait for it to finish
     *
     * @param string $command The command line to run, already quoted by the caller
     *
     * @throws Exception If the process could not be started at all
     *
     * @return ProcessResults The exit code and the captured output streams
     */
    public function executeAsSubprocess(string $command): ProcessResults;
}
