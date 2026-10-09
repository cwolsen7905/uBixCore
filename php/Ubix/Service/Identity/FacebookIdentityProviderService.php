<?php

declare(strict_types=1);

namespace Ubix\Service\Identity;

use DateTime;
use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Throwable;
use Ubix\DataTransferObject\Identity\BeginAuthorizationRequest;
use Ubix\DataTransferObject\Identity\CallbackParameters;
use Ubix\DataTransferObject\Identity\PendingAuthorization;
use Ubix\DataTransferObject\Identity\VerifiedIdentity;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Identity\IdentityProvider;
use Ubix\Exception\DtoException;
use Ubix\Service\Identity\IdentityProviderServiceInterface as IdentityProviderService;
use Ubix\Service\JsonService;

/**
 * "Continue with Facebook": OAuth 2.0 authorization-code flow (web Facebook Login is not OpenID Connect)
 *
 * | Env var                      | Meaning                                         | Source |
 * |------------------------------|-------------------------------------------------|--------|
 * | `FACEBOOK_APP_ID`            | The Meta app id                                 | uBixVault in every deployed environment; `.env` only for local dev |
 * | `FACEBOOK_APP_SECRET`        | Its app secret                                  | uBixVault |
 * | `FACEBOOK_GRAPH_API_VERSION` | Graph API version, e.g. `v23.0` (optional)      | config; defaults to {@see self::DEFAULT_GRAPH_API_VERSION} |
 *
 * Constructor arguments override the environment (tests, or a host that wires
 * credentials explicitly). With no app id or secret, every call fails closed.
 *
 * Because there is no signed ID token, what proves the identity is a chain of
 * server-side calls, each made with `appsecret_proof` (HMAC-SHA256 of the
 * access token, keyed with the app secret) so a leaked token alone cannot drive them:
 *
 * 1. the code is exchanged for a user access token (with the PKCE verifier);
 * 2. `debug_token`, called with the **app** access token, must report that the
 *    user token is valid, unexpired, of type `USER`, and issued to **our** app
 *    id -- a token minted for another app is refused before anything is read;
 * 3. `/me` gives the profile, and its `id` must equal the user id `debug_token` reported.
 *
 * Facebook makes **no assertion that an email is verified**, so `emailVerified`
 * is always false here, and `email` is empty when the person signed up by
 * phone or declined the `email` permission. Whether such an identity may sign
 * in, sign up or must confirm an address is the host's decision.
 *
 * `state` plus the flow cookie ({@see AuthorizationFlowService}) carry CSRF
 * protection exactly as for Google. PKCE (S256) is sent and honoured by
 * Facebook, but not relied on. The flow's `nonce` is generated so every
 * {@see PendingAuthorization} has one, yet it has no role in this flow:
 * a nonce binds an ID token, and Facebook's web login returns none.
 *
 * Provider → server notices (Meta's data-deletion and deauthorize callbacks)
 * are verified by {@see FacebookSignedRequestParserService}.
 *
 * @see \Ubix\Tests\Service\Identity\FacebookIdentityProviderServiceTest PHPUnit test case
 */
final class FacebookIdentityProviderService implements IdentityProviderService
{
    public const DEFAULT_GRAPH_API_VERSION = 'v23.0';

    private const DIALOG_HOST   = 'https://www.facebook.com';
    private const GRAPH_HOST    = 'https://graph.facebook.com';
    private const PROFILE_FIELD = 'id,email,first_name,last_name';
    private const SCOPES        = 'email,public_profile';
    private const UNAVAILABLE   = 'Facebook sign-in is not available.';
    private const UNFINISHED    = 'That sign-in didn\'t complete. Please try again.';

    /**
     * Constructor
     *
     * @param Logger         $logger          The Monolog logger
     * @param HttpClient     $httpClient      PSR-18 client for the Graph API
     * @param RequestFactory $requestFactory  PSR-17 request factory
     * @param JsonService    $jsonService     JSON codec
     * @param string         $appId           Meta app id, or empty to read `FACEBOOK_APP_ID`
     * @param string         $appSecret       Meta app secret, or empty to read `FACEBOOK_APP_SECRET`
     * @param string         $graphApiVersion Graph API version (`v23.0`), or empty to read `FACEBOOK_GRAPH_API_VERSION`, else the default
     */
    public function __construct(
        private Logger $logger,
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private JsonService $jsonService,
        private string $appId = '',
        private string $appSecret = '',
        private string $graphApiVersion = '',
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getProvider(): IdentityProvider
    {
        return IdentityProvider::FACEBOOK;
    }

    /**
     * {@inheritDoc}
     *
     * `loginHint` is ignored: Facebook's login dialog has no equivalent.
     *
     * Fails closed (a `DtoException`) when no app id is configured.
     */
    public function beginAuthorization(BeginAuthorizationRequest $request): PendingAuthorization
    {
        $appId        = $this->getAppId();
        $state        = bin2hex(random_bytes(32));
        $nonce        = bin2hex(random_bytes(32));
        $codeVerifier = $this->base64UrlEncode(random_bytes(32));

        $query = [
            'client_id'             => $appId,
            'code_challenge'        => $this->base64UrlEncode(hash('sha256', $codeVerifier, true)),
            'code_challenge_method' => 'S256',
            'redirect_uri'          => $request->redirectUri,
            'response_type'         => 'code',
            'scope'                 => self::SCOPES,
            'state'                 => $state,
        ];

        return new PendingAuthorization(
            provider:               IdentityProvider::FACEBOOK,
            authorizationUrl:       self::DIALOG_HOST . '/' . $this->getGraphApiVersion() . '/dialog/oauth?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986),
            state:                  $state,
            nonce:                  $nonce,
            codeVerifier:           $codeVerifier,
            redirectUri:            $request->redirectUri,
            returnTo:               $request->returnTo,
            hostContext:            $request->hostContext,
            createdAtUnixTimestamp: (new DateTime())->getTimestamp(),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException When the user declined, or the exchange or verification failed
     */
    public function completeAuthorization(CallbackParameters $parameters, PendingAuthorization $pending): VerifiedIdentity
    {
        if ($pending->provider !== IdentityProvider::FACEBOOK) {
            throw new DtoException(self::UNFINISHED, ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        if ($parameters->error !== '') {
            $this->logger->info('Facebook sign-in declined', ['error' => substr($parameters->error, 0, 64)]);

            throw new DtoException('Sign-in with Facebook was cancelled.', ExceptionCode::IDENTITY_AUTHORIZATION_DENIED->value);
        }

        if ($parameters->code === '') {
            throw new DtoException(self::UNFINISHED, ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        $accessToken = $this->exchangeCode($parameters->code, $pending);
        $userId      = $this->inspectToken($accessToken);
        $profile     = $this->graphGet('/me', ['fields' => self::PROFILE_FIELD], $accessToken, 'profile');

        $subject = is_string($profile['id'] ?? null) ? $profile['id'] : '';
        if ($subject === '' || !hash_equals($userId, $subject)) {
            $this->logger->warning('Facebook profile does not belong to the inspected token');

            throw new DtoException(self::UNFINISHED, ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        }

        return new VerifiedIdentity(
            provider:            IdentityProvider::FACEBOOK,
            subject:             $subject,
            email:               is_string($profile['email'] ?? null) ? $profile['email'] : '',
            emailVerified:       false,
            emailIsPrivateRelay: false,
            givenName:           is_string($profile['first_name'] ?? null) ? $profile['first_name'] : '',
            familyName:          is_string($profile['last_name'] ?? null) ? $profile['last_name'] : '',
        );
    }

    /**
     * Exchange an authorization code for a user access token
     *
     * @param string               $code    The authorization code
     * @param PendingAuthorization $pending The flow, for the redirect URI and PKCE verifier
     *
     * @return string The access token (not yet inspected)
     *
     * @throws DtoException When Facebook is unreachable or refuses the exchange
     */
    private function exchangeCode(string $code, PendingAuthorization $pending): string
    {
        $decoded = $this->graphGet('/oauth/access_token', [
            'client_id'     => $this->getAppId(),
            'client_secret' => $this->getAppSecret(),
            'code'          => $code,
            'code_verifier' => $pending->codeVerifier,
            'redirect_uri'  => $pending->redirectUri,
        ], '', 'code exchange');

        if (!is_string($decoded['access_token'] ?? null) || $decoded['access_token'] === '') {
            $this->logger->warning('Facebook code exchange returned no access token');

            throw new DtoException(self::UNFINISHED, ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $decoded['access_token'];
    }

    /**
     * Inspect a user access token with `debug_token`, as our app
     *
     * @param string $accessToken The user access token
     *
     * @return string The Facebook user id the token belongs to
     *
     * @throws DtoException When the token is invalid, expired, not a user token, or issued to another app
     */
    private function inspectToken(string $accessToken): string
    {
        $decoded = $this->graphGet(
            '/debug_token',
            ['input_token' => $accessToken],
            $this->getAppId() . '|' . $this->getAppSecret(),
            'token inspection',
        );

        $data      = is_array($decoded['data'] ?? null) ? $decoded['data'] : [];
        $appId     = is_scalar($data['app_id'] ?? null) ? (string) $data['app_id'] : '';
        $userId    = is_scalar($data['user_id'] ?? null) ? (string) $data['user_id'] : '';
        $expiresAt = is_int($data['expires_at'] ?? null) ? $data['expires_at'] : 0;

        $reason = match (true) {
            $appId === '' || !hash_equals($this->getAppId(), $appId) => 'issued to another app',
            ($data['is_valid'] ?? false) !== true                    => 'not valid',
            ($data['type'] ?? '') !== 'USER'                         => 'not a user token',
            $expiresAt <= (new DateTime())->getTimestamp()           => 'expired',
            $userId === ''                                           => 'no user id',
            default                                                  => '',
        };

        if ($reason !== '') {
            $this->logger->warning('Facebook access token refused', ['reason' => $reason]);

            throw new DtoException(self::UNFINISHED, ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        }

        return $userId;
    }

    /**
     * Make a Graph API GET call, with `appsecret_proof` whenever an access token is sent
     *
     * The access token travels in the `Authorization` header rather than the
     * query string, so it does not land in access logs of anything in between.
     *
     * @param string                $path        Path below the version, e.g. `/me`
     * @param array<string, string> $query       Query parameters
     * @param string                $accessToken The access token to authenticate with, or empty for none
     * @param string                $operation   What the call is, for logs
     *
     * @return array<int|string, mixed> The decoded response
     */
    private function graphGet(string $path, array $query, string $accessToken, string $operation): array
    {
        if ($accessToken !== '') {
            $query['appsecret_proof'] = hash_hmac('sha256', $accessToken, $this->getAppSecret());
        }

        $request = $this->requestFactory->createRequest(
            'GET',
            self::GRAPH_HOST . '/' . $this->getGraphApiVersion() . $path . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986),
        )->withHeader('Accept', 'application/json');

        if ($accessToken !== '') {
            $request = $request->withHeader('Authorization', 'Bearer ' . $accessToken);
        }

        return $this->send($request, $operation);
    }

    /**
     * Send a Graph request and decode its JSON answer
     *
     * @param Request $request   The request
     * @param string  $operation What the call is, for logs
     *
     * @return array<int|string, mixed> The decoded response
     *
     * @throws DtoException When Facebook is unreachable or answers with an error
     */
    private function send(Request $request, string $operation): array
    {
        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientException $e) {
            $this->logger->error('Facebook Graph API unreachable', ['operation' => $operation, 'reason' => $e->getMessage()]);

            throw new DtoException('Facebook sign-in is unavailable right now. Please try again.', ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value, null, $e);
        }

        try {
            $decoded = $this->jsonService->decode((string) $response->getBody());
        } catch (Throwable $e) {
            $this->logger->info('Facebook Graph response was not JSON', ['operation' => $operation, 'reason' => $e->getMessage()]);
            $decoded = [];
        }

        if ($response->getStatusCode() !== 200 || isset($decoded['error'])) {
            $error = is_array($decoded['error'] ?? null) ? $decoded['error'] : [];
            $this->logger->warning('Facebook Graph API refused a call', [
                'code'      => is_int($error['code'] ?? null) ? $error['code'] : 0,
                'operation' => $operation,
                'status'    => $response->getStatusCode(),
                'type'      => is_string($error['type'] ?? null) ? $error['type'] : '',
            ]);

            throw new DtoException(self::UNFINISHED, ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $decoded;
    }

    /**
     * Get the app id, failing closed when none is configured
     *
     * @return string The app id
     *
     * @throws DtoException When none is configured
     */
    private function getAppId(): string
    {
        $appId = $this->appId !== '' ? $this->appId : (string) getenv('FACEBOOK_APP_ID');
        if ($appId === '') {
            throw new DtoException(self::UNAVAILABLE, ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $appId;
    }

    /**
     * Get the app secret, failing closed when none is configured
     *
     * @return string The app secret
     *
     * @throws DtoException When none is configured
     */
    private function getAppSecret(): string
    {
        $appSecret = $this->appSecret !== '' ? $this->appSecret : (string) getenv('FACEBOOK_APP_SECRET');
        if ($appSecret === '') {
            throw new DtoException(self::UNAVAILABLE, ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $appSecret;
    }

    /**
     * Get the Graph API version, refusing anything that is not `vN.N`
     *
     * @return string The version, e.g. `v23.0`
     *
     * @throws DtoException When the configured version is malformed
     */
    private function getGraphApiVersion(): string
    {
        $version = $this->graphApiVersion !== '' ? $this->graphApiVersion : (string) getenv('FACEBOOK_GRAPH_API_VERSION');
        if ($version === '') {
            return self::DEFAULT_GRAPH_API_VERSION;
        }

        if (preg_match('/^v\d{1,3}\.\d{1,3}$/', $version) !== 1) {
            $this->logger->error('FACEBOOK_GRAPH_API_VERSION is malformed', ['version' => substr($version, 0, 16)]);

            throw new DtoException(self::UNAVAILABLE, ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $version;
    }

    /**
     * Base64url-encode without padding (RFC 7636)
     *
     * @param string $bytes Raw bytes
     *
     * @return string The encoding
     */
    private function base64UrlEncode(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
