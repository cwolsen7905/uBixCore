<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Exception;
use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\External\GoogleAuthenticator;
use Ubix\Model\Performer;
use Ubix\Model\PlatformUser;
use Ubix\Model\PlatformUserLoginAttempt;
use Ubix\Model\PlatformUserTwoFactorAuthentication;
use Ubix\Model\PlatformWhiteLabel;
use Ubix\Repository\PendingPlatformUser\PendingPlatformUserReaderInterface as PendingPlatformUserReader;
use Ubix\Repository\Performer\PerformerReaderInterface as PerformerReader;
use Ubix\Repository\PlatformUser\PlatformUserReaderInterface as PlatformUserReader;
use Ubix\Repository\PlatformUser\PlatformUserWriterInterface as PlatformUserWriter;
use Ubix\Repository\PlatformUserLoginAttempt\PlatformUserLoginAttemptWriterInterface as PlatformUserLoginAttemptWriter;
use Ubix\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountReaderInterface as PlatformUserVelocityCountReader;
use Ubix\Repository\PlatformWhiteLabel\PlatformWhiteLabelReaderInterface as PlatformWhiteLabelReader;

/**
 * Service to handle account authentication (logging in and out)
 *
 * @see \Ubix\Tests\Service\AccountAuthenticationServiceTest PHPUnit test case
 */
final class AccountAuthenticationService
{
    private const COOKIE_KEY_FLAG_SUCCESSFUL_LOGIN = 'has_logged_in';
    private const COOKIE_KEY_PASSWORD_MD5          = 'CHAT_PASS_MD5';
    private const COOKIE_KEY_REMEMBER_ME           = 'f4f_username';
    private const COOKIE_KEY_USERNAME              = 'CHAT_USER';

    private const GOOGLE_AUTHENTICATOR_DISCREPANCY_DEFAULT = 2;

    private const SESSION_KEY_LOGGED_IN_PERFORMER     = 'PERFORMER';
    private const SESSION_KEY_LOGGED_IN_PLATFORM_USER = 'PLATFORM_USER';

    private const VELOCITY_CHECK_TIME_LIMIT       = 1200; // 1200 = 60 * 20
    private const VELOCITY_CHECK_IP_ADDRESS_LIMIT = 62;
    private const VELOCITY_CHECK_USERNAME_LIMIT   = 10;

    /**
     * Constructor
     *
     * @param Logger                          $logger                          Logger
     * @param PendingPlatformUserReader       $pendingPlatformUserReader       Pending platform user reader
     * @param PerformerReader                 $performerReader                 Performer reader
     * @param PlatformUserReader              $platformUserReader              Platform user reader
     * @param PlatformUserWriter              $platformUserWriter              Platform user writer
     * @param PlatformUserLoginAttemptWriter  $platformUserLoginAttemptWriter  Platform user login attempt writer
     * @param PlatformUserVelocityCountReader $platformUserVelocityCountReader Platform user velocity count reader
     * @param PlatformWhiteLabelReader        $platformWhiteLabelReader        Platform white label reader
     * @param CookieService                   $cookieService                   Cookie service
     * @param NetworkingService               $networkingService               Networking service
     * @param PlatformUserService             $platformUserService             Platform user service
     * @param XvtService                      $xvtService                      XVT service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private PendingPlatformUserReader $pendingPlatformUserReader,
        private PerformerReader $performerReader,
        private PlatformUserReader $platformUserReader,
        private PlatformUserWriter $platformUserWriter,
        private PlatformUserLoginAttemptWriter $platformUserLoginAttemptWriter,
        private PlatformUserVelocityCountReader $platformUserVelocityCountReader,
        private PlatformWhiteLabelReader $platformWhiteLabelReader,
        private CookieService $cookieService,
        private NetworkingService $networkingService,
        private PlatformUserService $platformUserService,
        private XvtService $xvtService,
    ) {
    }

    /**
     * Attempt to login as a performer
     *
     * @param string $username The username to attempt to login with
     * @param string $password The plaintext password to attempt to login with
     *
     * @throws Exception If no performer is found
     * @throws InvalidArgumentException When an invalid parameter is passed
     *
     * @return Performer Return the performer to login as
     */
    public function loginAsPerformer(string $username, string $password): Performer
    {
        //
        //  Validate parameters
        //
        if (trim($username) === '') {
            throw new InvalidArgumentException('You must enter a username.', ExceptionCode::MISSING_USERNAME_FOR_PERFORMER_LOGIN->value);
        }

        if (trim($password) === '') {
            throw new InvalidArgumentException('You must enter a password.', ExceptionCode::MISSING_PASSWORD_FOR_PERFORMER_LOGIN->value);
        }

        //
        //  Initialize an array of all matches
        //
        $matches = [];

        //
        //  Loop through potential matches
        //
        foreach ($this->performerReader->getByUsername($username) as $performer) {
            //
            //  If the password is valid insert the performer into our matches array
            //
            if ($performer->login($password)) {
                $matches[] = $performer;
            }
        }

        //
        //  If there are no matches throw an exception
        //
        if (count($matches) === 0) {
            throw new Exception('No model account found with that username and password.', ExceptionCode::NO_MATCHES_FOUND_FOR_PERFORMER_LOGIN->value);
        }

        //
        //  Return the first match
        //
        return $matches[0];
    }

    /**
     * Attempt to login as a platform user
     *
     * @param string  $username                        The username to attempt to login with
     * @param string  $password                        The plaintext password to attempt to login with
     * @param string  $originatingDomain               The domain the login is originating from
     * @param string  $ipAddress                       The IP address attempting the login
     * @param ?string $twoFactorAuthenticationCode     The two factor authentication code (optional) (default: null)
     * @param ?string $sitekey                         Limit the login to only this sitekey (optional) (default: null)
     * @param bool    $overrideOriginatingDomainForXvt If set to true the platform user's registration domain will override the $originatingDomain for XVT (optional) (default: false)
     * @param bool    $creditLoginInRewardsProgram     If set to true the login will be credited in the Flirt Rewards program (optional) (default: true)
     * @param bool    $validateDomainFor1clickUsers    If set to true the login attempt will validate the domain for 1click users (optional) (default: true)
     * // NOT_IMPLEMENTED: add a `?string $merchant = null` parameter to this method (and maybe others) - look at the RandyBlue fixes for the OAuth web service in the legacy code as a jumping off point
     *
     * @throws Exception If no platform user is found, the platform user has an invalid status or a velocity check has been hit
     * @throws InvalidArgumentException When an invalid parameter is passed
     *
     * @return PlatformUser Return the platform user to login as
     */
    public function loginAsPlatformUser(
        string $username,
        string $password,
        string $originatingDomain,
        string $ipAddress,
        ?string $twoFactorAuthenticationCode = null,
        ?string $sitekey = null,
        bool $overrideOriginatingDomainForXvt = false,
        bool $creditLoginInRewardsProgram = true,
        bool $validateDomainFor1clickUsers = true,
    ): PlatformUser {
        //
        //  Validate parameters
        //
        if (trim($username) === '') {
            throw new InvalidArgumentException('You must enter a username.', ExceptionCode::MISSING_USERNAME_FOR_PLATFORM_USER_LOGIN->value);
        }

        if (trim($password) === '') {
            throw new InvalidArgumentException('You must enter a password.', ExceptionCode::MISSING_PASSWORD_FOR_PLATFORM_USER_LOGIN->value);
        }

        //
        //  Create an array of all matching platform users
        //
        $matches = [];

        foreach ($this->platformUserReader->getByUsername($username, $sitekey) as $platformUser) {
            try {
                //
                //  Perform a domain check
                //
                $this->domainCheck($platformUser, $originatingDomain, $validateDomainFor1clickUsers);

                //
                //  Perform a velocity check
                //
                $this->velocityCheck($ipAddress, $platformUser);

                //
                //  Attempt the login
                //
                $loginLocally = !$platformUser->getHasXvtAccount() // If the platform user does not have an XVT account then attempt a login with the $platformUser object
                && $platformUser->login($password);

                $loginWithXvtApi = $platformUser->getHasXvtAccount() // If the platform user has an XVT account then attempt to authenticate with the XVT service
                && $this->xvtService->authenticate(
                    username:                    $username,
                    password:                    $password,
                    originatingDomain:           $overrideOriginatingDomainForXvt && $platformUser->getDomain() !== null ? $platformUser->getDomain() : $originatingDomain,
                    ipAddress:                   $ipAddress,
                    twoFactorAuthenticationCode: $twoFactorAuthenticationCode,
                );

                if ($loginLocally || $loginWithXvtApi) {
                    //
                    //  Handle two factor authentication (2fa)
                    //
                    if ($platformUser->getTwoFactorAuthentication() !== null) {
                        if ($twoFactorAuthenticationCode === null || trim($twoFactorAuthenticationCode) === '') {
                            throw new Exception('You must include a two factor authentication code.', ExceptionCode::TWO_FACTOR_AUTHENTICATION_MISSING_FOR_PLATFORM_USER->value);
                        } elseif (!$this->validateTwoFactorAuthenticationCode($platformUser->getTwoFactorAuthentication(), $twoFactorAuthenticationCode)) {
                            throw new Exception('The two factor authentication code submitted is invalid.', ExceptionCode::TWO_FACTOR_AUTHENTICATION_INVALID_FOR_PLATFORM_USER->value);
                        }
                    }

                    //
                    //  Load the platform user's screen name(s) then add to matches
                    //
                    $this->platformUserService->loadScreenNames($platformUser);

                    $matches[] = $platformUser;
                }
            } catch (Exception $e) {
                switch ($e->getCode()) {
                    case ExceptionCode::ONECLICK_DOMAIN_MISMATCH->value:
                        $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_1CLICK_DOMAIN_MISMATCH);
                        break;

                    case ExceptionCode::INACTIVE_DOMAIN->value:
                        $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_INVALID_DOMAIN);
                        break;

                    case ExceptionCode::TWO_FACTOR_AUTHENTICATION_INVALID_FOR_PLATFORM_USER->value:
                    case ExceptionCode::TWO_FACTOR_AUTHENTICATION_MISSING_FOR_PLATFORM_USER->value:
                        $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_2FA_FAILED);
                        break;

                    case ExceptionCode::GDPR_FORGOTTEN_PLATFORM_USER_STATUS->value:
                    case ExceptionCode::INVALID_PLATFORM_USER_STATUS->value:
                        $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_INVALID_STATUS);
                        break;

                    case ExceptionCode::VELOCITY_CHECK_FAILED_DUE_TO_BLOCKED_IP_ADDRESS->value:
                    case ExceptionCode::VELOCITY_CHECK_FAILED_DUE_TO_IP_ADDRESS_LIMIT->value:
                    case ExceptionCode::VELOCITY_CHECK_FAILED_DUE_TO_PRIVATE_IP_ADDRESS->value:
                        $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_IP_ADDRESS_BLOCK);
                        break;

                    case ExceptionCode::VELOCITY_CHECK_FAILED_DUE_TO_USERNAME_LIMIT->value:
                        $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_USERNAME_BLOCK);
                        break;
                }

                throw new Exception($e->getMessage(), ExceptionCode::MATCH_ERROR_FOR_PLATFORM_USER->value, $e);
            }
        }

        //
        //  Check for a pending platform user match
        //
        foreach ($this->pendingPlatformUserReader->getByUsername($username) as $pendingPlatformUser) {
            //
            //  If the password is valid insert the performer into our matches array
            //
            if ($pendingPlatformUser->login($password)) {
                $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_PENDING_CONFIRMATION);

                throw new Exception('Your account is pending confirmation.', ExceptionCode::PLATFORM_USER_IS_PENDING->value);
            }
        }

        //
        //  If there are no matches throw an exception
        //
        if (count($matches) === 0) {
            $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_INCORRECT_PASSWORD);

            throw new Exception('No user account found with that username and password.', ExceptionCode::NO_MATCHES_FOUND_FOR_PLATFORM_USER_LOGIN->value);
        }

        //
        //  The login was successful so record and return the first match
        //
        //  NOTE:
        //      This emulates fan club 1.0 behaviour which is the only product, at the time, that allowed logins across all sitekeys (on all other sites we are limiting logins to a single sitekey, fan club is unique in that it allows logins from any sitekey which may result in multiple matches)
        //      If multiple accounts match then we just use the first one and disregard the others
        //      If we want to do any further processing to either show multiple options for a user to select or add logic to determine which one to use it would go here - if( count($matches) > 1 ) { /* ... */ }
        //
        $this->logPlatformUserLoginAttempt($username, $ipAddress, $originatingDomain, $sitekey, PlatformUserLoginAttempt::FAIL_CODE_SUCCESS);

        //
        //  Update the platform user's last login date
        //
        $matches[0]->setDateLastLogin(new DateTime());

        $this->platformUserWriter->save($matches[0]);

        //
        //  Credit the login in Flirt Rewards
        //
		// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf -- Disable violation for the empty if statement until it is implemented
        if ($creditLoginInRewardsProgram) {
			// phpcs:disable Squiz.PHP.CommentedOutCode.Found

            /*
                // NOT_IMPLEMENTED: We need to replicate this reward activity tracking from the legacy system (see code snippet below) most likely with an event/queuing system like RabbitMQ

                $ACTIVITY_TYPE = FlirtRewards::getActivityType('login_daily_f4f');

                $FLIRT_OPTIONS = [
                    'user_id'          => $OPTIONS['user_id'],
                    'model_id'         => 0,
                    'activity_type_id' => $ACTIVITY_TYPE['id'],
                    'activity_ref_id'  => 0,
                    'num_points'       => 10,
                    'date_earned'      => date('Y-m-d'),
                    'datetime_earned'  => date('Y-m-d H:i:s'),
                ];

                FlirtRewards::editActivity($FLIRT_OPTIONS);
            */

			// phpcs:enable Squiz.PHP.CommentedOutCode.Found
        }

        //
        //  Record the login in BigQuery
        //
        // NOT_IMPLEMENTED: we need to log this login to BigQuery, look for `BigQuery logging of user devices - Task #395448` in /home/webservices/vs3.com/ws/users/login.php
        return $matches[0];
    }

    /**
     * Get the currently logged in account
     *
     * @param Request $request              PSR request
     * @param bool    $performersEnabled    Whether or not to check for logged in performers (optional) (default: false)
     * @param bool    $platformUsersEnabled Whether or not to check for logged in platform users (optional) (default: false)
     *
     * @return Performer|PlatformUser|null Return the logged in account if found or null if none is found
     */
    public function getLoggedInAccount(
        Request $request,
        bool $performersEnabled = false,
        bool $platformUsersEnabled = false,
    ): Performer|PlatformUser|null {
        //
        //  We store the account object in session, if it's there then return it
        //
        if ($performersEnabled && !empty($_SESSION[self::SESSION_KEY_LOGGED_IN_PERFORMER]) && is_object($_SESSION[self::SESSION_KEY_LOGGED_IN_PERFORMER]) && get_class($_SESSION[self::SESSION_KEY_LOGGED_IN_PERFORMER]) === Performer::class) {
            return $_SESSION[self::SESSION_KEY_LOGGED_IN_PERFORMER];
        } elseif ($platformUsersEnabled && !empty($_SESSION[self::SESSION_KEY_LOGGED_IN_PLATFORM_USER]) && is_object($_SESSION[self::SESSION_KEY_LOGGED_IN_PLATFORM_USER]) && get_class($_SESSION[self::SESSION_KEY_LOGGED_IN_PLATFORM_USER]) === PlatformUser::class) {
            // NOT_IMPLEMENTED: Open bug here... the session variable also stores the platform user's `timeleft` and doesn't check if it has changed (possibly in another simultaneous session) so we may accidentally approve a transaction when the user doesn't actually have enough credits. Figure out a way to check the timeleft balance either once per page load (slow) or, at least, immediately before transactions occur. Keep in mind this is a general problem of data stored in session going stale so consider a broad solution for that problem.
            return $_SESSION[self::SESSION_KEY_LOGGED_IN_PLATFORM_USER];
        } elseif (
            !empty($request->getCookieParams()[self::COOKIE_KEY_USERNAME])
            && !empty($request->getCookieParams()[self::COOKIE_KEY_PASSWORD_MD5])
            && !empty($request->getCookieParams()[self::COOKIE_KEY_REMEMBER_ME])
        ) {
            //
            //  If the account object is not in session we should attempt to AutoLogin and restore the session so long as we have these cookies...
            //      1. Username
            //      2. Password (MD5 hash of the password column, not the actual password)
            //      3. Remember me
            //
            $cookieParams = $request->getCookieParams();
            assert(is_array($cookieParams));

            if (is_string($cookieParams[self::COOKIE_KEY_USERNAME]) && is_string($cookieParams[self::COOKIE_KEY_PASSWORD_MD5])) {
                return $this->getAutoLoginMatch(
                    $cookieParams[self::COOKIE_KEY_USERNAME],
                    $cookieParams[self::COOKIE_KEY_PASSWORD_MD5],
                    $performersEnabled,
                    $platformUsersEnabled,
                );
            }
        }

        //
        //  No logged in account found so return null
        //
        return null;
    }

    /**
     * Set all cookies and session variables for an account to log them in and keep them logged in
     *
     * @param Request                $request    PSR request
     * @param Response               $response   PSR response
     * @param Performer|PlatformUser $account    The account to set cookies and session variables for
     * @param bool                   $rememberMe Whether or not the account has opted into the "remember me" feature
     *
     * @return Response PSR response
     */
    public function setCookiesAndSession(
        Request $request,
        Response $response,
        Performer|PlatformUser $account,
        bool $rememberMe = false,
    ): Response { // NOT_IMPLEMENTED: These cookies have not been deployed to production and were stubbed up based on the FanClub 1.0 cookies - please review before using in production as some may be wrong or unneeded
        //
        //  Set cookies
        //
        $response = $this->cookieService->set(
            $response,
            self::COOKIE_KEY_USERNAME,
            $account->getUsername() ?? '',
            (int)(new DateTime())->modify('+86400 seconds')->format('U'), // 86400 = 60 * 60 * 24
            $this->cookieService->getDefaultPath(),
            $this->cookieService->getDefaultDomain($request),
            $this->cookieService->getDefaultSecure(),
            $this->cookieService->getDefaultHttpOnly($request),
        );

        $response = $this->cookieService->set(
            $response,
            self::COOKIE_KEY_PASSWORD_MD5,
            md5($account->getPassword() ?? ''),
            (int)(new DateTime())->modify('+86400 seconds')->format('U'), // 86400 = 60 * 60 * 24
            $this->cookieService->getDefaultPath(),
            $this->cookieService->getDefaultDomain($request),
            $this->cookieService->getDefaultSecure(),
            $this->cookieService->getDefaultHttpOnly($request),
        );

        $response = $this->cookieService->set(
            $response,
            self::COOKIE_KEY_FLAG_SUCCESSFUL_LOGIN,
            '_',
            (int)(new DateTime())->modify('+1520640 seconds')->format('U'), // 1520640 was copy/pasted from the legacy codebase
            $this->cookieService->getDefaultPath(),
            $this->cookieService->getDefaultDomain($request),
            $this->cookieService->getDefaultSecure(),
            $this->cookieService->getDefaultHttpOnly($request),
        );

        if ($rememberMe) {
            $response = $this->cookieService->set(
                $response,
                self::COOKIE_KEY_REMEMBER_ME,
                $account->getUsername() ?? '',
                (int)(new DateTime())->modify('+1520640 seconds')->format('U'), // 1520640 was copy/pasted from the legacy codebase
                $this->cookieService->getDefaultPath(),
                $this->cookieService->getDefaultDomain($request),
                $this->cookieService->getDefaultSecure(),
                $this->cookieService->getDefaultHttpOnly($request),
            );
        } elseif (isset($request->getCookieParams()[self::COOKIE_KEY_REMEMBER_ME])) {
            $response = $this->cookieService->delete(
                $response,
                self::COOKIE_KEY_REMEMBER_ME,
                $this->cookieService->getDefaultPath(),
                $this->cookieService->getDefaultDomain($request),
            );
        }

        //
        //  Store account object in session
        //
        switch (get_class($account)) {
            case Performer::class:
                $_SESSION[self::SESSION_KEY_LOGGED_IN_PERFORMER] = $account;

                break;

            case PlatformUser::class:
                $_SESSION[self::SESSION_KEY_LOGGED_IN_PLATFORM_USER] = $account;

                break;
        }

        //
        //  Return the updated response
        //
        return $response;
    }

    /**
     * Unset all cookies and session variables for logged in accounts
     *
     * @param Request  $request  PSR request
     * @param Response $response PSR response
     *
     * @return Response PSR response
     */
    public function unsetCookiesAndSession(Request $request, Response $response): Response
    {
        //
        //  Unset cookies
        //
        $keysToUnset = [
            self::COOKIE_KEY_USERNAME,
            self::COOKIE_KEY_PASSWORD_MD5,
            self::COOKIE_KEY_REMEMBER_ME,
        ];
        foreach ($keysToUnset as $keyToUnset) {
            $response = $this->cookieService->delete(
                $response,
                $keyToUnset,
                $this->cookieService->getDefaultPath(),
                $this->cookieService->getDefaultDomain($request),
            );
        }

        //
        //  Unset session
        //
        $keysToUnset = [
            self::SESSION_KEY_LOGGED_IN_PERFORMER,
            self::SESSION_KEY_LOGGED_IN_PLATFORM_USER,
        ];
        foreach ($keysToUnset as $keyToUnset) {
            unset($_SESSION[$keyToUnset]);
        }

        //
        //  Return the updated response
        //
        return $response;
    }

    /**
     * @param PlatformUserTwoFactorAuthentication $twoFactorAuthentication The 2fa object
     * @param string                              $code                    The 2fa code
     *
     * @throws LogicException If SMS 2fa is used [because it is not implemented]
     *
     * @return bool Whether or not the 2fa code is valid
     */
    public function validateTwoFactorAuthenticationCode(
        PlatformUserTwoFactorAuthentication $twoFactorAuthentication,
        string $code,
    ): bool {
        switch ($twoFactorAuthentication->getAuthMethod()) {
            case 'Google Authenticator':
                assert(is_string($twoFactorAuthentication->getAuthValue()));

                $googleAuthenticator = new GoogleAuthenticator();

                return $googleAuthenticator->verifyCode(
                    $twoFactorAuthentication->getAuthValue(),
                    $code,
                    self::GOOGLE_AUTHENTICATOR_DISCREPANCY_DEFAULT,
                );

            default:
                throw new LogicException('ALTERNATIVE TWO FACTOR AUTHENTICATION METHODS ARE NOT IMPLEMENTED.'); // NOT_IMPLEMENTED: needs to be done (* or perhaps we should just drop the "SMS" option from the database schema as only Google Authenticator is active)
        }
    }

    /**
     * Return the account that is currently logged in with AutoLogin
     *
     * @param string $username             The username
     * @param string $passwordMd5          The password (as an MD5 hash)
     * @param bool   $performersEnabled    Whether or not to check for logged in performers
     * @param bool   $platformUsersEnabled Whether or not to check for logged in platform users
     *
     * @return Performer|PlatformUser|null Returns either a platform user or performer if one is logged in or null if no account is found
     */
    private function getAutoLoginMatch(
        string $username,
        string $passwordMd5,
        bool $performersEnabled = false,
        bool $platformUsersEnabled = false,
    ): Performer|PlatformUser|null {
        // NOT_IMPLEMENTED: There has been a significant amount of new processing added to $this->loginAsPlatformUser(), some of which we may want to replicate in this method before production usage
        //
        //  Attempt to find a performer that matches
        //
        if ($performersEnabled) {
            $performers = $this->performerReader->getForAutoLogin($username, $passwordMd5);
            foreach ($performers as $performer) {
                if ($performer->autoLogin()) {
                    return $performer;
                }
            }
        }

        //
        //  Attempt to find a platform user that matches
        //
        if ($platformUsersEnabled) {
            $platformUsers = $this->platformUserReader->getForAutoLogin($username, $passwordMd5);
            foreach ($platformUsers as $platformUser) {
                if ($platformUser->autoLogin()) {
                    //
                    //  Load the platform user's screen name(s) then return
                    //
                    $this->platformUserService->loadScreenNames($platformUser);

                    return $platformUser;
                }
            }
        }

        //
        //  No match found so return null
        //
        return null;
    }

    /**
     * Perform a velocity check
     *
     * @param string                 $ipAddress The IP address
     * @param Performer|PlatformUser $account   The account
     *
     * @throws Exception If the velocity check fails
     * @throws LogicException If unimplemented features are accessed
     *
     * @return void
     */
    private function velocityCheck(string $ipAddress, Performer|PlatformUser $account): void
    {
        //
        //  If the IP address is internal then always treat the velocity check as a success
        //
        if ($this->networkingService->isInternalIpAddress($ipAddress)) {
            return;
        }

        //
        //  If the IP address is blocked then throw an exception
        //
        if ($this->networkingService->isBlockedIpAddress($ipAddress)) {
            throw new Exception('Your IP address has been blocked.', ExceptionCode::VELOCITY_CHECK_FAILED_DUE_TO_BLOCKED_IP_ADDRESS->value);
        }

        //
        //  If the IP address is in a private range then throw an exception
        //
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            throw new Exception('Your IP address is in a private range and not allowed to be used.', ExceptionCode::VELOCITY_CHECK_FAILED_DUE_TO_PRIVATE_IP_ADDRESS->value);
        }

        //
        //  Check velocity counts based on account type
        //
        switch (get_class($account)) {
            case Performer::class:
                throw new LogicException('PERFORMER VELOCITY CHECKS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done

            case PlatformUser::class:
                $velocityCount = $this->platformUserVelocityCountReader->get(
                    $ipAddress,
                    $account->getUsername() ?? '',
                    self::VELOCITY_CHECK_TIME_LIMIT,
                    self::VELOCITY_CHECK_IP_ADDRESS_LIMIT,
                    self::VELOCITY_CHECK_USERNAME_LIMIT,
                );

                $ipAddressCount = count($velocityCount) > 0 ? $velocityCount[0]->getIpAddressCount() : 0;
                if ($ipAddressCount >= self::VELOCITY_CHECK_IP_ADDRESS_LIMIT) {
                    throw new Exception('You have tried to login too many times in a row. Please take a break and come back in a little while.', ExceptionCode::VELOCITY_CHECK_FAILED_DUE_TO_IP_ADDRESS_LIMIT->value);
                }

                $usernameCount = count($velocityCount) > 0 ? $velocityCount[0]->getUsernameCount() : 0;
                if ($usernameCount >= self::VELOCITY_CHECK_USERNAME_LIMIT) {
                    throw new Exception('You have tried to login too many times in a row. Please take a break and come back in a little while.', ExceptionCode::VELOCITY_CHECK_FAILED_DUE_TO_USERNAME_LIMIT->value);
                }

                break;
        }
    }

    /**
     * Log a platform user login attempt
     *
     * @param string  $username          The username
     * @param string  $ipAddress         The IP address
     * @param string  $originatingDomain The originating domain
     * @param ?string $sitekey           The sitekey
     * @param int     $failCode          The fail code
     *
     * @return void
     */
    private function logPlatformUserLoginAttempt(string $username, string $ipAddress, string $originatingDomain, ?string $sitekey, int $failCode): void
    {
        $platformUserLoginAttempt = new PlatformUserLoginAttempt(
            username:  $username,
            password:  'AccountAuthenticationService',
            ipAddress: $ipAddress,
            datetime:  new DateTime(),
            domain:    $originatingDomain,
            failCode:  $failCode,
            sitekey:   $sitekey ?? '',
            loginType: PlatformUserLoginAttempt::LOGIN_TYPE_PROJECT_NEPTUNE,
        );

        $this->platformUserLoginAttemptWriter->save($platformUserLoginAttempt);
    }

    /**
     * Validate the domain
     *
     * @param PlatformUser $platformUser                 The platform user
     * @param string       $originatingDomain            The originating domain
     * @param bool         $validateDomainFor1clickUsers If set to true then validate the domain for 1click users
     *
     * @throws Exception If there is a problem with the domain being checked
     *
     * @return void
     */
    private function domainCheck(
        PlatformUser $platformUser,
        string $originatingDomain,
        bool $validateDomainFor1clickUsers,
    ): void {
        $platformWhiteLabels = $this->platformWhiteLabelReader->getByDomain($originatingDomain);
        if (count($platformWhiteLabels) > 0) {
            //
            //  If the originating domain exists and is a status other than active throw an exception
            //
            if ($platformWhiteLabels[0]->getStatus() !== PlatformWhiteLabel::STATUS_ACTIVE) {
                throw new Exception('Invalid domain supplied: `' . ($platformUser->getPlatformWhiteLabel()?->getDomain() ?? 'null') . '`', ExceptionCode::INACTIVE_DOMAIN->value);
            }

            //
            //  If the originating domain exists and is a status other than active throw an exception
            //
            if (
                $validateDomainFor1clickUsers
                && $platformUser->getOneclickProcessorId() !== null
                && $platformUser->getOneclickProcessorId() > 0
                && $platformWhiteLabels[0]->getSitekey() !== null
                && $platformWhiteLabels[0]->getSitekey() !== 'flirt4free'
                && $platformWhiteLabels[0]->getDomain() !== null
                && $platformWhiteLabels[0]->getDomain() !== $platformUser->getPlatformWhiteLabel()?->getDomain()
            ) {
                throw new Exception(
                    'The username you are trying to login with is associated with a payment account restricted to another site. Please create a new account to login to this site.',
                    ExceptionCode::ONECLICK_DOMAIN_MISMATCH->value,
                );
            }
        }
    }
}
