<?php

declare(strict_types=1);

namespace Ubix\Service\Ci;

use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\Exception\DtoException;
use Ubix\Service\JsonService;

/**
 * Advisory AI review of a merge request, written to one note on the MR
 *
 * Meant for CI (`ci:aiReview`), not a developer's machine. Sends the diff, the
 * framework's generic review instructions and the host's own conventions file to the
 * Gemini API, then creates the MR's review note or replaces the one an earlier push
 * wrote, so a branch pushed ten times has one note. Wiring:
 * `docs/standards/ai-review-in-ci.md`.
 *
 * Gemini's free tier keeps what it is sent to improve Google's products. Whether that
 * is acceptable is each host's decision; the CI job chooses which paths are sent.
 *
 * @see \Ubix\Tests\Service\Ci\AiReviewServiceTest PHPUnit test case
 */
final class AiReviewService
{
    /**
     * Marks the note this service owns, so a later push edits it rather than adding one
     */
    public const NOTE_MARKER = '<!-- ubix-ai-review -->';

    /**
     * Most diff characters sent; past it the review covers the first files only, and says so
     */
    public const MAX_DIFF_CHARS = 400000;

    /**
     * Gemini's REST endpoint
     */
    private const GEMINI_URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    /**
     * Constructor
     *
     * @param Logger         $logger         The Monolog logger
     * @param HttpClient     $httpClient     Talks to Gemini and GitLab
     * @param RequestFactory $requestFactory Builds those requests
     * @param StreamFactory  $streamFactory  Builds their bodies
     * @param JsonService    $jsonService    Encodes requests, decodes replies
     */
    public function __construct(
        private Logger $logger,
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private StreamFactory $streamFactory,
        private JsonService $jsonService,
    ) {
    }

    /**
     * The system instruction: the generic review rules, then the host's conventions
     *
     * @param string $projectGuide The host's conventions (Markdown), or '' for none
     *
     * @return string The instruction
     */
    public function instructions(string $projectGuide): string
    {
        $generic = file_get_contents(__DIR__ . '/ai-review-instructions.md');
        $generic = is_string($generic) ? trim($generic) : '';

        return trim($projectGuide) === '' ? $generic : $generic . "\n\n## This project's conventions\n\n" . trim($projectGuide);
    }

    /**
     * Cut a diff at the last whole file that fits
     *
     * @param string $diff     The full diff
     * @param int    $maxChars Most characters to keep
     *
     * @return array{diff: string, truncated: bool} The diff to send, and whether files were dropped
     */
    public function fitDiff(string $diff, int $maxChars = self::MAX_DIFF_CHARS): array
    {
        if (strlen($diff) <= $maxChars) {
            return ['diff' => $diff, 'truncated' => false];
        }

        // The window runs one marker past the limit, so a file that starts exactly at
        // the limit still counts as a boundary; the cut itself never exceeds it.
        $marker = "\ndiff --git ";
        $cut    = strrpos(substr($diff, 0, $maxChars + strlen($marker)), $marker);

        return ['diff' => substr($diff, 0, $cut === false || $cut === 0 || $cut > $maxChars ? $maxChars : $cut), 'truncated' => true];
    }

    /**
     * Ask Gemini for the review
     *
     * @param string $apiKey Gemini API key
     * @param string $model  Model id
     * @param string $guide  The system instruction (see instructions())
     * @param string $title  The MR title
     * @param string $diff   The diff to review
     *
     * @throws RuntimeException When there is no review: rate limited, refused, or empty
     *
     * @return string The review, as GitLab Markdown
     */
    public function review(string $apiKey, string $model, string $guide, string $title, string $diff): string
    {
        $payload = $this->jsonService->encode([
            'contents'           => [['parts' => [['text' => 'Merge request: ' . $title . "\n\nDiff:\n\n" . $diff]], 'role' => 'user']],
            'generationConfig'   => ['maxOutputTokens' => 8192],
            'system_instruction' => ['parts' => [['text' => $guide]]],
        ]);

        $request = $this->requestFactory->createRequest('POST', sprintf(self::GEMINI_URL, rawurlencode($model)))
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('x-goog-api-key', $apiKey)
            ->withBody($this->streamFactory->createStream($payload));

        $response = $this->send($request);
        $status   = $response->getStatusCode();
        $body     = (string) $response->getBody();

        if ($status === 429) {
            throw new RuntimeException('Gemini rate limit reached (free tier); no review for this push.', $status);
        }

        if ($status !== 200) {
            // The status is the code, so a caller can tell "busy, try again" (503) from "refused" (4xx).
            throw new RuntimeException(sprintf('Gemini answered HTTP %d: %s', $status, substr($body, 0, 500)), $status);
        }

        return $this->reviewText($body);
    }

    /**
     * Ask each model in turn, retrying a busy one before moving to the next
     *
     * Gemini's free tier answers 503 ("high demand") and 429 often, and usually only for a
     * minute or two; the newest model is the busiest. So a busy answer is retried after
     * each of `$backoffSeconds`, and only then does the next model get a turn. Any other
     * failure (a refusal, an empty answer, a bad key) is final and is not retried.
     *
     * @param string   $apiKey         Gemini API key
     * @param string[] $models         Model ids, first choice first
     * @param string   $guide          The system instruction (see instructions())
     * @param string   $title          The MR title
     * @param string   $diff           The diff to review
     * @param int[]    $backoffSeconds Waits between attempts on one model
     *
     * @throws RuntimeException When no model produced a review
     *
     * @return array{model: string, text: string} The review and the model that wrote it
     */
    public function reviewWithFallback(string $apiKey, array $models, string $guide, string $title, string $diff, array $backoffSeconds = [10, 30]): array
    {
        $lastError = new RuntimeException('No model to ask.');

        foreach ($models as $model) {
            foreach ([0, ...$backoffSeconds] as $wait) {
                if ($wait > 0) {
                    sleep($wait);
                }

                try {
                    return ['model' => $model, 'text' => $this->review($apiKey, $model, $guide, $title, $diff)];
                } catch (RuntimeException $e) {
                    $lastError = $e;

                    if (!in_array($e->getCode(), [429, 503], true)) {
                        throw $e;
                    }

                    $this->logger->info('Gemini busy; retrying', ['model' => $model, 'status' => $e->getCode()]);
                }
            }
        }

        throw $lastError;
    }

    /**
     * The note that goes on the MR
     *
     * @param string $review    The review text
     * @param string $model     Model id that wrote it
     * @param string $commitSha The commit reviewed
     * @param bool   $truncated Whether the diff had to be cut
     *
     * @return string Note body, carrying NOTE_MARKER
     */
    public function noteBody(string $review, string $model, string $commitSha, bool $truncated): string
    {
        $cut    = $truncated ? "\n\n> The diff was too large to send whole: this review covers the first files only." : '';
        $footer = "\n\n<sub>`" . $model . '` on `' . substr($commitSha, 0, 8) . '` · updated on each push</sub>';

        return self::NOTE_MARKER . "\n#### AI review — advisory\n\n" . trim($review) . $cut . $footer;
    }

    /**
     * The note that replaces the review when there is none for this push
     *
     * Posting it matters as much as the review itself: without it a failed run left the
     * previous push's review standing, describing code that has since changed.
     *
     * @param string $reason    Why there is no review (one line)
     * @param string $commitSha The commit that was not reviewed
     *
     * @return string Note body, carrying NOTE_MARKER
     */
    public function notReviewedNoteBody(string $reason, string $commitSha): string
    {
        $reason = trim(strtok($reason, "\n") ?: $reason);
        $reason = strlen($reason) > 200 ? substr($reason, 0, 200) . '…' : $reason;

        return sprintf(
            "%s\n#### AI review — NOT reviewed\n\n**`%s` was not reviewed: review this one yourself.** Any earlier AI review on this MR described older code and has been replaced.\n\n<sub>Reason: %s · retry the `ai-review` job to try again</sub>",
            self::NOTE_MARKER,
            substr($commitSha, 0, 8),
            $reason,
        );
    }

    /**
     * Create the MR's review note, or replace the one an earlier push wrote
     *
     * @param string $apiUrl    GitLab API v4 base URL
     * @param string $projectId Project id
     * @param string $mrIid     Merge request iid
     * @param string $token     Project access token (Reporter, `api` scope)
     * @param string $body      Note body
     *
     * @throws RuntimeException When GitLab refuses the write
     *
     * @return void
     */
    public function upsertNote(string $apiUrl, string $projectId, string $mrIid, string $token, string $body): void
    {
        $notesUrl = rtrim($apiUrl, '/') . '/projects/' . rawurlencode($projectId) . '/merge_requests/' . rawurlencode($mrIid) . '/notes';
        $existing = $this->getOwnNoteId($notesUrl, $token);
        $payload  = $this->jsonService->encode(['body' => $body]);

        $request = $this->requestFactory->createRequest($existing === null ? 'POST' : 'PUT', $existing === null ? $notesUrl : $notesUrl . '/' . $existing)
            ->withHeader('PRIVATE-TOKEN', $token)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($payload));

        $status = $this->send($request)->getStatusCode();

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException(sprintf('GitLab refused the note (HTTP %d): check the token has the Reporter role and api scope.', $status));
        }
    }

    /**
     * Pull the review text out of a generateContent response
     *
     * @param string $body Response body
     *
     * @throws RuntimeException When there is no text in it
     *
     * @return string The text
     */
    private function reviewText(string $body): string
    {
        try {
            $decoded = $this->jsonService->decode($body);
        } catch (DtoException $e) {
            $this->logger->warning('Gemini answered with something that is not JSON', ['error' => $e->getMessage()]);
            $decoded = [];
        }

        $candidates = is_array($decoded['candidates'] ?? null) ? $decoded['candidates'] : [];
        $candidate  = is_array($candidates[0] ?? null) ? $candidates[0] : [];
        $content    = is_array($candidate['content'] ?? null) ? $candidate['content'] : [];
        $parts      = is_array($content['parts'] ?? null) ? $content['parts'] : [];
        $text       = '';

        foreach ($parts as $part) {
            if (is_array($part) && is_string($part['text'] ?? null)) {
                $text .= $part['text'];
            }
        }

        if (trim($text) === '') {
            $reason = is_string($candidate['finishReason'] ?? null) ? $candidate['finishReason'] : 'none given';

            throw new RuntimeException('Gemini returned no review text (finish reason: ' . $reason . ').');
        }

        return trim($text);
    }

    /**
     * The id of the note an earlier run wrote on this MR, if any
     *
     * @param string $notesUrl The MR's notes endpoint
     * @param string $token    Project access token
     *
     * @return ?int The note id
     */
    private function getOwnNoteId(string $notesUrl, string $token): ?int
    {
        $request  = $this->requestFactory->createRequest('GET', $notesUrl . '?per_page=100&sort=desc')->withHeader('PRIVATE-TOKEN', $token);
        $response = $this->send($request);
        try {
            $notes = $response->getStatusCode() === 200 ? $this->jsonService->decode((string) $response->getBody()) : [];
        } catch (DtoException $e) {
            $this->logger->warning('Could not read the MR notes; a new note will be added', ['error' => $e->getMessage()]);
            $notes = [];
        }

        foreach ($notes as $note) {
            if (is_array($note) && is_string($note['body'] ?? null) && str_contains($note['body'], self::NOTE_MARKER) && is_int($note['id'] ?? null)) {
                return $note['id'];
            }
        }

        return null;
    }

    /**
     * Send a request; a transport failure becomes the RuntimeException callers already handle
     *
     * @param Request $request The request
     *
     * @throws RuntimeException When the request cannot be sent (timeout, DNS, TLS)
     *
     * @return Response The response
     */
    private function send(Request $request): Response
    {
        try {
            return $this->httpClient->sendRequest($request);
        } catch (ClientException $e) {
            throw new RuntimeException('Request to ' . $request->getUri()->getHost() . ' failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
