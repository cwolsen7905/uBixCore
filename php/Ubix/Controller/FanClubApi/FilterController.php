<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\FilterService;
use Ubix\Service\JsonService;

/**
 * Controller to handle API calls involving filtering data
 *
 * @see \Ubix\Tests\Controller\FanClubApi\FilterControllerTest PHPUnit test case
 */
final class FilterController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger           $logger        PSR logger
     * @param TemplateRenderer $view          Template renderer
     * @param JsonService      $jsonService   JSON service
     * @param FilterService    $filterService Filter service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected FilterService $filterService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Method to filter a string
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @throws HttpBadRequestException If the filter service fails (likely due to the API being down)
     *
     * @return Response PSR response
     */
    public function string(Request $request, Response $response): Response
    {
        //
        //  Filter the string
        //
        /**
         * @var array{
         *     string?: string,
         * } $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        try {
            $filteredString = $this->filterService->string($postBody['string'] ?? '');
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }

        //
        //  Render JSON response
        //
        return $this->renderJson($response, [
            'filtered'           => $filteredString->filtered,
            'filteredWords'      => $filteredString->filteredWords,
            'filtered_words'     => $filteredString->filteredWords, // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'isSpam'             => $filteredString->isSpam,
            'is_spam'            => $filteredString->isSpam, // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'original'           => $filteredString->original,
            'originalFormatted'  => $filteredString->originalFormatted,
            'original_formatted' => $filteredString->originalFormatted, // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'wasFiltered'        => $filteredString->wasFiltered,
            'was_filtered'       => $filteredString->wasFiltered, // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
        ]);
    }
}
