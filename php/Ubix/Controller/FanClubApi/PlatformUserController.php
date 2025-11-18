<?php

declare(strict_types=1);

namespace Ubix\Controller\FanClubApi;

use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\PlatformUser;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\AccountAuthenticationService;
use Ubix\Service\JsonService;
use Ubix\Service\NotificationService;
use Ubix\Service\PlatformUserService;

/**
 * Controller to handle API calls involving platform users
 *
 * @see \Ubix\Tests\Controller\FanClubApi\PlatformUserControllerTest PHPUnit test case
 */
final class PlatformUserController extends Controller
{
    private PlatformUser $user;

    /**
     * Constructor
     *
     * @param Logger                       $logger                       PSR logger
     * @param TemplateRenderer             $view                         Template renderer
     * @param AccountAuthenticationService $accountAuthenticationService Account authentication service
     * @param JsonService                  $jsonService                  JSON service
     * @param NotificationService          $notificationService          Notification service
     * @param PlatformUserService          $platformUserService          Platform user service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected AccountAuthenticationService $accountAuthenticationService,
        protected JsonService $jsonService,
        protected NotificationService $notificationService,
        protected PlatformUserService $platformUserService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Method to authenticate a user
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @throws HttpBadRequestException When the login fails
     *
     * @return Response PSR response
     */
    public function authenticate(Request $request, Response $response): Response
    {
        /**
         * @var array<int|string, string> $postBody
         */
        $postBody = $this->jsonService->decode($request->getBody()->getContents());

        $ipAddress = $request->getAttribute('normalizedIpAddress') ?? '';
        assert(is_string($ipAddress));

        try {
            $platformUser = $this->accountAuthenticationService->loginAsPlatformUser(
                username:                        $postBody['username'] ?? '',
                password:                        $postBody['password'] ?? '',
                originatingDomain:               'flirt.fans', // In most other cases we would want to pass in `$request->getAttribute('normalizedHost')` but since this is an API we are injecting the product name ('flirt.fans') instead
                ipAddress:                       $ipAddress,
                twoFactorAuthenticationCode:     $postBody['2fa'] ?? '',
                overrideOriginatingDomainForXvt: true,
                creditLoginInRewardsProgram:     false,
                validateDomainFor1clickUsers:    false,
            );
        } catch (Exception $e) {
            if ($this->exceptionContainsCode($e, ExceptionCode::TWO_FACTOR_AUTHENTICATION_MISSING_FOR_PLATFORM_USER->value)) {
                return $this->renderJson($response, [
                    '2faError'     => 'missing',
                    '2faProvider'  => 'google',
                    '2fa_error'    => 'missing', // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
                    '2fa_provider' => 'google', // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
                    'message'      => $e->getMessage(),
                ]);
            } elseif ($this->exceptionContainsCode($e, ExceptionCode::TWO_FACTOR_AUTHENTICATION_INVALID_FOR_PLATFORM_USER->value)) {
                return $this->renderJson($response, [
                    '2faError'     => 'invalid',
                    '2faProvider'  => 'google',
                    '2fa_error'    => 'invalid', // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
                    '2fa_provider' => 'google', // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
                    'message'      => $e->getMessage(),
                ]);
            } elseif ($this->exceptionContainsCode($e, ExceptionCode::TWO_FACTOR_AUTHENTICATION_MISSING_FOR_XVT->value)) {
                return $this->renderJson($response, [
                    '2faError'     => 'missing',
                    '2faProvider'  => 'xvt',
                    '2fa_error'    => 'missing', // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
                    '2fa_provider' => 'xvt', // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
                    'message'      => $e->getMessage(),
                ]);
            } elseif ($this->exceptionContainsCode($e, ExceptionCode::TWO_FACTOR_AUTHENTICATION_INVALID_FOR_XVT->value)) {
                return $this->renderJson($response, [
                    '2faError'     => 'invalid',
                    '2faProvider'  => 'xvt',
                    '2fa_error'    => 'invalid', // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
                    '2fa_provider' => 'xvt', // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
                    'message'      => $e->getMessage(),
                ]);
            } else {
                throw new HttpBadRequestException($request, $e->getMessage());
            }
        }

        //
        //  Render JSON response
        //
        return $this->renderJson($response, $this->userToOutputArray($platformUser));
    }

    /**
     * Method to get a user's details
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     * @param int      $userId   The user's ID
     * @param ?string  $sitekey  The user's sitekey (optional)
     *
     * @return Response PSR response
     */
    public function get(Request $request, Response $response, int $userId, ?string $sitekey = null): Response
    {
        //
        //  Load the user
        //
        $this->loadUser($request, $userId, $sitekey);

        //
        //  Render JSON response
        //
        return $this->renderJson($response, $this->userToOutputArray($this->user));
    }

    /**
     * Method to get users by OAuth account
     *
     * @param Request  $request       PSR request
     * @param Response $response      PSR response
     * @param string   $oauthProvider The OAuth provider
     * @param string   $oauthGuid     The OAuth GUID
     *
     * @throws HttpNotFoundException If no users are found
     *
     * @return Response PSR response
     */
    public function getByOauth(Request $request, Response $response, string $oauthProvider, string $oauthGuid): Response
    {
        //
        //  Load the user(s)
        //
        $users = $this->platformUserService->getByOauth($oauthProvider, $oauthGuid);
        if (count($users) === 0) {
            throw new HttpNotFoundException($request, 'No users found with `' . $oauthProvider . '` as the OAuth provider and `' . $oauthGuid . '` as the OAuth GUID');
        }

        //
        //  Render JSON response
        //
        $output = [];
        foreach ($users as $user) {
            $output[] = $this->userToOutputArray($user);
        }
        return $this->renderJson($response, $output);
    }

    /**
     * Method to notify a user
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     * @param int      $userId   The user's ID
     *
     * @throws HttpBadRequestException If the message fails to send
     *
     * @return Response PSR response
     */
    public function notify(Request $request, Response $response, int $userId): Response
    {
        //
        //  Load the user
        //
        $this->loadUser($request, $userId);

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

        $ipAddress = $request->getAttribute('normalizedIpAddress') ?? '';
        assert(is_string($ipAddress));

        try {
            $this->notificationService->sendMessage(
                title:         $postBody['title'] ?? '',
                body:          $postBody['message'] ?? '',
                recipientType: 'user',
                recipientIds:  $userId,
                senderIp:      $ipAddress,
                senderType:    'system',
            );
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }

        //
        //  Include a 201 HTTP header (Created) to indicate the notification was sent successfully
        //
        $response = $response->withStatus(201);

        //
        //  Render JSON response
        //
        return $this->renderJson($response, [
            'message' => 'You have successfully notified the user with a message.',
            'status'  => 'success',
        ]);
    }

    /**
     * Load a platform user by user ID
     *
     * @param Request $request PSR request
     * @param int     $userId  The user's ID
     * @param ?string $sitekey The user's sitekey (optional)
     *
     * @throws HttpNotFoundException If the user isn't found
     *
     * @return void
     */
    private function loadUser(Request $request, int $userId, ?string $sitekey = null): void
    {
        try {
            $this->user = $this->platformUserService->getById($userId, $sitekey);
        } catch (Exception $e) {
            throw new HttpNotFoundException($request, 'User not found.', $e);
        }
    }

    /**
     * Transform a platform user into an array that is safe to use in output to customers
     *
     * @param PlatformUser $platformUser Platform user
     *
     * @return array<string, mixed> Output that is safe to show to customers
     */
    private function userToOutputArray(PlatformUser $platformUser): array
    {
        return [
            'billNext'        => $platformUser->getBillNext(),
            'bill_next'       => $platformUser->getBillNext(), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'dateCreated'     => $platformUser->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
            'date_created'    => $platformUser->getDateCreated()?->format(DateTime::ISO8601_EXPANDED), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'domain'          => $platformUser->getDomain(),
            'email'           => $platformUser->getEmail(),
            'firstname'       => $platformUser->getFirstname(),
            'lastname'        => $platformUser->getLastname(),
            'sitekey'         => $platformUser->getSitekey(),
            'spendingGroup'   => $platformUser->getSpendingGroup(),
            'spending_group'  => $platformUser->getSpendingGroup(), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'status'          => $platformUser->getStatus(),
            'timeleft'        => $platformUser->getTimeleft(),
            'timeleftBanked'  => $platformUser->getTimeleftBanked(),
            'timeleft_banked' => $platformUser->getTimeleftBanked(), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'totalSpent'      => $platformUser->getTotalSpent(),
            'total_spent'     => $platformUser->getTotalSpent(), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'userId'          => $platformUser->getUserId(),
            'username'        => $platformUser->getUsername(),
            'user_id'         => $platformUser->getUserId(), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
            'vsVip'           => $platformUser->getVsVip(),
            'vs_vip'          => $platformUser->getVsVip(), // NOT_IMPLEMENTED: remove the underscore version once Miro/Quash have confirmed they have switched to the camel case variant
        ];
    }

    /**
     * Determine whether or not an exception contains a specific code (itself of in the previous chain)
     *
     * @param Exception $exception     The exception
     * @param int       $exceptionCode The exception code
     *
     * @return bool Whether or not an exception means we need to show two factor authentication (2fa)
     */
    private function exceptionContainsCode(Exception $exception, int $exceptionCode): bool // NOT_IMPLEMENTED: this is a generally useful method and may be of greater use available somewhere more globally (,aybe in the exception code enum?)
    {
        //
        //  Check the previous exception if one exists
        //
        $previous = $exception->getPrevious();
        if ($previous !== null && $previous instanceof Exception && $this->exceptionContainsCode($previous, $exceptionCode)) {
            return true;
        }

        //
        //  Check the current exception
        //
        return $exception->getCode() === $exceptionCode;
    }
}
