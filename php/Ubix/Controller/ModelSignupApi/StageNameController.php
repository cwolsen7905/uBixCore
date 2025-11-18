<?php

declare(strict_types=1);

namespace Ubix\Controller\ModelSignupApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Throwable;
use Ubix\Controller\AbstractController as Controller;
use Ubix\DataTransferObject\PagedObjects;
use Ubix\Enum\Performer\PerformerNameSuggestionSex;
use Ubix\Model\PerformerNameSuggestion;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\JsonService;
use Ubix\Service\PerformerService;
use Ubix\Service\ProspectService;

/**
 * Controller to handle stagename related API calls
 *
 * @see \Ubix\Tests\Controller\ModelSignupApi\StageNameControllerTest PHPUnit test case
 */
final class StageNameController extends Controller
{
    private const MINIMUM_SEARCH_QUERY_LENGTH = 3;

    private int $page;

    private int $pageSize;

    private PerformerNameSuggestionSex $sex;

    private string $searchQuery;

    /**
     * Constructor
     *
     * @param Logger           $logger           PSR logger
     * @param TemplateRenderer $view             Template renderer
     * @param JsonService      $jsonService      JSON service
     * @param PerformerService $performerService Performer service
     * @param ProspectService  $prospectService  Prospect service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected PerformerService $performerService,
        protected ProspectService $prospectService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Method to handle incoming stagename/list requests
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function list(Request $request, Response $response): Response
    {
        //
        //  Determine list parameters
        //
        $this->loadPage($request);
        $this->loadPageSize($request);
        $this->loadSex($request);

        //
        //  Get performer name suggestions and metadata
        //
        $performerNameSuggestions = $this->performerService->getNameSuggestions(
            page:     $this->page,
            pageSize: $this->pageSize,
            sex:      $this->sex,
        );

        //
        //  Add CORS headers to the response
        //
        $this->addCorsHeaders($response);

        //
        //  Send JSON response
        //
        return $this->pagedObjectsToJsonResponse($performerNameSuggestions, $response);
    }

    /**
     * Method to handle incoming stagename/search requests
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function search(Request $request, Response $response): Response
    {
        //
        //  Determine list parameters
        //
        $this->loadPage($request);
        $this->loadPageSize($request);
        $this->loadSex($request);
        $this->loadSearchQuery($request);

        //
        //  Get performer name suggestions and metadata
        //
        $performerNameSuggestions = $this->performerService->getNameSuggestions(
            page:        $this->page,
            pageSize:    $this->pageSize,
            sex:         $this->sex,
            searchQuery: $this->searchQuery,
        );

        //
        //  Add CORS headers to the response
        //
        $this->addCorsHeaders($response);

        //
        //  Send JSON response
        //
        return $this->pagedObjectsToJsonResponse($performerNameSuggestions, $response);
    }

    /**
     * Method to handle incoming stagename/validate requests
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @throws HttpBadRequestException If there is a data type mismatch
     *
     * @return Response PSR response
     */
    public function validate(Request $request, Response $response): Response
    {
        //
        //  Determine list parameters
        //
        $this->loadSearchQuery($request);

        //
        //  Add CORS headers to the response
        //
        $this->addCorsHeaders($response);

        //
        //  Determine stage name validity and send JSON response
        //
        try {
            $this->prospectService->validateStageName(
                $this->prospectService->formatStageName($this->searchQuery),
            );

            return $this->renderJson($response, ['valid' => true]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                'error' => $e->getMessage(),
                'valid' => false,
            ]);
        } catch (Throwable $e) {
            throw new HttpBadRequestException($request, 'Bad request');
        }
    }

    /**
     * Return a JSON response based on a PagedObjects DTO
     *
     * @param PagedObjects $pagedObjects Paged results of performer name suggestion objects
     * @param Response     $response     PSR response
     *
     * @return Response PSR response
     */
    private function pagedObjectsToJsonResponse(PagedObjects $pagedObjects, Response $response): Response
    {
        $json = [
            'code' => 200,
            'data' => [
                'pagination'      => [
                    'max_page' => max(1, ceil($pagedObjects->total / $this->pageSize)),
                    'page'     => $this->page,
                    'pagesize' => $this->pageSize,
                    'total'    => $pagedObjects->total,
                ],
                'suggested_names' => [],
            ],
        ];

        foreach ($pagedObjects->objects as $performerNameSuggestion) {
            assert($performerNameSuggestion instanceof PerformerNameSuggestion);
            $json['data']['suggested_names'][] = $performerNameSuggestion->getName();
        }

        return $this->renderJson($response, $json);
    }

    /**
     * Load the page property
     *
     * @param Request $request PSR request
     *
     * @throws HttpBadRequestException If the page parameter is invalid
     *
     * @return void
     */
    private function loadPage(Request $request): void
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        if (!filter_var($page, FILTER_VALIDATE_INT)) {
            throw new HttpBadRequestException($request, 'The `page` parameter must be an integer');
        }
        assert(is_string($page));
        $this->page = (int)$page;
    }

    /**
     * Load the pageSize property
     *
     * @param Request $request PSR request
     *
     * @throws HttpBadRequestException If the pagesize parameter is invalid
     *
     * @return void
     */
    private function loadPageSize(Request $request): void
    {
        $pageSize = $request->getQueryParams()['pagesize'] ?? PerformerService::DEFAULT_PAGE_SIZE;
        if (!filter_var($pageSize, FILTER_VALIDATE_INT)) {
            throw new HttpBadRequestException($request, 'The `pagesize` parameter must be an integer');
        }
        assert(is_string($pageSize));
        $this->pageSize = (int)$pageSize;
    }

    /**
     * Load the sex property
     *
     * @param Request $request PSR request
     *
     * @throws HttpBadRequestException If the service parameter is invalid
     *
     * @return void
     */
    private function loadSex(Request $request): void
    {
        $sex = $request->getQueryParams()['service'] ?? '';
        assert(is_string($sex));

        switch (strtolower($sex)) {
            case 'f':
            case 'female':
            case 'females':
            case 'girl':
            case 'girls':
            case 'woman':
            case 'women':
                $this->sex = PerformerNameSuggestionSex::FEMALE;
                break;

            case 'boy':
            case 'boys':
            case 'guy':
            case 'guys':
            case 'm':
            case 'male':
            case 'males':
            case 'man':
            case 'men':
                $this->sex = PerformerNameSuggestionSex::MALE;
                break;

            case '':
            case 'either':
                $this->sex = PerformerNameSuggestionSex::EITHER;
                break;

            default:
                throw new HttpBadRequestException($request, 'The `service` parameter must be guys, girls or either');
        }
    }

    /**
     * Load the searchQuery property
     *
     * @param Request $request PSR request
     *
     * @throws HttpBadRequestException If the query parameter is invalid
     *
     * @return void
     */
    private function loadSearchQuery(Request $request): void
    {
        $searchQuery = $request->getQueryParams()['query'] ?? '';
        if (!is_string($searchQuery) || strlen(trim($searchQuery)) < self::MINIMUM_SEARCH_QUERY_LENGTH) {
            throw new HttpBadRequestException($request, 'The `query` parameter must be at least three characters long');
        }
        $this->searchQuery = $searchQuery;
    }

    /**
     * Add CORS headers to a PSR response
     *
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    private function addCorsHeaders(Response $response): Response // NOT IMPLEMENTED: this method likely shouldn't be here and should be more broadly accessible to the entire Project Neptune framework - we should also consider tightening up these headers to be more specific and potentially include validation (this may mean moving them into middleware or the abstract controller)
    {
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response = $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
        return $response->withHeader('Control-Allow-Credentials', 'true');
    }
}
