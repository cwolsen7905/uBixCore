<?php

declare(strict_types=1);

namespace Ubix\Controller\AffiliateApi;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\StatusCode;
use Ubix\Exception\DtoException;
use Ubix\Payload\Request\FirstMoneyRequestPayload;
use Ubix\Payload\Response\AttributionResponsePayload;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\AffiliateService;
use Ubix\Service\AttributionService;
use Ubix\Service\JsonService;

/**
 * Controller to handle API calls involving models
 *
 * @see \Ubix\Tests\Controller\AffiliateApi\AttributionControllerTest PHPUnit test case
 */
final class AttributionController extends Controller
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
     * All registered users will begin with an xxxx code.
     * We Check For An Earlier Related Account mp_code To attribute to.
     * If not within the week protection we attribute them to the
     * incoming mp_code. Earlier Accounts MUST have some payment activity to be
     * considered
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client.
     *
     * @return Response The modified response object with the operation result.
     */
    public function firstMoney(Request $request, Response $response): Response
    {
        try {
            $requestData = FirstMoneyRequestPayload::getRequest($request);
            assert($requestData instanceof FirstMoneyRequestPayload);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        }

        //  Log Things So We Know What's Going On
        $this->logger->debug('userId: ' . $requestData->userId->value . ', amount: ' . $requestData->amount->value . ', envMpCode: ' . $requestData->envMpCode->value . ', transactionDate: ' . $requestData->transactionDate?->value->format('Y-m-d H:i:s') . ', clickId: ' . $requestData->clickId?->value . ', voluumClickId: ' . $requestData->voluumClickId?->value);

        $returnData = $this->attributionService->firstMoney($requestData->userId, $requestData->amount, $requestData->envMpCode, $requestData->transactionDate);

        $responsePayload = AttributionResponsePayload::getResponse($returnData);


        $this->logger->debug($this->jsonService->encode($returnData));

        return $this->renderJsonWithPayload($response, $responsePayload);
    }
}
