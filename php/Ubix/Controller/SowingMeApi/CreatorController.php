<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeApi;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\StatusCode;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\CreatorProfileService;
use Ubix\Service\JsonService;

/**
 * Public creator profile endpoints (creator-profile TDS §4.1)
 *
 * @see \Ubix\Tests\Controller\SowingMeApi\CreatorControllerTest PHPUnit test case
 */
final class CreatorController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger                $logger                The Monolog logger
     * @param TemplateRenderer      $view                  The template renderer
     * @param JsonService           $jsonService           The JSON service
     * @param CreatorProfileService $creatorProfileService The creator profile service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected CreatorProfileService $creatorProfileService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Public profile read: 200 profile, 301 retired-slug redirect, or 404
     *
     * @param Request               $request  The HTTP request object containing client data.
     * @param Response              $response The HTTP response object used to send data back to the client
     * @param array<string, string> $args     The route arguments (slug)
     *
     * @return Response The modified response object with the operation result.
     */
    public function getBySlug(Request $request, Response $response, array $args): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $request is required by the route signature but not used
    {
        $slug       = $args['slug'] ?? '';
        $resolution = $this->creatorProfileService->resolveSlug($slug);

        if ($resolution->creator !== null) {
            return $this->renderJson($response, $this->creatorProfileService->composePublicProfile($resolution->creator));
        }

        if ($resolution->redirectToSlug !== null) {
            // 301 (not 302) so the redirect is cacheable/SEO-correct (TDS §3)
            return $this->renderJson(
                $response->withHeader('Location', '/creators/' . $resolution->redirectToSlug),
                ['slug' => $resolution->redirectToSlug],
                StatusCode::MOVED_PERMANENTLY,
            );
        }

        return $this->renderJson($response, ['message' => 'Creator not found'], StatusCode::NOT_FOUND);
    }
}
