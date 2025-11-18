<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubWeb;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Model\FanClub;
use Ubix\Model\FanClubPost;
use Ubix\Model\Performer;
use Ubix\Model\PlatformUser;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\FanClubService;
use Ubix\Service\JsonService;

/**
 * Abstract controller for use on flirt.fans
 */
abstract class AbstractFanClubWebController extends Controller
{
    protected Performer|PlatformUser|null $loggedInAccount = null;

    protected ?FanClub $fanClub = null;

    /**
     * Constructor
     *
     * @param Logger           $logger         PSR logger
     * @param TemplateRenderer $view           Template renderer
     * @param JsonService      $jsonService    JSON service
     * @param FanClubService   $fanClubService Fan club service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected FanClubService $fanClubService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Load the logged in account from the request
     *
     * @param Request $request PSR request
     *
     * @return void
     */
    protected function loadLoggedInAccount(Request $request): void
    {
        $account = $request->getAttribute('loggedInAccount');
        assert($account instanceof Performer || $account instanceof PlatformUser || $account === null);

        $this->loggedInAccount = $account;
        $this->sendToTemplate('loggedInAccount', $account);
    }

    /**
     * Load the fan club of a performer
     *
     * @param Request   $request   PSR request
     * @param Performer $performer The performer
     *
     * @throws HttpBadRequestException If the performer doesn't have a fan club
     *
     * @return void
     */
    protected function loadFanClub(Request $request, Performer $performer)
    {
        $performerId = $performer->getId();
        if ($performerId === null) {
            throw new HttpBadRequestException($request, 'Model is missing an ID.');
        }

        try {
            $this->fanClub = $this->fanClubService->getByPerformerId($performerId);
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, 'This model does not have a fan club.', $e);
        }

        $this->sendToTemplate('fanClub', $this->fanClub);
    }

    /**
     * Get trending posts by category
     *
     * @return array<string, FanClubPost[]> Return an array of trending posts by predefined categories
     */
    protected function getTrending(): array
    {
        return [
            'Latest Post'    => $this->fanClubService->getLatestPost(),
            'Most Commented' => $this->fanClubService->getMostCommentedPosts(),
            'Most Liked'     => $this->fanClubService->getMostLikedPosts(),
            'Public Posts'   => $this->fanClubService->getPublicPosts(),
            'Top Hashtag'    => $this->fanClubService->getTopHashTagPosts(),
        ];
    }
}
