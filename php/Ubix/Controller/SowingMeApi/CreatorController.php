<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\StatusCode;
use Ubix\Exception\DtoException;
use Ubix\Payload\Request\CreatorProfileRequestPayload;
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
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     * @param string   $slug     The route slug (injected by name via the PHP-DI Slim bridge)
     *
     * @return Response The modified response object with the operation result.
     */
    public function getBySlug(Request $request, Response $response, string $slug): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $request is required by the route signature but not used
    {
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

    /**
     * Create the authenticated user's creator profile (wizard profile step)
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function createProfile(Request $request, Response $response): Response
    {
        try {
            $payload = CreatorProfileRequestPayload::getRequest($request);
            assert($payload instanceof CreatorProfileRequestPayload);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        }

        $userId = $this->sessionUserId();
        if ($userId === null) {
            return $this->renderJson($response, ['message' => 'Not Authenticated'], StatusCode::UNAUTHORIZED);
        }

        try {
            $creator = $this->creatorProfileService->createCreatorProfile($userId, $payload);
        } catch (Exception $e) {
            return $this->renderJson($response, ['message' => $e->getMessage()], StatusCode::CONFLICT);
        }

        return $this->renderJson($response, [
            'slug'   => $creator->getSlug(),
            'status' => $creator->getStatus()?->value,
        ], StatusCode::CREATED);
    }

    /**
     * Read the authenticated user's own creator profile, any status
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function getOwnProfile(Request $request, Response $response): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $request is required by the route signature but not used
    {
        $userId = $this->sessionUserId();
        if ($userId === null) {
            return $this->renderJson($response, ['message' => 'Not Authenticated'], StatusCode::UNAUTHORIZED);
        }

        $creator = $this->creatorProfileService->getOwnCreator($userId);
        if ($creator === null) {
            return $this->renderJson($response, ['message' => 'No creator profile yet'], StatusCode::NOT_FOUND);
        }

        $profile           = $this->creatorProfileService->composePublicProfile($creator);
        $profile['status'] = $creator->getStatus()?->value;

        return $this->renderJson($response, $profile);
    }

    /**
     * Read the authenticated user's derived onboarding state
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function getOnboarding(Request $request, Response $response): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $request is required by the route signature but not used
    {
        $userId = $this->sessionUserId();
        if ($userId === null) {
            return $this->renderJson($response, ['message' => 'Not Authenticated'], StatusCode::UNAUTHORIZED);
        }

        return $this->renderJson($response, $this->creatorProfileService->getOnboardingState($userId));
    }

    /**
     * Get the authenticated session user id, if any
     *
     * @return ?int The session user id
     */
    private function sessionUserId(): ?int
    {
        $sessionUser = $_SESSION['user'] ?? null;
        if (!is_array($sessionUser) || !isset($sessionUser['id'])) {
            return null;
        }

        $id = $sessionUser['id'];
        assert(is_int($id) || is_string($id));

        return (int) $id;
    }
}
