<?php

declare(strict_types=1);

namespace Ubix\Controller\ProductApi;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\JsonService;

/**
 * Controller to handle API calls involving performers
 *
 * @see \Ubix\Tests\Controller\ProductApi\SiteControllerTest PHPUnit test case
 */
final class SiteController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger           $logger      PSR logger
     * @param TemplateRenderer $view        Template renderer
     * @param JsonService      $jsonService JSON service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
    ) {
        parent::__construct(logger: $logger, templateRenderer: $view, jsonService: $jsonService);
    }

    /**
     * Method to get a performer's details
     *
     * @param Request  $request    PSR request
     * @param Response $response   PSR response
     * @param string   $domainName The domain to return site information on.
     *
     * @return Response PSR response
     */
    public function info(Request $request, Response $response, string $domainName): Response
    {
        $domainRaw = $request->getAttribute('domainName', 'unknown');
        $domain    = is_scalar($domainRaw) ? (string)$domainRaw : 'unknown';

        $this->logger->info('Product API SiteController::info called for domain: ' . $domain);

        //
        //  Render JSON response
        //
        return $this->renderJson($response, [
            'domain'     => $domain,
            'domainName' => $domainName,
            'siteKey'    => 'whitelabel',
        ]);
    }
}
