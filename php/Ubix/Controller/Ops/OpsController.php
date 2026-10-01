<?php

declare(strict_types=1);

namespace Ubix\Controller\Ops;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\StatusCode;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\Ci\ApprovalWebhookService;
use Ubix\Service\JsonService;

/**
 * The uBixOps app's endpoints: a health check, and GitLab's merge-request webhook
 *
 * Wiring: `docs/architecture/ubix-ops.md`.
 *
 * @see \Ubix\Tests\Controller\Ops\OpsControllerTest PHPUnit test case
 */
final class OpsController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger                 $logger                 Logger
     * @param TemplateRenderer       $templateRenderer       Unused here; required by the base controller
     * @param JsonService            $jsonService            Renders the JSON answers
     * @param ApprovalWebhookService $approvalWebhookService Re-runs sign-off jobs
     */
    public function __construct(
        Logger $logger,
        TemplateRenderer $templateRenderer,
        JsonService $jsonService,
        private ApprovalWebhookService $approvalWebhookService,
    ) {
        parent::__construct($logger, $templateRenderer, $jsonService);
    }

    /**
     * GET /health
     *
     * @param Request  $request  The request
     * @param Response $response The response
     *
     * @return Response The response
     */
    public function health(Request $request, Response $response): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- Slim route signature
    {
        return $this->renderJson($response, ['status' => 'ok']);
    }

    /**
     * POST /gitlab/merge-request-hook
     *
     * @param Request  $request  The webhook delivery
     * @param Response $response The response
     *
     * @return Response 401 for a wrong secret, otherwise 200 with what happened
     */
    public function gitlabMergeRequestHook(Request $request, Response $response): Response
    {
        $result = $this->approvalWebhookService->handle($request->getHeaderLine('X-Gitlab-Token'), (string) $request->getBody());

        return $this->renderJson($response, ['outcome' => $result['outcome']], $result['status'] === 401 ? StatusCode::UNAUTHORIZED : StatusCode::OK);
    }
}
