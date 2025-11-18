<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubWeb;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use Ubix\Controller\FanClubWeb\AbstractFanClubWebController as FanClubWebController;
use Ubix\Model\FanClubPost;
use Ubix\Model\PlatformUser;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\FanClubService;
use Ubix\Service\JsonService;

/**
 * Controller for interacting with a specific fan club post
 *
 * @see \Ubix\Tests\Controller\FanClubWeb\PostControllerTest PHPUnit test case
 */
final class PostController extends FanClubWebController
{
    private FanClubPost $post;

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
        parent::__construct($logger, $view, $jsonService, $fanClubService);

        //
        //  Determine if we are on the platform user portal or the performer portal
        //
        $this->sendToTemplate('portal', 'platformUser');
    }

    /**
     * Method to like a fan club post
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     * @param int      $postId   The post's ID
     *
     * @throws HttpUnauthorizedException When a user isn't logged in
     *
     * @return Response PSR response
     */
    public function like(Request $request, Response $response, int $postId): Response
    {
        $this->loadLoggedInAccount($request);
        if ($this->loggedInAccount === null) {
            throw new HttpUnauthorizedException($request, 'You must be logged in to like a post.');
        }

        $this->loadLastUrl($request);

        //
        //  Load the post
        //
        $this->loadPost($request, $postId);

        //
        //  Like the post
        //
        assert($this->loggedInAccount instanceof PlatformUser);
        $this->fanClubService->likePost($this->loggedInAccount, $this->post);

        //
        //  Redirect back to the post (which is on the performer's page)
        //
        return $this->redirect($response, $this->post->getPerformer() !== null ? $this->post->getPerformer()->getFanClubPermalink() : '/');
    }

    /**
     * Method to unlike a fan club post
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     * @param int      $postId   The post's ID
     *
     * @throws HttpUnauthorizedException When a user isn't logged in
     *
     * @return Response PSR response
     */
    public function unlike(Request $request, Response $response, int $postId): Response
    {
        $this->loadLoggedInAccount($request);

        if ($this->loggedInAccount === null) {
            throw new HttpUnauthorizedException($request, 'You must be logged in to unlike a post.');
        }

        $this->loadLastUrl($request);

        //
        //  Load the post
        //
        $this->loadPost($request, $postId);

        //
        //  Unlike the post
        //
        assert($this->loggedInAccount instanceof PlatformUser);
        $this->fanClubService->unlikePost($this->loggedInAccount, $this->post);

        //
        //  Redirect back to the post (which is on the performer's page)
        //
        return $this->redirect($response, $this->post->getPerformer() !== null ? $this->post->getPerformer()->getFanClubPermalink() : '/');
    }

    /**
     * Method to comment on a fan club post
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     * @param int      $postId   The post's ID
     *
     * @throws HttpUnauthorizedException When the user isn't logged in
     *
     * @return Response PSR response
     */
    public function comment(Request $request, Response $response, int $postId): Response
    {
        $this->loadLoggedInAccount($request);

        if ($this->loggedInAccount === null) {
            throw new HttpUnauthorizedException($request, 'You must be logged in to comment on a post.');
        }

        $this->loadLastUrl($request);

        //
        //  Load the post
        //
        $this->loadPost($request, $postId);

        //
        //  Comment on the post
        //
        /**
         * @var array<int|string, string> $postData
         */
        $postData = $request->getParsedBody();

        assert($this->loggedInAccount instanceof PlatformUser);
        $this->fanClubService->commentOnPost(
            $this->loggedInAccount,
            $this->post,
            $postData['comment'] ?? '',
        );

        //
        //  Redirect back to the post (which is on the performer's page)
        //
        return $this->redirect($response, $this->post->getPerformer() !== null ? $this->post->getPerformer()->getFanClubPermalink() : '/');
    }

    /**
     * Method to unlock a fan club post
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     * @param int      $postId   The post's ID
     *
     * @throws HttpBadRequestException When the post does not require an unlock
     * @throws HttpUnauthorizedException When a user isn't logged into unlock the post
     *
     * @return Response PSR response
     */
    public function unlock(Request $request, Response $response, int $postId): Response
    {
        $this->loadLoggedInAccount($request);

        if ($this->loggedInAccount === null) {
            throw new HttpUnauthorizedException($request, 'You must be logged in to unlock a post.');
        }

        $this->loadLastUrl($request);

        $this->loadPost($request, $postId);

        //
        //  If the post does not need to be unlocked then show an error
        //
        if ($this->post->getVisibility() !== 'extra') {
            throw new HttpBadRequestException($request, 'This post does not need to be unlocked.');
        }

        //
        //  If it's a post then reactivate the membership
        //
        if ($request->getMethod() === 'POST') {
            try {
                assert($this->loggedInAccount instanceof PlatformUser);
                $this->fanClubService->unlockPost($this->loggedInAccount, $this->post);
            } catch (Exception $e) {
                $this->sendToTemplate('error', $e->getMessage());

                return $this->renderTemplate($response, 'Post/unlock.latte');
            }

            //
            //  Redirect back to the newly unlocked post
            //
            return $this->redirect($response, $this->post->getPermalink());
        }

        //
        //  Load the template
        //
        return $this->renderTemplate($response, 'Post/unlock.latte');
    }

    /**
     * Load a fan club post
     *
     * @param Request $request PSR request
     * @param int     $postId  The post's ID
     *
     * @throws HttpNotFoundException When the post is not found
     *
     * @return void
     */
    private function loadPost(Request $request, int $postId): void
    {
        //
        //  Load the post
        //
        try {
            $this->post = $this->fanClubService->getPostById($postId);
        } catch (Exception $e) {
            throw new HttpNotFoundException($request, 'The post was not found.', $e);
        }

        //
        //  Send the post to the template
        //
        $this->sendToTemplate('post', $this->post);
    }
}
