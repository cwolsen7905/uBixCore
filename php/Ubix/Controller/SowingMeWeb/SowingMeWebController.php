<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeWeb;

use Exception;
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

	/**
     * Signup page for SowingMeWeb
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function signup(Request $request, Response $response): Response
    {
        // Determine API hostname based on ENV environment variable
        $env = getenv('ENV') ?: '';
        $apiHostname = match ($env) {
            'PROD'    => 'https://api.sowing.me',
            'STAGING' => 'https://api-sowing-me.staging.ubixsys.com',
            'DEV'     => 'https://api-sowing-me.dev.ubixsys.com',
            default   => 'http://localhost',
        };

        $this->sendToTemplate('apiHostname', $apiHostname);

        return $this->renderTemplate(
			$response,
			'signup.latte',
		);
    }

    /**
     * For Creators page
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function forCreators(Request $request, Response $response): Response
    {
        return $this->renderTemplate($response, 'for-creators.latte');
    }

    /**
     * How It Works page
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function howItWorks(Request $request, Response $response): Response
    {
        return $this->renderTemplate($response, 'how-it-works.latte');
    }

    /**
     * Pricing page
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function pricing(Request $request, Response $response): Response
    {
        return $this->renderTemplate($response, 'pricing.latte');
    }

    /**
     * About page
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function about(Request $request, Response $response): Response
    {
        return $this->renderTemplate($response, 'about.latte');
    }

    /**
     * FAQ page
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function faq(Request $request, Response $response): Response
    {
        return $this->renderTemplate($response, 'faq.latte');
    }

    /**
     * Testimonials page
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function testimonials(Request $request, Response $response): Response
    {
        return $this->renderTemplate($response, 'testimonials.latte');
    }
}
