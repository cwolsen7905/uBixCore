<?php

declare(strict_types=1);

namespace Ubix\Service\Ci;

use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\Exception\DtoException;
use Ubix\Service\JsonService;

/**
 * Re-run a merge request's sign-off job when someone approves or un-approves it
 *
 * GitLab creates no pipeline on approval, so `ci:requireApproval`'s verdict would stay
 * frozen at the last push. A merge-request webhook delivers the approve event here, and
 * this retries just the newest sign-off job on the MR's head pipeline. A job, never a new
 * pipeline: a pipeline would re-run every check and the AI review for each click.
 *
 * Fails soft. Only a wrong secret is refused (401); every other outcome, including an
 * error, is answered 200 and logged, because GitLab disables a webhook that keeps
 * failing and a disabled webhook fails silently.
 *
 * @see \Ubix\Tests\Service\Ci\ApprovalWebhookServiceTest PHPUnit test case
 */
final class ApprovalWebhookService
{
    /**
     * Merge-request actions that change the sign-off verdict
     */
    private const SIGN_OFF_ACTIONS = ['approval', 'approved', 'unapproval', 'unapproved'];

    /**
     * Job states where a retry would only queue a duplicate
     */
    private const ACTIVE_STATES = ['created', 'pending', 'preparing', 'running', 'waiting_for_resource'];

    /**
     * Constructor
     *
     * @param Logger         $logger         Logger
     * @param HttpClient     $httpClient     Talks to the GitLab API
     * @param RequestFactory $requestFactory Builds those requests
     * @param JsonService    $jsonService    Decodes payloads and replies
     * @param string         $apiUrl         GitLab API v4 base URL
     * @param string         $secret         The webhook's secret token; empty refuses everything
     * @param string         $projectTokens  Pairs `id=token`, comma-separated as the environment carries them (Developer role, `api` scope)
     * @param string[]       $jobNames       Names of the sign-off jobs to retry
     */
    public function __construct(
        private Logger $logger,
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private JsonService $jsonService,
        private string $apiUrl,
        private string $secret,
        private string $projectTokens,
        private array $jobNames,
    ) {
    }

    /**
     * Handle one webhook delivery
     *
     * @param string $secretHeader The `X-Gitlab-Token` header
     * @param string $body         The raw payload
     *
     * @return array{status: int, outcome: string} HTTP status to answer, and what happened
     */
    public function handle(string $secretHeader, string $body): array
    {
        if ($this->secret === '' || !hash_equals($this->secret, $secretHeader)) {
            return ['status' => 401, 'outcome' => 'rejected: webhook secret does not match'];
        }

        try {
            $outcome = $this->retrySignOffJob($body);
        } catch (RuntimeException $e) {
            $outcome = 'error: ' . $e->getMessage();
            $this->logger->error('Approval webhook failed', ['error' => $e->getMessage()]);
        }

        $this->logger->info('Approval webhook', ['outcome' => $outcome]);

        return ['status' => 200, 'outcome' => $outcome];
    }

    /**
     * Find the MR's newest sign-off job and retry it, if the event calls for that
     *
     * @param string $body The raw payload (a bad payload or a failed GitLab call surfaces as RuntimeException)
     *
     * @return string What happened
     */
    private function retrySignOffJob(string $body): string
    {
        $event      = $this->decode($body);
        $attributes = is_array($event['object_attributes'] ?? null) ? $event['object_attributes'] : [];
        $project    = is_array($event['project'] ?? null) ? $event['project'] : [];
        $action     = is_string($attributes['action'] ?? null) ? $attributes['action'] : '';

        if (($event['object_kind'] ?? null) !== 'merge_request' || !in_array($action, self::SIGN_OFF_ACTIONS, true)) {
            return 'ignored: not an approval event';
        }

        $projectId = is_int($project['id'] ?? null) ? (string) $project['id'] : '';
        $iid       = is_int($attributes['iid'] ?? null) ? $attributes['iid'] : 0;
        $token     = $this->getProjectTokens()[$projectId] ?? '';

        if ($token === '' || $iid === 0) {
            return 'ignored: no token configured for project ' . $projectId;
        }

        $base         = '/projects/' . rawurlencode($projectId);
        $mergeRequest = $this->call('GET', $base . '/merge_requests/' . $iid, $token);
        $pipeline     = is_array($mergeRequest['head_pipeline'] ?? null) ? $mergeRequest['head_pipeline'] : [];
        $pipelineId   = is_int($pipeline['id'] ?? null) ? $pipeline['id'] : 0;

        if ($pipelineId === 0) {
            return 'ignored: !' . $iid . ' has no pipeline';
        }

        $job = $this->getNewestSignOffJob($this->call('GET', $base . '/pipelines/' . $pipelineId . '/jobs?per_page=100', $token));

        if ($job === null) {
            return 'ignored: pipeline ' . $pipelineId . ' has no sign-off job';
        }

        if (in_array($job['status'], self::ACTIVE_STATES, true)) {
            return 'skipped: job ' . $job['id'] . ' is already ' . $job['status'];
        }

        $this->call('POST', $base . '/jobs/' . $job['id'] . '/retry', $token);

        return 'retried: job ' . $job['id'] . ' (' . $job['name'] . ') for !' . $iid . ' after ' . $action;
    }

    /**
     * The `id=token,id=token` list as a map; malformed pairs are skipped
     *
     * @return array<string, string> Project id => token
     */
    private function getProjectTokens(): array
    {
        $tokens = [];

        foreach (explode(',', $this->projectTokens) as $pair) {
            $parts = explode('=', trim($pair), 2);

            if (count($parts) === 2 && trim($parts[0]) !== '' && trim($parts[1]) !== '') {
                $tokens[trim($parts[0])] = trim($parts[1]);
            }
        }

        return $tokens;
    }

    /**
     * The newest job in a pipeline whose name is a sign-off job
     *
     * @param array<int|string, mixed> $jobs The pipeline's jobs
     *
     * @return ?array{id: int, name: string, status: string} The job
     */
    private function getNewestSignOffJob(array $jobs): ?array
    {
        $newest = null;

        foreach ($jobs as $job) {
            if (!is_array($job) || !is_int($job['id'] ?? null) || !is_string($job['name'] ?? null) || !is_string($job['status'] ?? null)) {
                continue;
            }

            if (in_array($job['name'], $this->jobNames, true) && ($newest === null || $job['id'] > $newest['id'])) {
                $newest = ['id' => $job['id'], 'name' => $job['name'], 'status' => $job['status']];
            }
        }

        return $newest;
    }

    /**
     * Call the GitLab API and decode the reply
     *
     * @param string $method HTTP method
     * @param string $path   Path below the API base URL
     * @param string $token  API token
     *
     * @throws RuntimeException When the request fails, is refused, or the reply is not JSON
     *
     * @return array<int|string, mixed> The decoded reply
     */
    private function call(string $method, string $path, string $token): array
    {
        $request = $this->requestFactory->createRequest($method, rtrim($this->apiUrl, '/') . $path)->withHeader('PRIVATE-TOKEN', $token);

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientException $e) {
            throw new RuntimeException('GitLab could not be reached: ' . $e->getMessage(), 0, $e);
        }

        return $this->decodeResponse($response, $method . ' ' . strtok($path, '?'));
    }

    /**
     * Decode a GitLab reply, or say why it cannot be used
     *
     * @param Response $response The reply
     * @param string   $what     The call, for the message
     *
     * @throws RuntimeException When GitLab refused the call or the reply is not JSON
     *
     * @return array<int|string, mixed> The decoded reply
     */
    private function decodeResponse(Response $response, string $what): array
    {
        $status = $response->getStatusCode();

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException(sprintf('GitLab answered HTTP %d to %s', $status, $what));
        }

        return $this->decode((string) $response->getBody());
    }

    /**
     * Decode JSON
     *
     * @param string $json The text
     *
     * @throws RuntimeException When it is not JSON
     *
     * @return array<int|string, mixed> The value
     */
    private function decode(string $json): array
    {
        try {
            return $this->jsonService->decode($json);
        } catch (DtoException $e) {
            throw new RuntimeException('Not JSON: ' . $e->getMessage(), 0, $e);
        }
    }
}
