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
     * The note carries the marker, the model, the short commit and a truncation warning
     *
     * @return void
     */
    public function testTheNoteSaysWhatItIs(): void
    {
        $note = $this->buildService()->noteBody('Nothing worth raising.', 'gemini-flash-latest', '0123456789abcdef', true);

        $this->assertStringStartsWith(AiReviewService::NOTE_MARKER, $note);
        $this->assertStringContainsString('`gemini-flash-latest` on `01234567`', $note);
        $this->assertStringContainsString('covers the first files only', $note);
    }

    /**
     * A second push edits the note the first one wrote
     *
     * @return void
     */
    public function testALaterPushEditsTheEarlierNote(): void
    {
        $this->responses = [
            new Psr7Response(200, [], '[{"id":5,"body":"a person"},{"id":9,"body":"' . AiReviewService::NOTE_MARKER . ' old"}]'),
            new Psr7Response(200, [], '{}'),
        ];

        $this->buildService()->upsertNote('https://gitlab.test/api/v4', '12', '34', 'token', 'new');

        $this->assertSame('PUT', $this->requests[1]->getMethod());
        $this->assertSame('https://gitlab.test/api/v4/projects/12/merge_requests/34/notes/9', (string) $this->requests[1]->getUri());
    }

    /**
     * The first push adds a note
     *
     * @return void
     */
    public function testTheFirstPushAddsANote(): void
    {
        $this->responses = [new Psr7Response(200, [], '[{"id":5,"body":"a person"}]'), new Psr7Response(201, [], '{}')];

        $this->buildService()->upsertNote('https://gitlab.test/api/v4', '12', '34', 'token', 'new');

        $this->assertSame('POST', $this->requests[1]->getMethod());
        $this->assertSame('token', $this->requests[1]->getHeaderLine('PRIVATE-TOKEN'));
    }

    /**
     * A refused write is a failure
     *
     * @return void
     */
    public function testARefusedNoteIsAFailure(): void
    {
        $this->responses = [new Psr7Response(200, [], '[]'), new Psr7Response(403, [], '{}')];

        $this->expectException(RuntimeException::class);

        $this->buildService()->upsertNote('https://gitlab.test/api/v4', '12', '34', 'token', 'new');
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
