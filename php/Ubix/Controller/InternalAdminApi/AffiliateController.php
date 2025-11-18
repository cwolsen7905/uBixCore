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
 * @see \Ubix\Tests\Controller\AffiliateApi\AttributionControllerTest PHPUnit test case
 * @see \Ubix\Tests\Controller\InternalAdminApi\AffiliateControllerTest PHPUnit test case
 */
final class AffiliateController extends Controller
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
    public function list(Request $request, Response $response): Response
    {
        /**
 * @var array<int, array{ id: int, domainName: string, username: string }> $affiliates
*/
        $affiliates = $this->affiliateService->searchAffiliates();

        /**
 * @var array{affiliateId: int, domainName: string, username: string} $params
*/
        $params = $request->getQueryParams();

        $affiliateId = $params['affiliateId'] ?? 0;
        $domainName  = $params['domainName'] ?? '';
        $username    = $params['username'] ?? '';

        // Apply filters if provided
        if ($affiliateId !== 0) {
            $filteredAffiliates = [];
            foreach ($affiliates as $affiliate) {
                if ($affiliate['id'] === $affiliateId) {
                    $filteredAffiliates[] = $affiliate;
                }
            }
            $affiliates = $filteredAffiliates;
        }
        if ($domainName !== '') {
            $filteredAffiliates = [];
            foreach ($affiliates as $affiliate) {
                if (stripos($affiliate['domainName'], $domainName) !== false) {
                    $filteredAffiliates[] = $affiliate;
                }
            }
            $affiliates = $filteredAffiliates;
        }
        if ($username !== '') {
            $filteredAffiliates = [];
            foreach ($affiliates as $affiliate) {
                if (stripos($affiliate['username'], $username) !== false) {
                    $filteredAffiliates[] = $affiliate;
                }
            }
            $affiliates = $filteredAffiliates;
        }

        // Sort the results by ID
        usort($affiliates, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        return $this->renderJson($response, $affiliates);
    }

    /**
     * Get affiliate by ID
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function get(Request $request, Response $response): Response
    {
        $affiliateId = $request->getAttribute('affiliateId') ?? 0;
        assert(is_int($affiliateId));

        try {
            $affiliate = $this->affiliateService->getAffiliateById(new AffiliateId($affiliateId));
        } catch (Exception $e) {
            return $this->renderJson($response, [
                'message'    => 'Affiliate not found',
                'statusCode' => StatusCode::NOT_FOUND,
            ], StatusCode::NOT_FOUND);
        }


        return $this->renderJson($response, get_object_vars($affiliate)); // @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
    }
}
