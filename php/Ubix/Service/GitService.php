<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;

/**
 * Service to access models
 *
 * @see \Ubix\Tests\Service\GitServiceTest PHPUnit test case
 */
final class GitService
{
    /**
     * Constructor
     *
     * @param Logger         $logger         Logger instance
     * @param ProcessService $processService Process service instance
     */
    public function __construct(
        private Logger $logger,
        private ProcessService $processService,
    ) {
    }

    /**
     * Get the list of branches in the repository
     *
     * @return array<int, string>
     *
     * @throws Exception If git command fails
     */
    public function getBranches(): array
    {
        $result = $this->processService->executeAsSubprocess('git branch --list --all --no-color');
        if ($result->exitCode !== 0) {
            $this->logger->error('Failed to get git branches: ' . $result->stderrOutput);
            throw new Exception('Failed to get git branches: ' . $result->stderrOutput);
        }
        $branches = [];
        foreach (explode("\n", $result->stdoutOutput) as $line) {
            $branch = trim(str_replace('*', '', $line));
            if (!empty($branch)) {
                $branches[] = $branch;
            }
        }
        return $branches;
    }

    /**
     * Get the repository's default branch, as the remote reports it.
     *
     * Resolves `refs/remotes/origin/HEAD`, so it is correct for a host on
     * `dev` and for a framework repo on `main` without either having to know
     * about the other. Falls back rather than throwing: a fresh clone may have
     * no `origin/HEAD` (it is set by `git clone`, but not by `git init` plus a
     * manually added remote), and a missing default branch is not a reason to
     * fail a command that can simply ask the user.
     *
     * @param string $fallback Branch to assume when the remote does not say.
     *
     * @return string The default branch name, e.g. `main`.
     */
    public function getDefaultBranch(string $fallback = 'dev'): string
    {
        $result = $this->processService->executeAsSubprocess(
            'git symbolic-ref --quiet --short refs/remotes/origin/HEAD',
        );

        return $this->parseDefaultBranch($result->exitCode, $result->stdoutOutput, $fallback);
    }

    /**
     * Add file(s) to staging area
     *
     * @param string|array<string> $files File or array of files to add
     *
     * @return void
     *
     * @throws Exception If git add fails
     */
    public function add(string|array $files): void
    {
        $files  = (array) $files;
        $result = $this->processService->executeAsSubprocess('git add ' . implode(' ', array_map('escapeshellarg', $files)));
        if ($result->exitCode !== 0) {
            $this->logger->error('Git add failed: ' . $result->stderrOutput);
            throw new Exception('Git add failed: ' . $result->stderrOutput);
        }
    }

    /**
     * Get current branch name
     *
     * @return string
     *
     * @throws Exception If git command fails
     */
    public function getCurrentBranch(): string
    {
        $result = $this->processService->executeAsSubprocess('git rev-parse --abbrev-ref HEAD');
        if ($result->exitCode !== 0) {
            $this->logger->error('Failed to get current branch: ' . $result->stderrOutput);
            throw new Exception('Failed to get current branch: ' . $result->stderrOutput);
        }
        return $result->stdoutOutput;
    }

    /**
     * Get list of modified files/folders
     *
     * @return array{staged: array<array{status: string, file: string, originalFile: string}>, unstaged: array<array{status: string, file: string, originalFile: string}>, untracked: array<array{status: string, file: string, originalFile: string}>}
     *
     * @throws Exception If git status command fails
     */
    public function getFiles(): array
    {
        // Get the list of files and store in an array for processing -> staged, unstanged and untracked
        $stagedFiles    = [];
        $unstagedFiles  = [];
        $untrackedFiles = [];

        $result = $this->processService->executeAsSubprocess('git status --porcelain');

        if ($result->exitCode !== 0) {
            $this->logger->error('Failed to get git status: ' . $result->stderrOutput);
            throw new Exception('Failed to get git status: ' . $result->stderrOutput);
        }

        foreach (explode("\n", $result->stdoutOutput) as $line) {
            $status = substr($line, 0, 2);
            $file   = substr($line, 3);

            if (in_array($status[0], ['M', 'A', 'D', 'R', 'C'], true)) {
                if ($status[0] === 'R') {
                    // Handle renamed files
                    $renamedFiles  = explode(' -> ', $file);
                    $file          = $renamedFiles[1];
                    $originalFile  = $renamedFiles[0];
                    $stagedFiles[] = ['file' => $file, 'status' => $status[0], 'originalFile' => $originalFile];
                } else {
                    $stagedFiles[] = ['file' => $file, 'status' => $status[0], 'originalFile' => $file];
                }
            }

            if (in_array($status[1], ['M', 'A', 'D', 'R', 'C'], true)) {
                $unstagedFiles[] = ['file' => $file, 'status' => $status[1], 'originalFile' => $file];
            }

            if ($status === '??') {
                $untrackedFiles[] = ['file' => $file, 'status' => '?', 'originalFile' => $file];
            }
        }

        return ['staged' => $stagedFiles, 'unstaged' => $unstagedFiles, 'untracked' => $untrackedFiles];
    }

    /**
     * Commit staged changes with a message and description
     *
     * @param string|array<string> $message Commit message
     *
     * @return void
     *
     * @throws Exception If git commit fails
     */
    public function commit(string|array $message): void
    {
        $result = $this->processService->executeAsSubprocess('git commit -m "' . (is_array($message) ? implode('" -m "', array_map('escapeshellarg', $message)) : escapeshellarg($message)) . '"');
        if ($result->exitCode !== 0) {
            $this->logger->error('Git commit failed: ' . $result->stderrOutput);
            throw new Exception('Git commit failed: ' . $result->stderrOutput);
        }
    }

    /**
     * Push committed changes to remote repository
     *
     * @return void
     *
     * @throws Exception If git push fails
     */
    public function push(): void
    {
        $result = $this->processService->executeAsSubprocess('git push');
        if ($result->exitCode !== 0) {
            $this->logger->error('Git push failed: ' . $result->stderrOutput);
            throw new Exception('Git push failed: ' . $result->stderrOutput);
        }
    }

    /**
     * Create merge request from supplied branch to target branch
     *
     * @param string $sourceBranch Source branch name
     * @param string $targetBranch Target branch name
     * @param string $title        Title of the merge request
     * @param string $description  Description of the merge request
     *
     * @return void
     *
     * @throws Exception If git push with merge request options fails
     */
    public function createMergeRequest(string $sourceBranch, string $targetBranch, string $title, string $description): void
    {
        $command = 'git push -o merge_request.create -o merge_request.target=' . escapeshellarg($targetBranch) . ' -o merge_request.title=' . escapeshellarg($title) . (empty($description) ? '' : ' -o merge_request.description=' . escapeshellarg($description)) . ' origin ' . escapeshellarg($sourceBranch);
        $result  = $this->processService->executeAsSubprocess($command);
        if ($result->exitCode !== 0) {
            $this->logger->error('Git create merge request failed: ' . $result->stderrOutput);
            throw new Exception('Git create merge request failed: ' . $result->stderrOutput);
        }
    }

    /**
     * Turn the output of `git symbolic-ref refs/remotes/origin/HEAD` into a branch name.
     *
     * Separated from the subprocess call so the interesting half is testable:
     * `ProcessService` is final and cannot be doubled, and the call itself is
     * one line with no logic in it. Kept an instance method because the `Ubix`
     * standard forbids static ones.
     *
     * @param int    $exitCode Exit code git reported.
     * @param string $stdout   Standard output git produced, e.g. `origin/main`.
     * @param string $fallback Branch to assume when the remote does not say.
     *
     * @return string The default branch name.
     */
    public function parseDefaultBranch(int $exitCode, string $stdout, string $fallback = 'dev'): string
    {
        if ($exitCode !== 0) {
            return $fallback;
        }

        // `origin/main` -> `main`. A branch name may itself contain slashes, so
        // only the first segment (the remote) is removed.
        $ref      = trim($stdout);
        $position = strpos($ref, '/');
        $branch   = $position === false ? $ref : substr($ref, $position + 1);

        return $branch === '' ? $fallback : $branch;
    }
}
