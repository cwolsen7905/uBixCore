<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubWeb;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpForbiddenException;
use Ubix\Controller\FanClubWeb\AbstractFanClubWebController as FanClubWebController;
use Ubix\Model\FanClub;
use Ubix\Model\Performer;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\FanClubService;
use Ubix\Service\JsonService;
use Ubix\Service\PerformerService;

/**
 * Controller to handle administration of a fan club
 *
 * @see \Ubix\Tests\Controller\FanClubWeb\FanClubControllerTest PHPUnit test case
 */
final class FanClubController extends FanClubWebController
{
    /**
     * Constructor
     *
     * @param Logger           $logger           PSR logger
     * @param TemplateRenderer $view             Template renderer
     * @param JsonService      $jsonService      JSON service
     * @param FanClubService   $fanClubService   Fan club service
     * @param PerformerService $performerService Performer service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected FanClubService $fanClubService,
        protected PerformerService $performerService,
    ) {
        parent::__construct($logger, $view, $jsonService, $fanClubService);

        //
        //  Determine if we are on the platform user portal or the performer portal
        //
        $this->sendToTemplate('portal', 'performer');
    }

    /**
     * Method to show a logged in performer their fan club's statistics
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @throws HttpForbiddenException When a performer isn't logged in
     *
     * @return Response PSR response
     */
    public function statistics(Request $request, Response $response): Response
    {
        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        if ($this->loggedInAccount === null) {
            throw new HttpForbiddenException($request, 'You must be logged in to view your statistics.');
        }

        assert($this->loggedInAccount instanceof Performer);
        $this->loadFanClub($request, $this->loggedInAccount);

        //
        //  Generate the data for the statistics tables
        //
        assert($this->loggedInAccount instanceof Performer && is_int($this->loggedInAccount->getId()));
        $this->sendToTemplate('memberships', $this->fanClubService->getMembershipsByPerformerId($this->loggedInAccount->getId())); // Andrew note: use ID 1126592 to test on dev

        assert($this->loggedInAccount instanceof Performer && is_int($this->loggedInAccount->getId()));
        $this->sendToTemplate('extraPosts', $this->fanClubService->getPostsByPerformerId($this->loggedInAccount->getId(), null, 'extra', true)); // Andrew note: use ID 588847 to test on dev

        assert($this->loggedInAccount instanceof Performer && is_int($this->loggedInAccount->getId()));
        $this->sendToTemplate('unsoldPosts', $this->fanClubService->getPostsByPerformerId($this->loggedInAccount->getId(), null, 'extra', false));

        assert($this->loggedInAccount instanceof Performer && is_int($this->loggedInAccount->getId()));
        $this->sendToTemplate('posts', $this->fanClubService->getPostsByPerformerId($this->loggedInAccount->getId()));

        assert($this->loggedInAccount instanceof Performer && is_int($this->loggedInAccount->getId()));
        $this->sendToTemplate('scheduledPosts', $this->fanClubService->getPostsByPerformerId($this->loggedInAccount->getId(), 'scheduled')); // Andrew note: use ID 588847 to test on dev

        assert($this->loggedInAccount instanceof Performer && is_int($this->loggedInAccount->getId()));
        $this->sendToTemplate('transactions', $this->fanClubService->getTransactionsByPerformerId($this->loggedInAccount->getId()));

        //
        //  Load generic statistics
        //
        assert($this->loggedInAccount instanceof Performer && is_int($this->loggedInAccount->getId()));
        $this->sendToTemplate('statistics', $this->performerService->getStatisticsByPerformerId($this->loggedInAccount->getId()));

        //
        //  Render the template
        //
        return $this->renderTemplate($response, 'FanClub/statistics.latte');
    }

    /**
     * Method to view all of a fan club's content
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @throws HttpForbiddenException When a performer isn't logged in
     *
     * @return Response PSR response
     */
    public function all(Request $request, Response $response): Response // NOT_IMPLEMENTED: This method is not being implemented at this time in the PHP port
    {
        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        if ($this->loggedInAccount === null) {
            throw new HttpForbiddenException($request, 'You must be logged in to view your content.');
        }

        assert($this->loggedInAccount instanceof Performer);
        $this->loadFanClub($request, $this->loggedInAccount);

        //
        //  Render the template
        //
        return $this->renderTemplate($response, 'FanClub/all.latte');
    }

    /**
     * Method to create new fan club content
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @throws HttpForbiddenException When a performer isn't logged in
     *
     * @return Response PSR response
     */
    public function new(Request $request, Response $response): Response // NOT_IMPLEMENTED: This method is not being implemented at this time in the PHP port
    {
        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        if ($this->loggedInAccount === null) {
            throw new HttpForbiddenException($request, 'You must be logged in to post new content.');
        }

        assert($this->loggedInAccount instanceof Performer);
        $this->loadFanClub($request, $this->loggedInAccount);

        //
        //  Render the template
        //
        return $this->renderTemplate($response, 'FanClub/new.latte');
    }

    /**
     * Method to change a fan club's price
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function changePrice(Request $request, Response $response): Response
    {
        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        if ($this->loggedInAccount instanceof Performer) {
            $this->loadFanClub($request, $this->loggedInAccount);

            //
            //  Change the price
            //
            /**
             * @var array<int|string, string> $postData
             */
            $postData = $request->getParsedBody();

            assert($this->fanClub instanceof FanClub);
            $this->fanClubService->changePrice(
                $this->fanClub,
                (int)($postData['credits'] ?? FanClub::MONTHLY_PRICE_DEFAULT),
            );

            // NOT_IMPLEMENTED: There should be some kind of notification stored to show to users after they are redirected to the homepage as confirmation of the change
        }

        //
        //  Redirect back to the homepage
        //
        return $this->redirect($response, '/');
    }

    /**
     * Method to gift a membership to the fan club
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function giftMembership(Request $request, Response $response): Response
    {
        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        if ($this->loggedInAccount instanceof Performer) {
            $this->loadFanClub($request, $this->loggedInAccount);

            //
            //  Gift the membership
            //
            /**
             * @var array<int|string, string> $postData
             */
            $postData = $request->getParsedBody();

            assert($this->fanClub instanceof FanClub);
            $this->fanClubService->giftMembership(
                $this->fanClub,
                $postData['screenName'] ?? '',
                (int)($postData['days'] ?? 0),
            );

            // NOT_IMPLEMENTED: There should be some kind of notification stored to show to users after they are redirected to the homepage as confirmation of the gift
        }

        //
        //  Redirect back to the homepage
        //
        return $this->redirect($response, '/');
    }
}
