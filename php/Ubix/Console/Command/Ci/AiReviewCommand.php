<?php

declare(strict_types=1);

namespace Ubix\Console\Command\Ci;

use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Service\Ci\AiReviewService;
use Ubix\Service\ProjectRootService;

/**
 * Post an advisory AI review on the merge request this CI pipeline is for
 *
 * Run by a host's MR pipeline on the GitLab runner, with the diff on stdin; it is not a
 * developer tool. Settings come from the job's environment: `GEMINI_API_KEY`,
 * `AI_REVIEW_GITLAB_TOKEN`, optional `AI_REVIEW_MODEL` / `AI_REVIEW_FALLBACK_MODEL`, and GitLab's predefined `CI_*`
 * variables. Wiring: `docs/standards/ai-review-in-ci.md`.
 *
 * A missing key or an empty diff is a skip, not a failure. A refused or rate-limited
 * request is a failure, which the job's `allow_failure` keeps out of the merge gate.
 *
 * @see \Ubix\Tests\Console\Command\Ci\AiReviewCommandTest PHPUnit test case
 */
final class AiReviewCommand extends Command
{
    /**
     * Used when AI_REVIEW_MODEL is unset: Google's alias for its newest Flash model
     */
    private const DEFAULT_MODEL = 'gemini-flash-latest';

    /**
     * Used when AI_REVIEW_FALLBACK_MODEL is unset: the lighter Flash, usually far less busy
     */
    private const DEFAULT_FALLBACK_MODEL = 'gemini-flash-lite-latest';

    /**
     * Constructor
     *
     * @param Logger             $logger          Logger
     * @param AiReviewService    $aiReviewService Talks to Gemini and GitLab
     * @param ProjectRootService $projectRoot     Resolves the host's conventions file
     *
     * @return void
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private AiReviewService $aiReviewService,
        private ProjectRootService $projectRoot,
    ) {
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('CI only: post an advisory AI review on this pipeline\'s merge request')->setHelp(
            <<<'HELP'
Sends the merge request's diff (stdin, or a file) to Gemini with the framework's review
instructions plus the host's own conventions, and writes the answer to one MR note that
is replaced on each push. Meant for an MR pipeline job; see docs/standards/ai-review-in-ci.md.

Usage:
  git diff "$CI_MERGE_REQUEST_DIFF_BASE_SHA" HEAD | ubix ci:aiReview --guide=bin/ci/ai-review-guide.md
HELP,
        )
            ->addArgument('diff-file', InputArgument::OPTIONAL, 'File holding the diff', 'php://stdin')
            ->addOption('guide', null, InputOption::VALUE_REQUIRED, 'The host\'s conventions file, relative to the project root');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int
    {
        $apiKey = $this->env('GEMINI_API_KEY');
        $token  = $this->env('AI_REVIEW_GITLAB_TOKEN');

        if ($apiKey === '' || $token === '') {
            $output->writeln('<comment>Skipped: GEMINI_API_KEY or AI_REVIEW_GITLAB_TOKEN is not set.</comment>');

            return Command::SUCCESS;
        }

        $diffFile = $input->getArgument('diff-file');
        $diff     = is_string($diffFile) ? file_get_contents($diffFile) : false;

        if (!is_string($diff) || trim($diff) === '') {
            $output->writeln('<comment>Skipped: nothing reviewable in this diff.</comment>');

            return Command::SUCCESS;
        }

        $model    = $this->env('AI_REVIEW_MODEL') !== '' ? $this->env('AI_REVIEW_MODEL') : self::DEFAULT_MODEL;
        $fallback = $this->env('AI_REVIEW_FALLBACK_MODEL') !== '' ? $this->env('AI_REVIEW_FALLBACK_MODEL') : self::DEFAULT_FALLBACK_MODEL;
        $models   = array_values(array_unique([$model, $fallback]));
        $fitted   = $this->aiReviewService->fitDiff($diff);

        try {
            $instructions = $this->aiReviewService->instructions($this->projectGuide($input));
            $answer       = $this->aiReviewService->reviewWithFallback($apiKey, $models, $instructions, $this->env('CI_MERGE_REQUEST_TITLE'), $fitted['diff']);
            $this->aiReviewService->upsertNote(
                $this->env('CI_API_V4_URL'),
                $this->env('CI_PROJECT_ID'),
                $this->env('CI_MERGE_REQUEST_IID'),
                $token,
                $this->aiReviewService->noteBody($answer['text'], $answer['model'], $this->env('CI_COMMIT_SHA'), $fitted['truncated']),
            );
        } catch (RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $this->postNotReviewed($token, $e->getMessage(), $output);

            return Command::FAILURE;
        }

        $output->writeln('<info>Review posted for ' . substr($this->env('CI_COMMIT_SHA'), 0, 8) . '.</info>');

        return Command::SUCCESS;
    }

    /**
     * Replace the MR's review note with a "not reviewed" one
     *
     * Otherwise the previous push's review stays up and reads as current. Best effort: if
     * GitLab refuses this too, the job log is all there is.
     *
     * @param string $token  Project access token
     * @param string $reason Why there is no review
     * @param Output $output Console output
     *
     * @return void
     */
    private function postNotReviewed(string $token, string $reason, Output $output): void
    {
        try {
            $this->aiReviewService->upsertNote(
                $this->env('CI_API_V4_URL'),
                $this->env('CI_PROJECT_ID'),
                $this->env('CI_MERGE_REQUEST_IID'),
                $token,
                $this->aiReviewService->notReviewedNoteBody($reason, $this->env('CI_COMMIT_SHA')),
            );
            $output->writeln('<comment>Posted a "not reviewed" note on the MR.</comment>');
        } catch (RuntimeException $e) {
            $output->writeln('<error>Could not post the "not reviewed" note either: ' . $e->getMessage() . '</error>');
        }
    }

    /**
     * The host's conventions, or '' when it names none
     *
     * @param Input $input Console input
     *
     * @throws RuntimeException When a named file cannot be read: a silent fallback to no conventions would look like it worked
     *
     * @return string The file's contents
     */
    private function projectGuide(Input $input): string
    {
        $guide = $input->getOption('guide');

        if (!is_string($guide) || $guide === '') {
            return '';
        }

        $path     = $this->projectRoot->getPath($guide);
        $contents = is_readable($path) ? file_get_contents($path) : false;

        if (!is_string($contents)) {
            throw new RuntimeException('The conventions file ' . $guide . ' cannot be read.');
        }

        return $contents;
    }

    /**
     * An environment variable, or '' when unset
     *
     * @param string $name Variable name
     *
     * @return string The value
     */
    private function env(string $name): string
    {
        $value = getenv($name);

        return is_string($value) ? trim($value) : '';
    }
}
