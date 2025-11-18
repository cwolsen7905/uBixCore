<?php

declare(strict_types=1);

namespace Ubix\Controller\ModelSignupApi;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ubix\Controller\AbstractController as Controller;

/**
 * Controller to handle reject reason related API calls
 *
 * @see \Ubix\Tests\Controller\ModelSignupApi\ReasonsControllerTest PHPUnit test case
 */
final class ReasonsController extends Controller
{
    /**
     * Method to handle incoming reasons/list requests
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function list(Request $request, Response $response): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        //
        //  Add CORS headers to the response
        //
        $this->addCorsHeaders($response);

        //
        //  Send JSON response
        //
        return $this->renderJson($response, [
            'reasonTypeOne' => [
                [
                    'id'   => 1,
                    'text' => 'First reason',
                ],
                [
                    'id'   => 2,
                    'text' => 'Second reason',
                ],
                [
                    'id'   => 3,
                    'text' => 'Third reason',
                ],
            ],
            'reasonTypeTwo' => [
                [
                    'id'   => 1,
                    'text' => 'First reason',
                ],
                [
                    'id'   => 2,
                    'text' => 'Second reason',
                ],
                [
                    'id'   => 3,
                    'text' => 'Third reason',
                ],
            ],
        ]);
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
