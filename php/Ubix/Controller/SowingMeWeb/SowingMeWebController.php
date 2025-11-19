<?php

declare(strict_types=1);

namespace Ubix\Controller\InternalAdminApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\DataType\Int\AffiliateId;
use Ubix\Enum\StatusCode;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\AffiliateService;
use Ubix\Service\AttributionService;
use Ubix\Service\JsonService;

/**
 * Controller to handle API calls involving models
 *
 * @see \Ubix\Tests\Controller\SowingMeWeb\SowingMeWebControllerTest PHPUnit test case
 */
final class SowingMeWebController extends Controller
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
     * Home page for SowingMeWeb
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function home(Request $request, Response $response): Response
    {

        return $this->renderTemplate(
			$response,
			'home.latte',
		);
    }

}
