<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Ci;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\Exception\HttpClientException;
use Ubix\Service\Ci\AiReviewService;
use Ubix\Service\JsonService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Ci\AiReviewService
 *
 * The CI review must never post a note a reviewer cannot trust: no half a diff
 * passed off as the whole, no empty review, and one note per MR however many
 * times the branch is pushed.
 *
 * @coversDefaultClass \Ubix\Service\Ci\AiReviewService
 */
final class AiReviewServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const API = 'https://gitlab.test/api/v4';

    /**
     * Responses the stubbed HTTP client hands back, in order
     *
     * @var list<Psr7Response>
     */
    private array $responses = [];

    /**
     * Requests the stubbed HTTP client received
     *
     * @var list<Request>
     */
    private array $requests = [];

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AiReviewService::class);
    }

    /**
     * A diff that fits is sent whole
     *
     * @return void
     */
    public function testADiffThatFitsIsSentWhole(): void
    {
        $diff = "diff --git a/x b/x\n+one\n";

        $this->assertSame(['diff' => $diff, 'truncated' => false], $this->buildService()->fitDiff($diff, 1000));
    }

    /**
     * An oversized diff is cut at a file boundary and marked truncated
     *
     * Cutting mid-file would hand the model half a hunk to misread.
     *
     * @return void
     */
    public function testAnOversizedDiffIsCutAtAFileBoundary(): void
    {
        $first = "diff --git a/one b/one\n+" . str_repeat('a', 40) . "\n";
        $diff  = $first . "diff --git a/two b/two\n+" . str_repeat('b', 40) . "\n";

        $fitted = $this->buildService()->fitDiff($diff, strlen($first) + 10);

        $this->assertTrue($fitted['truncated']);
        $this->assertSame(rtrim($first, "\n"), $fitted['diff']);
    }

    /**
     * The review text is joined from every part of the first candidate
     *
     * @return void
     */
    public function testTheReviewTextIsReadFromTheResponse(): void
    {
        $this->responses = [new Psr7Response(200, [], '{"candidates":[{"content":{"parts":[{"text":"Nothing "},{"text":"worth raising."}]}}]}')];

        $review = $this->buildService()->review('key', 'gemini-flash-latest', 'guide', 'Title', 'diff');

        $this->assertSame('Nothing worth raising.', $review);
        $this->assertSame('key', $this->requests[0]->getHeaderLine('x-goog-api-key'));
        $this->assertStringContainsString('/models/gemini-flash-latest:generateContent', (string) $this->requests[0]->getUri());
    }

    /**
     * A rate-limited request fails with a reason rather than posting nothing
     *
     * @return void
     */
    public function testARateLimitIsAFailureWithAReason(): void
    {
        $this->responses = [new Psr7Response(429, [], '{}')];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('rate limit');

        $this->buildService()->review('key', 'm', 'guide', 'Title', 'diff');
    }

    /**
     * A response with no text is a failure, never an empty note
     *
     * @return void
     */
    public function testAnEmptyAnswerIsAFailure(): void
    {
        $this->responses = [new Psr7Response(200, [], '{"candidates":[{"finishReason":"SAFETY"}]}')];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('SAFETY');

        $this->buildService()->review('key', 'm', 'guide', 'Title', 'diff');
    }

    /**
     * A busy model is retried, then the fallback answers, and the answer names it
     *
     * @return void
     */
    public function testABusyModelIsRetriedThenTheFallbackAnswers(): void
    {
        $busy            = '{"error":{"code":503,"status":"UNAVAILABLE"}}';
        $this->responses = [
            new Psr7Response(503, [], $busy),
            new Psr7Response(503, [], $busy),
            new Psr7Response(200, [], '{"candidates":[{"content":{"parts":[{"text":"Nothing worth raising."}]}}]}'),
        ];

        $answer = $this->buildService()->reviewWithFallback('key', ['busy-model', 'lite-model'], 'guide', 'Title', 'diff', [0]);

        $this->assertSame(['model' => 'lite-model', 'text' => 'Nothing worth raising.'], $answer);
        $this->assertCount(3, $this->requests);
        $this->assertStringContainsString('/models/lite-model:', (string) $this->requests[2]->getUri());
    }

    /**
     * A refusal is final: no retry, and the fallback is not asked
     *
     * @return void
     */
    public function testARefusalIsNotRetried(): void
    {
        $this->responses = [new Psr7Response(400, [], '{"error":{"message":"API key not valid."}}')];

        try {
            $this->buildService()->reviewWithFallback('key', ['a', 'b'], 'guide', 'Title', 'diff', [0]);
            $this->fail('A 400 must not be retried');
        } catch (RuntimeException $e) {
            $this->assertSame(400, $e->getCode());
        }

        $this->assertCount(1, $this->requests);
    }

    /**
     * When there is no review, the note says so and tells a human to look
     *
     * @return void
     */
    public function testTheNotReviewedNoteSendsItToAHuman(): void
    {
        $note = $this->buildService()->notReviewedNoteBody("Gemini answered HTTP 503: {\n  \"error\"", '0123456789abcdef');

        $this->assertStringStartsWith(AiReviewService::NOTE_MARKER, $note);
        $this->assertStringContainsString('`01234567` was not reviewed: review this one yourself', $note);
        $this->assertStringContainsString('Reason: Gemini answered HTTP 503: {', $note);
        $this->assertStringNotContainsString('"error"', $note);
    }

    /**
     * The request makes Gemini answer in the fixed JSON shape
     *
     * @return void
     */
    public function testTheRequestAsksForStructuredFindings(): void
    {
        $this->responses = [new Psr7Response(200, [], '{"candidates":[{"content":{"parts":[{"text":"{}"}]}}]}')];

        $this->buildService()->review('key', 'm', 'guide', 'Title', 'diff');

        $sent = (string) $this->requests[0]->getBody();
        $this->assertStringContainsString('"responseMimeType":"application\/json"', $sent);
        $this->assertStringContainsString('"findings"', $sent);
    }

    /**
     * A structured answer becomes a verdict and clean findings
     *
     * @return void
     */
    public function testAStructuredAnswerIsRead(): void
    {
        $review = $this->buildService()->parseReview('{"verdict":"One bug","findings":[{"severity":"bug","file":"/php/A.php","line":12,"title":"Off by one","detail":"Loses the last row."},{"severity":"risk","file":"x","line":0,"title":"  ","detail":"no title, dropped"}]}');

        $this->assertSame('One bug', $review['verdict']);
        $this->assertSame([['detail' => 'Loses the last row.', 'file' => 'php/A.php', 'line' => 12, 'severity' => 'bug', 'title' => 'Off by one']], $review['findings']);
    }

    /**
     * Prose instead of JSON still produces a summary rather than a failed run
     *
     * @return void
     */
    public function testProseFallsBackToAVerdict(): void
    {
        $this->assertSame(['findings' => [], 'verdict' => 'Looks fine to me.'], $this->buildService()->parseReview('Looks fine to me.'));
    }

    /**
     * The summary names every finding and tells a human the merge waits for them
     *
     * @return void
     */
    public function testTheSummarySaysWhatItIs(): void
    {
        $review = ['findings' => [['detail' => 'd', 'file' => 'php/A.php', 'line' => 12, 'severity' => 'bug', 'title' => 'Off by one']], 'verdict' => 'One bug'];
        $body   = $this->buildService()->summaryBody($review, 'gemini-flash-latest', '0123456789abcdef', true);

        $this->assertStringStartsWith(AiReviewService::NOTE_MARKER, $body);
        $this->assertStringContainsString('#### AI review — One bug', $body);
        $this->assertStringContainsString('**[bug]** `php/A.php:12` — Off by one', $body);
        $this->assertStringContainsString('the merge waits for it', $body);
        $this->assertStringContainsString('covers the first files only', $body);
    }

    /**
     * With no review thread yet, one is opened
     *
     * @return void
     */
    public function testTheFirstPushOpensAThread(): void
    {
        $this->responses = [new Psr7Response(200, [], '[]'), new Psr7Response(201, [], '{}')];

        $this->buildService()->upsertThread(self::API, '12', '34', 'token', 'summary');

        $this->assertSame('POST', $this->requests[1]->getMethod());
        $this->assertSame(self::API . '/projects/12/merge_requests/34/discussions', (string) $this->requests[1]->getUri());
    }

    /**
     * An unresolved summary is edited in place: one thread to read, however many pushes
     *
     * @return void
     */
    public function testAnUnresolvedSummaryIsEditedInPlace(): void
    {
        $this->responses = [new Psr7Response(200, [], $this->threads([['abc', 5, AiReviewService::NOTE_MARKER . ' old', false]])), new Psr7Response(200, [], '{}')];

        $this->buildService()->upsertThread(self::API, '12', '34', 'token', 'summary');

        $this->assertSame('PUT', $this->requests[1]->getMethod());
        $this->assertSame(self::API . '/projects/12/merge_requests/34/discussions/abc/notes/5', (string) $this->requests[1]->getUri());
    }

    /**
     * Once a human resolved the summary, the next push opens a new one
     *
     * @return void
     */
    public function testAResolvedSummaryIsNotReopened(): void
    {
        $this->responses = [new Psr7Response(200, [], $this->threads([['abc', 5, AiReviewService::NOTE_MARKER . ' old', true]])), new Psr7Response(201, [], '{}')];

        $this->buildService()->upsertThread(self::API, '12', '34', 'token', 'summary');

        $this->assertSame('POST', $this->requests[1]->getMethod());
    }

    /**
     * A refused thread is a failure, never silence
     *
     * @return void
     */
    public function testARefusedThreadIsAFailure(): void
    {
        $this->responses = [new Psr7Response(200, [], '[]'), new Psr7Response(403, [], '{}')];

        $this->expectException(RuntimeException::class);

        $this->buildService()->upsertThread(self::API, '12', '34', 'token', 'summary');
    }

    /**
     * A new finding goes inline on its line; one already raised is not raised again
     *
     * @return void
     */
    public function testFindingsGoInlineAndAreNotRaisedTwice(): void
    {
        $service = $this->buildService();
        $old     = ['detail' => 'd', 'file' => 'php/A.php', 'line' => 3, 'severity' => 'bug', 'title' => 'Already raised'];
        $new     = ['detail' => 'd', 'file' => 'php/B.php', 'line' => 9, 'severity' => 'risk', 'title' => 'Fresh'];

        $this->responses = [
            new Psr7Response(200, [], $this->threads([['f1', 7, $service->getFindingBody($old, 'aaaaaaaa'), true]])),
            new Psr7Response(200, [], '{"diff_refs":{"base_sha":"b","head_sha":"h","start_sha":"s"}}'),
            new Psr7Response(201, [], '{}'),
        ];

        $result = $service->postFindings(self::API, '12', '34', 'token', [$old, $new], 'cccccccc');

        $this->assertSame(['inline' => 1, 'posted' => 1, 'skipped' => 1], $result);
        $sent = (string) $this->requests[2]->getBody();
        $this->assertStringContainsString('"new_path":"php\/B.php"', $sent);
        $this->assertStringContainsString('"new_line":9', $sent);
    }

    /**
     * A line outside the diff becomes a general thread instead of being lost
     *
     * @return void
     */
    public function testAFindingOffTheDiffBecomesAGeneralThread(): void
    {
        $finding = ['detail' => 'd', 'file' => 'php/B.php', 'line' => 400, 'severity' => 'risk', 'title' => 'Far away'];

        $this->responses = [
            new Psr7Response(200, [], '[]'),
            new Psr7Response(200, [], '{"diff_refs":{"base_sha":"b","head_sha":"h","start_sha":"s"}}'),
            new Psr7Response(400, [], '{"message":"line_code can not be blank"}'),
            new Psr7Response(201, [], '{}'),
        ];

        $result = $this->buildService()->postFindings(self::API, '12', '34', 'token', [$finding], 'cccccccc');

        $this->assertSame(['inline' => 0, 'posted' => 1, 'skipped' => 0], $result);
        $this->assertStringNotContainsString('position', (string) $this->requests[3]->getBody());
    }

    /**
     * The host's conventions follow the framework's rules, under their own heading
     *
     * @return void
     */
    public function testTheHostsConventionsFollowTheGenericRules(): void
    {
        $service = $this->buildService();

        $this->assertStringContainsString('Correctness bugs', $service->instructions(''));
        $this->assertStringNotContainsString("This project's conventions", $service->instructions(''));
        $this->assertStringEndsWith("## This project's conventions\n\nMoney is minor units.", $service->instructions("Money is minor units.\n"));
    }

    /**
     * A transport failure (timeout, DNS, TLS) arrives as a RuntimeException
     *
     * The command handles RuntimeException; a raw client exception escaped it and
     * killed the job with exit 255 (kitg pipeline 6431, a 5 s timeout).
     *
     * @return void
     */
    public function testATransportFailureBecomesARuntimeException(): void
    {
        $httpClient = $this->createStub(HttpClient::class);
        $httpClient->method('sendRequest')->willThrowException(new HttpClientException('There was a cURL error (28: timed out).'));
        $logger  = $this->createStub(Logger::class);
        $service = new AiReviewService($logger, $httpClient, new Psr17Factory(), new Psr17Factory(), new JsonService($logger));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('timed out');

        $service->review('key', 'm', 'guide', 'Title', 'diff');
    }

    /**
     * Record a request and hand back the next queued response
     *
     * @param Request $request The request sent
     *
     * @return Psr7Response The next response
     */
    public function answer(Request $request): Psr7Response
    {
        $this->requests[] = $request;

        return array_shift($this->responses) ?? new Psr7Response(500);
    }

    /**
     * A discussions listing: one thread per [id, note id, body, resolved]
     *
     * @param array<array{string, int, string, bool}> $threads The threads
     *
     * @return string The JSON
     */
    private function threads(array $threads): string
    {
        $json = new JsonService($this->createStub(Logger::class));

        return $json->encode(array_map(static function (array $thread): array {
            return ['id' => $thread[0], 'notes' => [['body' => $thread[2], 'id' => $thread[1], 'resolvable' => true, 'resolved' => $thread[3]]]];
        }, $threads));
    }

    /**
     * The service under test, over a stubbed HTTP client
     *
     * @return AiReviewService The service
     */
    private function buildService(): AiReviewService
    {
        $httpClient = $this->createStub(HttpClient::class);
        $httpClient->method('sendRequest')->willReturnCallback($this->answer(...));
        $logger = $this->createStub(Logger::class);

        return new AiReviewService($logger, $httpClient, new Psr17Factory(), new Psr17Factory(), new JsonService($logger));
    }
}
