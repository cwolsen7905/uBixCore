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
 * Advisory AI review of a merge request, written to resolvable threads on the MR
 *
 * Meant for CI (`ci:aiReview`), not a developer's machine. Sends the diff, the
 * framework's generic review instructions and the host's own conventions file to the
 * Gemini API (which must answer in a fixed JSON shape), then opens a resolvable thread
 * per finding, inline on its line where the diff has it, plus one summary thread. With
 * the project setting "All threads must be resolved", a human must read the review
 * before the MR merges. Wiring: `docs/standards/ai-review-in-ci.md`.
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
     * Prefix of the marker on each finding's thread; the fingerprint follows it
     */
    public const FINDING_MARKER = '<!-- ubix-ai-review-finding:';

    /**
     * Most diff characters sent; past it the review covers the first files only, and says so
     */
    public const MAX_DIFF_CHARS = 400000;

    /**
     * Gemini's REST endpoint
     */
    private const GEMINI_URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    /**
     * The shape Gemini must answer in; enforced by the API, not asked for in prose
     */
    private const RESPONSE_SCHEMA = [
        'properties' => [
            'findings' => [
                'items' => [
                    'properties' => [
                        'detail'   => ['type' => 'STRING'],
                        'file'     => ['type' => 'STRING'],
                        'line'     => ['type' => 'INTEGER'],
                        'severity' => ['enum' => ['bug', 'security', 'risk', 'test-gap', 'nit-worth-it'], 'type' => 'STRING'],
                        'title'    => ['type' => 'STRING'],
                    ],
                    'required'   => ['severity', 'file', 'line', 'title', 'detail'],
                    'type'       => 'OBJECT',
                ],
                'type'  => 'ARRAY',
            ],
            'verdict'  => ['type' => 'STRING'],
        ],
        'required'   => ['verdict', 'findings'],
        'type'       => 'OBJECT',
    ];

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
     * @return string The review: JSON in the RESPONSE_SCHEMA shape (see parseReview())
     */
    public function review(string $apiKey, string $model, string $guide, string $title, string $diff): string
    {
        $payload = $this->jsonService->encode([
            'contents'           => [['parts' => [['text' => 'Merge request: ' . $title . "\n\nDiff:\n\n" . $diff]], 'role' => 'user']],
            'generationConfig'   => ['maxOutputTokens' => 8192, 'responseMimeType' => 'application/json', 'responseSchema' => self::RESPONSE_SCHEMA],
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
     * Read Gemini's answer into a verdict and findings
     *
     * The schema makes the shape all but certain; if it still is not JSON, the text
     * becomes the verdict with no findings, so the summary thread says what came back
     * rather than the run failing.
     *
     * @param string $text The answer
     *
     * @return array{verdict: string, findings: list<array{severity: string, file: string, line: int, title: string, detail: string}>} The review
     */
    public function parseReview(string $text): array
    {
        try {
            $decoded = $this->jsonService->decode($text);
        } catch (DtoException $e) {
            $this->logger->warning('The review was not JSON; posting it as the verdict', ['error' => $e->getMessage()]);

            return ['findings' => [], 'verdict' => trim($text)];
        }

        $findings = [];

        foreach (is_array($decoded['findings'] ?? null) ? $decoded['findings'] : [] as $finding) {
            if (!is_array($finding) || !is_string($finding['title'] ?? null) || trim($finding['title']) === '') {
                continue;
            }

            $findings[] = [
                'detail'   => is_string($finding['detail'] ?? null) ? trim($finding['detail']) : '',
                'file'     => is_string($finding['file'] ?? null) ? ltrim(trim($finding['file']), '/') : '',
                'line'     => is_int($finding['line'] ?? null) && $finding['line'] > 0 ? $finding['line'] : 0,
                'severity' => is_string($finding['severity'] ?? null) ? $finding['severity'] : 'risk',
                'title'    => trim($finding['title']),
            ];
        }

        $verdict = is_string($decoded['verdict'] ?? null) && trim($decoded['verdict']) !== '' ? trim($decoded['verdict']) : ($findings === [] ? 'Nothing worth raising' : count($findings) . ' finding(s)');

        return ['findings' => array_slice($findings, 0, 10), 'verdict' => $verdict];
    }

    /**
     * The summary thread: the verdict and every finding in one line each
     *
     * @param array{verdict: string, findings: list<array{severity: string, file: string, line: int, title: string, detail: string}>} $review    The review
     * @param string                                                                                                                  $model     Model id that wrote it
     * @param string                                                                                                                  $commitSha The commit reviewed
     * @param bool                                                                                                                    $truncated Whether the diff had to be cut
     *
     * @return string Thread body, carrying NOTE_MARKER
     */
    public function summaryBody(array $review, string $model, string $commitSha, bool $truncated): string
    {
        $lines = [];

        foreach ($review['findings'] as $finding) {
            $where   = $finding['file'] === '' ? '' : ' `' . $finding['file'] . ($finding['line'] > 0 ? ':' . $finding['line'] : '') . '`';
            $lines[] = '- **[' . $finding['severity'] . ']**' . $where . ' — ' . $finding['title'];
        }

        $list   = $lines === [] ? '' : "\n\n" . implode("\n", $lines) . "\n\nEach finding has its own thread.";
        $cut    = $truncated ? "\n\n> The diff was too large to send whole: this review covers the first files only." : '';
        $footer = "\n\n<sub>`" . $model . '` on `' . substr($commitSha, 0, 8) . '` · edited on each push until resolved · **resolve this thread once a human has read it — the merge waits for it**</sub>';

        return self::NOTE_MARKER . "\n#### AI review — " . $review['verdict'] . $list . $cut . $footer;
    }

    /**
     * One finding's thread
     *
     * @param array{severity: string, file: string, line: int, title: string, detail: string} $finding   The finding
     * @param string                                                                          $commitSha The commit it was found on
     *
     * @return string Thread body, carrying the finding's fingerprint
     */
    public function getFindingBody(array $finding, string $commitSha): string
    {
        $where = $finding['file'] === '' ? '' : "\n\n`" . $finding['file'] . ($finding['line'] > 0 ? ':' . $finding['line'] : '') . '`';

        $head = self::FINDING_MARKER . $this->getFingerprint($finding) . " -->\n**[" . $finding['severity'] . '] ' . $finding['title'] . '**' . $where;
        $foot = "\n\n<sub>AI review on `" . substr($commitSha, 0, 8) . '` · reply `Fixed:`, `Dismissed:` (with why) or `Deferred:` (with where), then resolve</sub>';

        return $head . "\n\n" . $finding['detail'] . $foot;
    }

    /**
     * Post each finding not already raised as its own resolvable thread, on its line when it can
     *
     * A finding is "already raised" when a thread with its fingerprint exists, resolved or
     * not: a human who resolved it has decided, and re-raising it on every push is noise.
     * A finding on a line the diff touches goes inline; GitLab refuses a position outside
     * the diff, and the finding then becomes a general thread rather than being lost.
     *
     * @param string                                                                                $apiUrl    GitLab API v4 base URL
     * @param string                                                                                $projectId Project id
     * @param string                                                                                $mrIid     Merge request iid
     * @param string                                                                                $token     Project access token
     * @param list<array{severity: string, file: string, line: int, title: string, detail: string}> $findings  The findings
     * @param string                                                                                $commitSha The commit reviewed
     *
     * @throws RuntimeException When GitLab refuses a thread outright
     *
     * @return array{posted: int, inline: int, skipped: int} What happened
     */
    public function postFindings(string $apiUrl, string $projectId, string $mrIid, string $token, array $findings, string $commitSha): array
    {
        $result = ['inline' => 0, 'posted' => 0, 'skipped' => 0];

        if ($findings === []) {
            return $result;
        }

        $mrUrl      = rtrim($apiUrl, '/') . '/projects/' . rawurlencode($projectId) . '/merge_requests/' . rawurlencode($mrIid);
        $threadsUrl = $mrUrl . '/discussions';
        $raised     = [];

        foreach ($this->getThreadHeads($threadsUrl, $token) as $head) {
            if (str_starts_with($head['body'], self::FINDING_MARKER)) {
                $raised[substr($head['body'], strlen(self::FINDING_MARKER), 12)] = true;
            }
        }

        $diffRefs = $this->getDiffRefs($mrUrl, $token);

        foreach ($findings as $finding) {
            if (isset($raised[$this->getFingerprint($finding)])) {
                $result['skipped']++;

                continue;
            }

            $body     = ['body' => $this->getFindingBody($finding, $commitSha)];
            $position = $diffRefs !== null && $finding['file'] !== '' && $finding['line'] > 0 ? ['base_sha' => $diffRefs['base_sha'], 'head_sha' => $diffRefs['head_sha'], 'new_line' => $finding['line'], 'new_path' => $finding['file'], 'position_type' => 'text', 'start_sha' => $diffRefs['start_sha']] : null;

            if ($position !== null && $this->postThread($threadsUrl, $token, $body + ['position' => $position])) {
                $result['inline']++;
                $result['posted']++;

                continue;
            }

            if (!$this->postThread($threadsUrl, $token, $body)) {
                throw new RuntimeException('GitLab refused a finding thread: check the token has the Reporter role and api scope.');
            }

            $result['posted']++;
        }

        return $result;
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
            "%s\n#### AI review — NOT reviewed\n\n**`%s` was not reviewed: review this one yourself.** Any earlier AI review on this MR described older code.\n\n<sub>Reason: %s · retry the `ai-review` job to try again · **resolve this thread once a human has reviewed the change — the merge waits for it**</sub>",
            self::NOTE_MARKER,
            substr($commitSha, 0, 8),
            $reason,
        );
    }

    /**
     * Put the review in a resolvable thread on the MR, so a human has to resolve it
     *
     * With the project setting "All threads must be resolved", an open review thread
     * blocks the merge: someone has to read it, whatever it says. The newest review
     * thread is edited in place while it is still unresolved, so a branch pushed ten
     * times before anyone looks has one thread to read; once a human resolves it, the
     * next push opens a new thread, because new code needs a new look.
     *
     * @param string $apiUrl    GitLab API v4 base URL
     * @param string $projectId Project id
     * @param string $mrIid     Merge request iid
     * @param string $token     Project access token (Reporter, `api` scope)
     * @param string $body      Thread body
     *
     * @throws RuntimeException When GitLab refuses the write
     *
     * @return void
     */
    public function upsertThread(string $apiUrl, string $projectId, string $mrIid, string $token, string $body): void
    {
        $threadsUrl = rtrim($apiUrl, '/') . '/projects/' . rawurlencode($projectId) . '/merge_requests/' . rawurlencode($mrIid) . '/discussions';
        $open       = $this->getOpenReviewThread($threadsUrl, $token);
        $payload    = $this->jsonService->encode(['body' => $body]);
        $method     = $open === null ? 'POST' : 'PUT';
        $url        = $open === null ? $threadsUrl : $threadsUrl . '/' . rawurlencode($open['discussionId']) . '/notes/' . $open['noteId'];

        $request = $this->requestFactory->createRequest($method, $url)
            ->withHeader('PRIVATE-TOKEN', $token)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($payload));

        $status = $this->send($request)->getStatusCode();

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException(sprintf('GitLab refused the review thread (HTTP %d): check the token has the Reporter role and api scope.', $status));
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
     * The newest summary thread on this MR, if it is still unresolved
     *
     * Only the newest one counts: if a human resolved it, an older unresolved one
     * describes even older code and is not reopened.
     *
     * @param string $threadsUrl The MR's discussions endpoint
     * @param string $token      Project access token
     *
     * @return ?array{discussionId: string, noteId: int} The thread and its first note
     */
    private function getOpenReviewThread(string $threadsUrl, string $token): ?array
    {
        $newest = null;

        foreach ($this->getThreadHeads($threadsUrl, $token) as $head) {
            if (str_starts_with($head['body'], self::NOTE_MARKER) && $head['resolvable']) {
                $newest = $head;
            }
        }

        return $newest === null || $newest['resolved'] ? null : ['discussionId' => $newest['discussionId'], 'noteId' => $newest['noteId']];
    }

    /**
     * The first note of every thread on the MR, oldest first
     *
     * @param string $threadsUrl The MR's discussions endpoint
     * @param string $token      Project access token
     *
     * @return list<array{discussionId: string, noteId: int, body: string, resolvable: bool, resolved: bool}> The thread heads
     */
    private function getThreadHeads(string $threadsUrl, string $token): array
    {
        $heads = [];

        for ($page = 1; $page <= 10; $page++) {
            $request  = $this->requestFactory->createRequest('GET', $threadsUrl . '?per_page=100&page=' . $page)->withHeader('PRIVATE-TOKEN', $token);
            $response = $this->send($request);

            try {
                $threads = $response->getStatusCode() === 200 ? $this->jsonService->decode((string) $response->getBody()) : [];
            } catch (DtoException $e) {
                $this->logger->warning('Could not read the MR threads', ['error' => $e->getMessage()]);
                $threads = [];
            }

            foreach ($threads as $thread) {
                $first = is_array($thread) && is_array($thread['notes'] ?? null) && is_array($thread['notes'][0] ?? null) ? $thread['notes'][0] : [];

                if (is_array($thread) && is_string($thread['id'] ?? null) && is_int($first['id'] ?? null) && is_string($first['body'] ?? null)) {
                    $heads[] = [
                        'body'         => $first['body'],
                        'discussionId' => $thread['id'],
                        'noteId'       => $first['id'],
                        'resolvable'   => ($first['resolvable'] ?? false) === true,
                        'resolved'     => ($first['resolved'] ?? false) === true,
                    ];
                }
            }

            if (count($threads) < 100) {
                break;
            }
        }

        return $heads;
    }

    /**
     * The MR's diff refs, which an inline thread's position needs
     *
     * @param string $mrUrl The MR's API URL
     * @param string $token Project access token
     *
     * @return ?array{base_sha: string, head_sha: string, start_sha: string} The refs, or null when unknown
     */
    private function getDiffRefs(string $mrUrl, string $token): ?array
    {
        $response = $this->send($this->requestFactory->createRequest('GET', $mrUrl)->withHeader('PRIVATE-TOKEN', $token));

        try {
            $mr = $response->getStatusCode() === 200 ? $this->jsonService->decode((string) $response->getBody()) : [];
        } catch (DtoException $e) {
            return null;
        }

        $refs = is_array($mr['diff_refs'] ?? null) ? $mr['diff_refs'] : [];

        return is_string($refs['base_sha'] ?? null) && is_string($refs['head_sha'] ?? null) && is_string($refs['start_sha'] ?? null) ? ['base_sha' => $refs['base_sha'], 'head_sha' => $refs['head_sha'], 'start_sha' => $refs['start_sha']] : null;
    }

    /**
     * Open a thread
     *
     * @param string               $threadsUrl The MR's discussions endpoint
     * @param string               $token      Project access token
     * @param array<string, mixed> $body       The request body
     *
     * @return bool Whether GitLab accepted it
     */
    private function postThread(string $threadsUrl, string $token, array $body): bool
    {
        $request = $this->requestFactory->createRequest('POST', $threadsUrl)
            ->withHeader('PRIVATE-TOKEN', $token)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($this->jsonService->encode($body)));

        $status = $this->send($request)->getStatusCode();

        return $status >= 200 && $status < 300;
    }

    /**
     * A finding's identity across pushes: where it is, roughly, and what it claims
     *
     * The line is left out on purpose: an unrelated edit above it moves the line, and the
     * same finding would be raised again.
     *
     * @param array{severity: string, file: string, line: int, title: string, detail: string} $finding The finding
     *
     * @return string Twelve hex characters
     */
    private function getFingerprint(array $finding): string
    {
        return substr(sha1(strtolower($finding['file'] . '|' . preg_replace('/\s+/', ' ', $finding['title']))), 0, 12);
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
