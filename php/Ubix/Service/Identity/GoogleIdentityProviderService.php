<?php

declare(strict_types=1);

namespace Ubix\Service\Identity;

use DateTime;
use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
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
 * "Continue with Google": OpenID Connect authorization-code flow with PKCE (S256)
 *
 * | Env var                      | Meaning                                  | Source |
 * |------------------------------|------------------------------------------|--------|
 * | `GOOGLE_OAUTH_CLIENT_ID`     | The OAuth client id (web application)    | uBixVault in every deployed environment; `.env` only for local dev |
 * | `GOOGLE_OAUTH_CLIENT_SECRET` | Its client secret                        | uBixVault |
 *
 * Constructor arguments override the environment (tests, or a host that wires
 * credentials explicitly). With neither, every call fails closed.
 *
 * `emailVerified` is reported exactly as Google asserts it, and `hd` is passed
 * through as `hostedDomain`: Google documents its email claim as authoritative
 * only for `@gmail.com` addresses or when `hd` matches the email's domain, and
 * whether that matters is the host's decision.
 *
 * @see \Ubix\Tests\Service\Identity\GoogleIdentityProviderServiceTest PHPUnit test case
 */
final class GoogleIdentityProviderService implements IdentityProviderService
{
    private const AUTHORIZATION_ENDPOINT = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const ISSUERS                = ['https://accounts.google.com', 'accounts.google.com'];
    private const JWKS_URI               = 'https://www.googleapis.com/oauth2/v3/certs';
    private const SCOPES                 = 'openid email profile';
    private const TOKEN_ENDPOINT         = 'https://oauth2.googleapis.com/token';

    /**
     * Constructor
     *
     * @param Logger                     $logger          The Monolog logger
     * @param HttpClient                 $httpClient      PSR-18 client for the code exchange
     * @param RequestFactory             $requestFactory  PSR-17 request factory
     * @param StreamFactory              $streamFactory   PSR-17 stream factory
     * @param OidcIdTokenVerifierService $idTokenVerifier Verifies the ID token Google returns
     * @param JsonService                $jsonService     JSON codec
     * @param string                     $clientId        OAuth client id, or empty to read `GOOGLE_OAUTH_CLIENT_ID`
     * @param string                     $clientSecret    OAuth client secret, or empty to read `GOOGLE_OAUTH_CLIENT_SECRET`
     */
    public function __construct(
        private Logger $logger,
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private StreamFactory $streamFactory,
        private OidcIdTokenVerifierService $idTokenVerifier,
        private JsonService $jsonService,
        private string $clientId = '',
        private string $clientSecret = '',
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getProvider(): IdentityProvider
    {
        return IdentityProvider::GOOGLE;
    }

    /**
     * {@inheritDoc}
     *
     * Fails closed (a `DtoException`) when no client id is configured.
     */
    public function beginAuthorization(BeginAuthorizationRequest $request): PendingAuthorization
    {
        $clientId     = $this->getClientId();
        $state        = bin2hex(random_bytes(32));
        $nonce        = bin2hex(random_bytes(32));
        $codeVerifier = $this->base64UrlEncode(random_bytes(32));

        $query = [
            'client_id'             => $clientId,
            'code_challenge'        => $this->base64UrlEncode(hash('sha256', $codeVerifier, true)),
            'code_challenge_method' => 'S256',
            'nonce'                 => $nonce,
            'prompt'                => 'select_account',
            'redirect_uri'          => $request->redirectUri,
            'response_type'         => 'code',
            'scope'                 => self::SCOPES,
            'state'                 => $state,
        ];

        if ($request->loginHint !== '') {
            $query['login_hint'] = $request->loginHint;
        }

        return new PendingAuthorization(
            provider:               IdentityProvider::GOOGLE,
            authorizationUrl:       self::AUTHORIZATION_ENDPOINT . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986),
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
        if ($pending->provider !== IdentityProvider::GOOGLE) {
            throw new DtoException('That sign-in didn\'t complete. Please try again.', ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        if ($parameters->error !== '') {
            $this->logger->info('Google sign-in declined', ['error' => substr($parameters->error, 0, 64)]);

            throw new DtoException('Sign-in with Google was cancelled.', ExceptionCode::IDENTITY_AUTHORIZATION_DENIED->value);
        }

        if ($parameters->code === '') {
            throw new DtoException('That sign-in didn\'t complete. Please try again.', ExceptionCode::IDENTITY_FLOW_INVALID->value);
        }

        $claims = $this->idTokenVerifier->verifyIdToken(
            $this->exchangeCode($parameters->code, $pending),
            self::JWKS_URI,
            self::ISSUERS,
            $this->getClientId(),
            $pending->nonce,
        );

        return new VerifiedIdentity(
            provider:            IdentityProvider::GOOGLE,
            subject:             is_string($claims['sub'] ?? null) ? $claims['sub'] : '',
            email:               is_string($claims['email'] ?? null) ? $claims['email'] : '',
            emailVerified:       ($claims['email_verified'] ?? false) === true || ($claims['email_verified'] ?? '') === 'true',
            emailIsPrivateRelay: false,
            givenName:           is_string($claims['given_name'] ?? null) ? $claims['given_name'] : '',
            familyName:          is_string($claims['family_name'] ?? null) ? $claims['family_name'] : '',
            hostedDomain:        is_string($claims['hd'] ?? null) ? $claims['hd'] : '',
        );
    }

    /**
     * Exchange an authorization code for Google's ID token
     *
     * @param string               $code    The authorization code
     * @param PendingAuthorization $pending The flow, for the redirect URI and PKCE verifier
     *
     * @return string The ID token (not yet verified)
     *
     * @throws DtoException When Google is unreachable or refuses the exchange
     */
    private function exchangeCode(string $code, PendingAuthorization $pending): string
    {
        $body = http_build_query([
            'client_id'     => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'code'          => $code,
            'code_verifier' => $pending->codeVerifier,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $pending->redirectUri,
        ], '', '&', PHP_QUERY_RFC3986);

        $request = $this->requestFactory->createRequest('POST', self::TOKEN_ENDPOINT)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('Accept', 'application/json')
            ->withBody($this->streamFactory->createStream($body));

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientException $e) {
            $this->logger->error('Google token endpoint unreachable', ['reason' => $e->getMessage()]);

            throw new DtoException('Google sign-in is unavailable right now. Please try again.', ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value, null, $e);
        }

        try {
            $decoded = $this->jsonService->decode((string) $response->getBody());
        } catch (Throwable $e) {
            $this->logger->info('Google token response was not JSON', ['reason' => $e->getMessage()]);
            $decoded = [];
        }

        if ($response->getStatusCode() !== 200 || !is_string($decoded['id_token'] ?? null)) {
            $this->logger->warning('Google refused the code exchange', [
                'error'  => is_string($decoded['error'] ?? null) ? $decoded['error'] : '',
                'status' => $response->getStatusCode(),
            ]);

            throw new DtoException('That sign-in didn\'t complete. Please try again.', ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $decoded['id_token'];
    }

    /**
     * Get the OAuth client id, failing closed when none is configured
     *
     * @return string The client id
     *
     * @throws DtoException When none is configured
     */
    private function getClientId(): string
    {
        $clientId = $this->clientId !== '' ? $this->clientId : (string) getenv('GOOGLE_OAUTH_CLIENT_ID');
        if ($clientId === '') {
            throw new DtoException('Google sign-in is not available.', ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $clientId;
    }

    /**
     * Get the OAuth client secret, failing closed when none is configured
     *
     * @return string The client secret
     *
     * @throws DtoException When none is configured
     */
    private function getClientSecret(): string
    {
        $clientSecret = $this->clientSecret !== '' ? $this->clientSecret : (string) getenv('GOOGLE_OAUTH_CLIENT_SECRET');
        if ($clientSecret === '') {
            throw new DtoException('Google sign-in is not available.', ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $clientSecret;
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
