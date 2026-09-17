<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ubix\Service\GitService;
use Ubix\Service\ProcessService;

/**
 * Behavioural tests for GitService::getDefaultBranch().
 *
 * The default branch has to come from the remote rather than a constant: a host
 * repository's trunk is `dev` and uBixCore's is `main`, so `code:merge` prompted
 * for a branch that did not exist in whichever repo it was not written for.
 *
 * @see \Ubix\Service\GitService::getDefaultBranch()
 */
#[CoversClass(GitService::class)]
final class GitServiceDefaultBranchTest extends TestCase
{
    /**
     * Strip the remote prefix from `origin/HEAD`.
     *
     * @return void
     */
    public function testReturnsTheBranchNameWithoutTheRemotePrefix(): void
    {
        $this->assertSame('main', $this->gitService()->parseDefaultBranch(0, "origin/main\n"));
    }

    /**
     * A host on `dev` resolves to `dev`, from the same code path.
     *
     * @return void
     */
    public function testResolvesAHostTrunk(): void
    {
        $this->assertSame('dev', $this->gitService()->parseDefaultBranch(0, "origin/dev\n"));
    }

    /**
     * A branch name containing a slash keeps everything after the remote.
     *
     * @return void
     */
    public function testKeepsSlashesInsideTheBranchName(): void
    {
        $this->assertSame('release/2026-09', $this->gitService()->parseDefaultBranch(0, "origin/release/2026-09\n"));
    }

    /**
     * No `origin/HEAD` (a `git init` clone) falls back rather than throwing.
     *
     * @return void
     */
    public function testFallsBackWhenTheRemoteDoesNotSay(): void
    {
        $this->assertSame('dev', $this->gitService()->parseDefaultBranch(1, ''));
        $this->assertSame('main', $this->gitService()->parseDefaultBranch(1, '', 'main'));
    }

    /**
     * A zero exit code with empty output must not yield an empty branch name.
     *
     * @return void
     */
    public function testFallsBackOnEmptyOutput(): void
    {
        $this->assertSame('dev', $this->gitService()->parseDefaultBranch(0, "\n"));
    }

    /**
     * A GitService whose subprocess dependency is never exercised.
     *
     * `parseDefaultBranch()` is pure -- it takes the exit code and output it
     * would have produced -- so a real ProcessService is fine here. It is final
     * and cannot be doubled anyway.
     *
     * @return GitService
     */
    private function gitService(): GitService
    {
        return new GitService(new NullLogger(), new ProcessService(new NullLogger()));
    }
}
