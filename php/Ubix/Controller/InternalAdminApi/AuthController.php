<?php

declare(strict_types=1);

namespace Ubix\Controller\InternalAdminApi;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\StatusCode;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\AffiliateService;
use Ubix\Service\AttributionService;
use Ubix\Service\JsonService;

/**
 * Controller to handle API calls involving models
 *
 * @see \Ubix\Tests\Controller\InternalAdminApi\AuthControllerTest PHPUnit test case
 */
final class AuthController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger             $logger             The Monolog logger
     * @param TemplateRenderer   $view               The template renderer
     * @param JsonService        $jsonService        The JSON service
     * @param AttributionService $attributionService The Attribution Service
     * @param AffiliateService   $affiliateService   The Affiliate Service
     *
     * @return void
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view, // -> Needed Always
        protected JsonService $jsonService, // -> Needed Always
        protected AttributionService $attributionService,
        protected AffiliateService $affiliateService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * List all the affiliates based on provided filters
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function validate(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $this->logger->info('Validating Auth', array_merge($params, $_COOKIE));

        if (empty($_SESSION['user_id'])) {
            return $this->renderJson($response, ['message' => 'Not Authorized', 'params' => $params, 'cookies' => $_COOKIE], StatusCode::UNAUTHORIZED);
        }

        return $this->renderJson($response, ['message' => 'Auth validated successfully', 'params' => $params]);
    }

    /**
     * List all the affiliates based on provided filters
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function authenticate(Request $request, Response $response): Response
    {

        $params = $request->getQueryParams();

        $postBody =  $this->jsonService->decode($request->getBody()->getContents());

        $this->logger->info('Authenticating Auth', ['data' => $request->getBody()->getContents()]);

        $_SESSION['user_id'] = 1; // Mock user ID for demonstration purposes


        return $this->renderJson($response, ['message' => 'Authentication successful', 'params' => $params, 'success' => '1', 'user' => ['id' => 1, 'username' => $postBody['username'] ?? '', 'password' => $postBody['password'] ?? '']] );
    }

    public function options(Request $request, Response $response): Response
    {
        $origin = $request->getHeaderLine('Origin');

        return $response
        ->withHeader('Access-Control-Allow-Origin', $origin) // Or specify your allowed origin
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withStatus(204);
    }
}
