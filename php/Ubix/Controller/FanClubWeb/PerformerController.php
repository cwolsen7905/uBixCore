<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubWeb;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Ubix\Controller\FanClubWeb\AbstractFanClubWebController as FanClubWebController;
use Ubix\Model\FanClub;
use Ubix\Model\FanClubFile;
use Ubix\Model\Performer;
use Ubix\Model\PlatformUser;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\Blob\FilestackBlobService;
use Ubix\Service\FanClubService;
use Ubix\Service\JsonService;
use Ubix\Service\PerformerService;

/**
 * Controller for interacting with a performer
 *
 * @see \Ubix\Tests\Controller\FanClubWeb\PerformerControllerTest PHPUnit test case
 */
final class PerformerController extends FanClubWebController
{
    /**
     * Constructor
     *
     * @param Logger               $logger               PSR logger
     * @param TemplateRenderer     $view                 Template renderer
     * @param JsonService          $jsonService          JSON service
     * @param FanClubService       $fanClubService       Fan club service
     * @param FilestackBlobService $filestackBlobService Filestack blob service
     * @param PerformerService     $performerService     Performer service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected FanClubService $fanClubService,
        protected FilestackBlobService $filestackBlobService,
        protected PerformerService $performerService,
    ) {
        parent::__construct($logger, $view, $jsonService, $fanClubService);

        //
        //  Determine if we are on the platform user portal or the performer portal
        //
        $this->sendToTemplate('portal', 'platformUser');
    }

    /**
     * Method to access a performer's profile
     *
     * @param Request   $request   PSR request
     * @param Response  $response  PSR response
     * @param Performer $performer The performer
     *
     * @throws HttpBadRequestException If the model doesn't have an active fan club
     *
     * @return Response PSR response
     */
    public function profile(Request $request, Response $response, Performer $performer): Response
    {
        $this->loadLoggedInAccount($request);

        $this->loadLastUrl($request);

        //
        //  If there is an active user load any active memberships they have with the performer
        //
        if ($this->loggedInAccount === null) {
            $this->sendToTemplate('membership', null);
        } else {
            assert($this->loggedInAccount instanceof PlatformUser && is_int($this->loggedInAccount->getId()) && is_int($performer->getId()));
            $this->sendToTemplate(
                'membership',
                $this->fanClubService->getActiveMembershipByUserAndPerformerIds($this->loggedInAccount->getId(), $performer->getId()),
            );
        }

        //
        //  Pass the performer to the template
        //
        $this->sendToTemplate('performer', $performer);

        //
        //  Pass the performer's fan club to the template
        //
        try {
            assert(is_int($performer->getId()));
            $this->sendToTemplate('fanClub', $this->fanClubService->getByPerformerId($performer->getId()));
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, 'This model does not have a fan club.', $e);
        }

        //
        //  Pass the performer's statistics to the template
        //
        assert(is_int($performer->getId()));
        $this->sendToTemplate('statistics', $this->performerService->getStatisticsByPerformerId($performer->getId()));

        //
        //  Look up the performer's posts then load each and pass them to the template
        //
        assert(is_int($performer->getId()));
        $posts = $this->fanClubService->getPostsByPerformerId(
            $performer->getId(),
            'published',
            null,
            null,
            $this->loggedInAccount?->getId(),
        );
        foreach ($posts as $post) {
            //
            //  Load the post's comments
            //
            assert(is_int($post->getId()));
            $comments = $this->fanClubService->getCommentsByPostId($post->getId());

            $post->setComments($comments);

            //
            //  Generate URLs for the post using Filestack
            //
            $postFile = $post->getFile();
            if ($postFile instanceof FanClubFile) {
                $postFile->setFileUrl(
                    $this->filestackBlobService->url($postFile->getHandle() ?? ''),
                );

                $postFile->setThumbnailUrl(
                    $this->filestackBlobService->url($postFile->getThumbnailHandle() ?? ''),
                );
            }
        }

        $this->sendToTemplate('posts', $posts);

        //
        //  Load the template
        //
        return $this->renderTemplate($response, 'Performer/profile.latte');
    }

    /**
     * Method to join a performer's fan club
     *
     * @param Request   $request   PSR request
     * @param Response  $response  PSR response
     * @param Performer $performer The performer
     *
     * @throws HttpUnauthorizedException If the user isn't logged in
     *
     * @return Response PSR response
     */
    public function joinFanClub(Request $request, Response $response, Performer $performer): Response
    {
        $this->loadLoggedInAccount($request);
        if ($this->loggedInAccount === null) {
            throw new HttpUnauthorizedException($request, 'You must be logged in to join a fan club.');
        }

        $this->loadLastUrl($request);

        $this->loadFanClub($request, $performer);

        //
        //  Pass the performer to the template
        //
        $this->sendToTemplate('performer', $performer);

        //
        //  If it's a post then reactivate the membership
        //
        if ($request->getMethod() === 'POST') {
            try {
                assert($this->fanClub instanceof FanClub && $this->loggedInAccount instanceof PlatformUser);
                $this->fanClubService->join($this->fanClub, $this->loggedInAccount);
            } catch (Exception $e) {
                $this->sendToTemplate('error', $e->getMessage());
                return $this->renderTemplate($response, 'Performer/join.latte');
            }

            //
            //  Redirect back to the newly unlocked post
            //
            return $this->redirect(
                $response,
                $this->fanClub->getPerformer() !== null ? $this->fanClub->getPerformer()->getFanClubPermalink() : '/',
            );
        }

        //
        //  Load the template
        //
        return $this->renderTemplate($response, 'Performer/join.latte');
    }

    /**
     * Method to access a performer's statistics
     *
     * @param Request   $request   PSR request
     * @param Response  $response  PSR response
     * @param Performer $performer The performer
     *
     * @return Response PSR response
     */
    public function statistics(Request $request, Response $response, Performer $performer): Response
    {
        $this->loadLoggedInAccount($request);

        $this->loadLastUrl($request);

        //
        //  Pass the performer to the template
        //
        $this->sendToTemplate('performer', $performer);

        //
        //  Load the performer's statistics and pass them to the template
        //
        assert(is_int($performer->getId()));
        $statistics = $this->performerService->getStatisticsByPerformerId($performer->getId());

        $this->sendToTemplate('statistics', $statistics);

        //
        //  Decide which template to render based on the output type
        //
        switch ($request->getQueryParams()['output'] ?? '') {
            case 'json':
                return $this->renderJson($response, [
                    'extraPosts'    => $statistics->getExtraPosts(),
                    'membersPosts'  => $statistics->getMembersPosts(),
                    'performerName' => '@' . $performer->getSlug(),
                    'publicPosts'   => $statistics->getPublicPosts(),
                ]);

            default:
                return $this->renderTemplate($response, 'Performer/statistics.latte');
        }
    }
}
