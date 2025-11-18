<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubWeb;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\FanClubWeb\AbstractFanClubWebController as FanClubWebController;
use Ubix\Model\Performer;
use Ubix\Model\PlatformUser;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\FanClubService;
use Ubix\Service\JsonService;
use Ubix\Service\PerformerService;

/**
 * Controller for accessing the homepage
 *
 * @see \Ubix\Tests\Controller\FanClubWeb\HomeControllerTest PHPUnit test case
 */
final class HomeController extends FanClubWebController
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
    }

    /**
     * Method to access the performer homepage
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function performer(Request $request, Response $response): Response
    {
        //
        //  Determine if we are on the platform user portal or the performer portal
        //
        $this->sendToTemplate('portal', 'performer');

        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        //
        //  If the performer is logged in load their statistics
        //
        if ($this->loggedInAccount instanceof Performer) {
            $this->loadFanClub($request, $this->loggedInAccount);

            assert($this->loggedInAccount instanceof Performer && is_int($this->loggedInAccount->getId()));
            $this->sendToTemplate('statistics', $this->performerService->getStatisticsByPerformerId($this->loggedInAccount->getId()));
        }

        //
        //  Render the template
        //
        return $this->renderTemplate($response, 'Home/performer.latte');
    }

    /**
     * Method to access the user homepage
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function platformUser(Request $request, Response $response): Response
    {
        //
        //  Determine if we are on the platform user portal or the performer portal
        //
        $this->sendToTemplate('portal', 'platformUser');

        $this->sendToTemplate('trending', $this->getTrending());

        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        //
        //  If the user is logged in load their memberships and unlocked posts
        //
        if ($this->loggedInAccount !== null) {
            assert($this->loggedInAccount instanceof PlatformUser && is_int($this->loggedInAccount->getId()));
            $this->sendToTemplate('memberships', $this->fanClubService->getMembershipsByUserId($this->loggedInAccount->getId()));

            assert($this->loggedInAccount instanceof PlatformUser && is_int($this->loggedInAccount->getId()));
            $this->sendToTemplate('unlockedPosts', $this->fanClubService->getUnlockedPostsByUserId($this->loggedInAccount->getId()));
        }

        //
        //  Render the template
        //
        return $this->renderTemplate($response, 'Home/platformUser.latte');
    }
}
