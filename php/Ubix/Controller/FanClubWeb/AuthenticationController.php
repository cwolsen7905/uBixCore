<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubWeb;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\FanClubWeb\AbstractFanClubWebController as FanClubWebController;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\AccountAuthenticationService;
use Ubix\Service\FanClubService;
use Ubix\Service\JsonService;

/**
 * Controller to handle authentication
 *
 * @see \Ubix\Tests\Controller\FanClubWeb\AuthenticationControllerTest PHPUnit test case
 */
final class AuthenticationController extends FanClubWebController
{
    /**
     * Constructor
     *
     * @param Logger                       $logger                       PSR logger
     * @param TemplateRenderer             $view                         Template renderer
     * @param JsonService                  $jsonService                  JSON service
     * @param FanClubService               $fanClubService               Fan club service
     * @param AccountAuthenticationService $accountAuthenticationService Account authentication service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected FanClubService $fanClubService,
        protected AccountAuthenticationService $accountAuthenticationService,
    ) {
        parent::__construct($logger, $view, $jsonService, $fanClubService);
    }

    /**
     * Method to handle logging in as a performer
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function loginAsPerformer(Request $request, Response $response): Response
    {
        //
        //  Determine if we are on the platform user portal or the performer portal
        //
        $this->sendToTemplate('portal', 'performer');

        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        /**
         * @var array<int|string, string> $postData
         */
        $postData = $request->getParsedBody();

        $rememberMe = ($postData['rememberMe'] ?? '') !== '';

        //
        //  Attempt the login
        //
        try {
            $performer = $this->accountAuthenticationService->loginAsPerformer(
                $postData['username'] ?? '',
                $postData['password'] ?? '',
            );
        } catch (Exception $e) {
            $this->sendToTemplate('error', $e->getMessage());
            $this->sendToTemplate('username', $postData['username'] ?? '');
            $this->sendToTemplate('rememberMe', $rememberMe);
            $this->sendToTemplate('lastUrl', $postData['lastUrl'] ?? '');

            return $this->renderTemplate($response, 'Error/login.latte');
        }

        //
        //  Set cookies and session to remember the login
        //
        $response = $this->accountAuthenticationService->setCookiesAndSession($request, $response, $performer, $rememberMe);

        //
        //  Redirect the user back to their last URL
        //
        $lastUrl = $this->safeRedirectUrl($postData['lastUrl'] ?? '');

        return $this->redirect($response, $lastUrl);
    }

    /**
     * Method to handle logging in as a platform user
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function loginAsPlatformUser(Request $request, Response $response): Response
    {
        //
        //  Determine if we are on the platform user portal or the performer portal
        //
        $this->sendToTemplate('portal', 'platformUser');

        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        /**
         * @var array<int|string, string> $postData
         */
        $postData = $request->getParsedBody();

        $domain = $request->getAttribute('normalizedHost') ?? '';
        assert(is_string($domain));

        $ipAddress = $request->getAttribute('normalizedIpAddress') ?? '';
        assert(is_string($ipAddress));

        $rememberMe = ($postData['rememberMe'] ?? '') !== '';

        //
        //  Attempt the login
        //
        try {
            $platformUser = $this->accountAuthenticationService->loginAsPlatformUser(
                username:                        $postData['username'] ?? '',
                password:                        $postData['password'] ?? '',
                originatingDomain:               $domain,
                ipAddress:                       $ipAddress,
                twoFactorAuthenticationCode:     $postData['2fa'] ?? '',
                overrideOriginatingDomainForXvt: true,
                creditLoginInRewardsProgram:     false,
            );
        } catch (Exception $e) {
            $this->sendToTemplate('error', $e->getMessage());
            $this->sendToTemplate('username', $postData['username'] ?? '');
            $this->sendToTemplate('show2fa', $this->show2fa($e));
            $this->sendToTemplate('rememberMe', $rememberMe);
            $this->sendToTemplate('lastUrl', $postData['lastUrl'] ?? '');

            return $this->renderTemplate($response, 'Error/login.latte');
        }

        //
        //  Set cookies and session to remember the login
        //
        $response = $this->accountAuthenticationService->setCookiesAndSession($request, $response, $platformUser, $rememberMe);

        //
        //  Redirect the user back to their last URL
        //
        $lastUrl = $this->safeRedirectUrl($postData['lastUrl'] ?? '');

        return $this->redirect($response, $lastUrl);
    }

    /**
     * Method to handle logging out
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function logout(Request $request, Response $response): Response
    {
        $this->loadLastUrl($request);

        $this->loadLoggedInAccount($request);

        //
        //  Unset the authentication cookies and session
        //
        $response = $this->accountAuthenticationService->unsetCookiesAndSession($request, $response);

        //
        //  Redirect the user back to their last URL
        //
        $lastUrlQueryString = $request->getQueryParams()['lastUrl'] ?? '';
        assert(is_string($lastUrlQueryString));

        $lastUrl = $this->safeRedirectUrl($lastUrlQueryString);

        return $this->redirect($response, $lastUrl);
    }

    /**
     * Do a safety check on a redirect URL
     *
     * @param string $redirectUrl The redirect URL
     * @param string $default     The default to use if the redirect URL is determined to be unsafe (optional) (default: '/')
     *
     * @return string A safe redirect URL
     */
    private function safeRedirectUrl(string $redirectUrl, string $default = '/'): string // NOT_IMPLEMENTED: this method may have broader use and belong elsewhere but I stuck it here for now since this is the only controller using it - potential future DRY violation!
    {
        return str_starts_with($redirectUrl, '/') && !str_starts_with($redirectUrl, '//') ? $redirectUrl : $default; // TEMPORARY: this was a quick hack job - we might want to be doing more verifications
    }

    /**
     * Determine whether or not an exception means we need to show two factor authentication (2fa)
     *
     * @param Exception $exception The exception
     *
     * @return bool Whether or not an exception means we need to show two factor authentication (2fa)
     */
    private function show2fa(Exception $exception): bool
    {
        //
        //  Check the previous exception if one exists
        //
        $previous = $exception->getPrevious();
        if ($previous !== null && $previous instanceof Exception && $this->show2fa($previous)) {
            return true;
        }

        //
        //  Check the current exception
        //
        switch ($exception->getCode()) {
            case ExceptionCode::TWO_FACTOR_AUTHENTICATION_MISSING_FOR_PLATFORM_USER->value:
            case ExceptionCode::TWO_FACTOR_AUTHENTICATION_INVALID_FOR_PLATFORM_USER->value:
            case ExceptionCode::TWO_FACTOR_AUTHENTICATION_MISSING_FOR_XVT->value:
            case ExceptionCode::TWO_FACTOR_AUTHENTICATION_INVALID_FOR_XVT->value:
                return true;
        }

        //
        //  Return false if we've made it this far
        //
        return false;
    }
}
