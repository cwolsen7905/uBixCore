<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Model\Performer;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\JsonService;
use Ubix\Service\NotificationService;
use Ubix\Service\PerformerService;

/**
 * Controller to handle API calls involving performers
 *
 * @see \Ubix\Tests\Controller\FanClubApi\PerformerControllerTest PHPUnit test case
 */
final class PerformerController extends Controller
{
    private Performer $performer;

    /**
     * Constructor
     *
     * @param Logger              $logger              PSR logger
     * @param TemplateRenderer    $view                Template renderer
     * @param JsonService         $jsonService         JSON service
     * @param PerformerService    $performerService    Performer service
     * @param NotificationService $notificationService Notification service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected PerformerService $performerService,
        protected NotificationService $notificationService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Method to get a performer's details
     *
     * @param Request  $request     PSR request
     * @param Response $response    PSR response
     * @param int      $performerId The performer's ID
     *
     * @return Response PSR response
     */
    public function get(Request $request, Response $response, int $performerId): Response
    {
        //
        //  Load the performer
        //
        $this->loadPerformer($request, $performerId);

        //
        //  Render JSON response
        //
        return $this->renderJson($response, [
            'broadcasterId'  => $this->performer->getBroadcasterId(),
            'broadcaster_id' => $this->performer->getBroadcasterId(), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'harvestCode'    => $this->performer->getHarvestCode(),
            'harvest_code'   => $this->performer->getHarvestCode(), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'id'             => $this->performer->getId(),
            'image'          => $this->performer->getImage(),
            'name'           => $this->performer->getName(),
            'studio'         => $this->performer->getStudioCode(),
            'username'       => $this->performer->getUsername(),
        ]);
    }

    /**
     * Method to notify a performer
     *
     * @param Request  $request     PSR request
     * @param Response $response    PSR response
     * @param int      $performerId The performer's ID
     *
     * @throws HttpBadRequestException If the message fails to send
     *
     * @return Response PSR response
     */
    public function notify(Request $request, Response $response, int $performerId): Response
    {
        //
        //  Load the performer
        //
        $this->loadPerformer($request, $performerId);

        //
        //  Send the notification
        //
        /**
         * @var array{
         *     title?: string,
         *     message?: string,
         * } $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        $senderIp = $request->getAttribute('normalizedIpAddress') ?? '';
        assert(is_string($senderIp));

        try {
            $this->notificationService->sendMessage(
                title:         $postBody['title'] ?? '',
                body:          $postBody['message'] ?? '',
                recipientType: 'model',
                recipientIds:  $performerId,
                senderIp:      $senderIp,
                senderType:    'system',
            );
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage()); // TEMPORARY: ANDREW:: why isn't phpcs flagging that there is no third parameter $e?
        }

        //
        //  Include a 201 HTTP header (Created) to indicate the notification was sent successfully
        //
        $response = $response->withStatus(201);

        //
        //  Render JSON to alert the user
        //
        return $this->renderJson($response, [
            'message' => 'You have successfully notified the model with a message.',
            'status'  => 'success',
        ]);
    }

    /**
     * Load a performer by ID
     *
     * @param Request $request     PSR request
     * @param int     $performerId The performer's ID
     *
     * @throws HttpNotFoundException If the performer isn't found
     *
     * @return void
     */
    private function loadPerformer(Request $request, int $performerId): void
    {
        try {
            $this->performer = $this->performerService->getById($performerId);
        } catch (Exception $e) {
            throw new HttpNotFoundException($request, 'Model not found.', $e);
        }
    }
}
