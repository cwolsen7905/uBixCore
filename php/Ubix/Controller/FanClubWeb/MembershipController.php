<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubWeb;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Ubix\Controller\FanClubWeb\AbstractFanClubWebController as FanClubWebController;
use Ubix\Model\FanClubMembership;
use Ubix\Model\PlatformUser;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\FanClubService;
use Ubix\Service\JsonService;

/**
 * Controller for interacting with a membership
 *
 * @see \Ubix\Tests\Controller\FanClubWeb\MembershipControllerTest PHPUnit test case
 */
final class MembershipController extends FanClubWebController
{
    private FanClubMembership $membership;

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
     * Method to reactivate a membership
     *
     * @param Request  $request      PSR request
     * @param Response $response     PSR response
     * @param int      $membershipId The membership's ID
     *
     * @throws HttpBadRequestException If a membership isn't canceled or expired to be reactivated
     *
     * @return Response PSR response
     */
    public function reactivate(Request $request, Response $response, int $membershipId): Response
    {
        $this->loadLoggedInAccount($request);

        $this->loadLastUrl($request);

        $this->loadMembership($membershipId);

        if ($this->membership->getStatus() !== 'expired' && $this->membership->getStatus() !== 'canceled') {
            throw new HttpBadRequestException($request, 'The membership is not currently canceled or expired so can not be reactivated.');
        }

        //
        //  If it's a post then reactivate the membership
        //
        if ($request->getMethod() === 'POST') {
            $this->fanClubService->reactivateMembership($this->membership);

            //
            //  Redirect back to the homepage
            //
            return $this->redirect($response, '/');
        }

        //
        //  Load the template
        //
        return $this->renderTemplate($response, 'Membership/reactivate.latte');
    }

    /**
     * Method to cancel a membership
     *
     * @param Request  $request      PSR request
     * @param Response $response     PSR response
     * @param int      $membershipId The membership's ID
     *
     * @throws HttpBadRequestException If a membership isn't active to be cancelled
     *
     * @return Response PSR response
     */
    public function cancel(Request $request, Response $response, int $membershipId): Response
    {
        $this->loadLoggedInAccount($request);

        $this->loadLastUrl($request);

        $this->loadMembership($membershipId);

        if ($this->membership->getStatus() !== 'active') {
            throw new HttpBadRequestException($request, 'The membership is not currently active so can not be canceled.');
        }

        //
        //  If it's a post then reactivate the membership
        //
        if ($request->getMethod() === 'POST') {
            //
            //  Cancel the membership
            //
            $this->fanClubService->cancelMembership($this->membership);

            //
            //  Redirect back to the homepage
            //
            return $this->redirect($response, '/');
        }

        //
        //  Load the template
        //
        return $this->renderTemplate($response, 'Membership/cancel.latte');
    }

    /**
     * Attempt to load a membership
     *
     * @param int $membershipId The ID of the membership
     *
     * @return void
     */
    private function loadMembership(int $membershipId): void
    {
        //
        //  Load the membership
        //
        $this->membership = $this->fanClubService->getMembershipForUserById(
            $this->loggedInAccount instanceof PlatformUser ? $this->loggedInAccount : null,
            $membershipId,
        );

        //
        //  Pass the membership to the template
        //
        $this->sendToTemplate('membership', $this->membership);
    }
}
