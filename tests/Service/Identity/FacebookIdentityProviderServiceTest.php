<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Identity;

use DateTime;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Ubix\DataTransferObject\Identity\BeginAuthorizationRequest;
use Ubix\DataTransferObject\Identity\CallbackParameters;
use Ubix\DataTransferObject\Identity\PendingAuthorization;
use Ubix\DataTransferObject\Identity\VerifiedIdentity;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Identity\IdentityProvider;
use Ubix\Exception\DtoException;
use Ubix\Service\Identity\AuthorizationFlowService;
use Ubix\Service\Identity\FacebookIdentityProviderService;
use Ubix\Service\JsonService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Identity\FacebookIdentityProviderService
 *
 * The Graph API is played by {@see IdentityFixture}'s route table: the code
 * exchange, `debug_token` and `/me` each answer from a canned response, and
 * every request sent is recorded so the `appsecret_proof` can be checked.
 *
 * @coversDefaultClass \Ubix\Service\Identity\FacebookIdentityProviderService
 */
final class FacebookIdentityProviderServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const APP_ID       = '1234567890';
    private const APP_SECRET   = 'app-secret-1';
    private const GRAPH        = 'https://graph.facebook.com/v23.0';
    private const REDIRECT_URI = 'https://api.example.com/auth/federated/facebook/callback';
    private const USER_ID      = '10158000000000001';
    private const USER_TOKEN   = 'EAAB-user-token';

    private IdentityFixture $fixture;

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FacebookIdentityProviderService::class);
    }

    /**
     * Test the login-dialog URL: versioned, PKCE S256, fresh state, our redirect URI, email scope
     *
     * @return void
     */
    public function testBeginAuthorizationBuildsTheDialogUrl(): void
    {
        $pending = $this->service()->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI, '/back', 'ctx', 'hint@example.com'));

        $this->assertSame(IdentityProvider::FACEBOOK, $pending->provider);
        $this->assertStringStartsWith('https://www.facebook.com/v23.0/dialog/oauth?', $pending->authorizationUrl);
        parse_str((string) parse_url($pending->authorizationUrl, PHP_URL_QUERY), $query);

        $this->assertSame(self::APP_ID, $query['client_id']);
        $this->assertSame(self::REDIRECT_URI, $query['redirect_uri']);
        $this->assertSame('code', $query['response_type']);
        $this->assertSame('email,public_profile', $query['scope']);
        $this->assertSame('S256', $query['code_challenge_method']);
        $this->assertSame(rtrim(strtr(base64_encode(hash('sha256', $pending->codeVerifier, true)), '+/', '-_'), '='), $query['code_challenge']);
        $this->assertSame($pending->state, $query['state']);
        $this->assertArrayNotHasKey('login_hint', $query);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $pending->state);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $pending->nonce);
        $this->assertSame('ctx', $pending->hostContext);
        $this->assertSame('/back', $pending->returnTo);

        $second = $this->service()->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI));
        $this->assertNotSame($pending->state, $second->state);
        $this->assertNotSame($pending->codeVerifier, $second->codeVerifier);
    }

    /**
     * Test that a configured Graph API version is used, and a malformed one fails closed
     *
     * @return void
     */
    public function testGraphApiVersionIsConfigurableAndValidated(): void
    {
        $pending = $this->service(graphApiVersion: 'v24.0')->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI));
        $this->assertStringStartsWith('https://www.facebook.com/v24.0/dialog/oauth?', $pending->authorizationUrl);

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        $this->service(graphApiVersion: '../evil')->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI));
    }

    /**
     * Test the whole return trip: exchange with the verifier, debug_token as our app, then /me -- each with appsecret_proof
     *
     * @return void
     */
    public function testCompleteAuthorizationReturnsTheVerifiedIdentity(): void
    {
        $pending = $this->begin();
        $this->serveGraph();

        $identity = $this->complete($pending);

        $this->assertSame(IdentityProvider::FACEBOOK, $identity->provider);
        $this->assertSame(self::USER_ID, $identity->subject);
        $this->assertSame('person@example.com', $identity->email);
        $this->assertFalse($identity->emailVerified, 'Facebook asserts nothing about email verification');
        $this->assertFalse($identity->emailIsPrivateRelay);
        $this->assertSame('Grace', $identity->givenName);
        $this->assertSame('Hopper', $identity->familyName);
        $this->assertSame('', $identity->hostedDomain);

        [$exchange, $debug, $me] = $this->fixture->getSentRequests();

        $this->assertSame('GET', $exchange->getMethod());
        $this->assertStringStartsWith(self::GRAPH . '/oauth/access_token?', (string) $exchange->getUri());
        $form = $this->query($exchange);
        $this->assertSame('auth-code', $form['code']);
        $this->assertSame($pending->codeVerifier, $form['code_verifier']);
        $this->assertSame(self::REDIRECT_URI, $form['redirect_uri']);
        $this->assertSame(self::APP_ID, $form['client_id']);
        $this->assertSame(self::APP_SECRET, $form['client_secret']);

        $appToken = self::APP_ID . '|' . self::APP_SECRET;
        $this->assertStringStartsWith(self::GRAPH . '/debug_token?', (string) $debug->getUri());
        $this->assertSame(self::USER_TOKEN, $this->query($debug)['input_token']);
        $this->assertSame('Bearer ' . $appToken, $debug->getHeaderLine('Authorization'));
        $this->assertSame(hash_hmac('sha256', $appToken, self::APP_SECRET), $this->query($debug)['appsecret_proof']);

        $this->assertStringStartsWith(self::GRAPH . '/me?', (string) $me->getUri());
        $this->assertSame('id,email,first_name,last_name', $this->query($me)['fields']);
        $this->assertSame('Bearer ' . self::USER_TOKEN, $me->getHeaderLine('Authorization'));
        $this->assertSame(hash_hmac('sha256', self::USER_TOKEN, self::APP_SECRET), $this->query($me)['appsecret_proof']);
        $this->assertStringNotContainsString(self::USER_TOKEN, (string) $me->getUri(), 'The user token stays out of the URL');
    }

    /**
     * Test that a person who shared no email comes back with an empty, unverified email -- not a failure
     *
     * @return void
     */
    public function testMissingEmailIsReportedAsEmpty(): void
    {
        $pending = $this->begin();
        $this->serveGraph(profile: ['id' => self::USER_ID, 'first_name' => 'Grace']);

        $identity = $this->complete($pending);

        $this->assertSame(self::USER_ID, $identity->subject);
        $this->assertSame('', $identity->email);
        $this->assertFalse($identity->emailVerified);
        $this->assertSame('', $identity->familyName);
    }

    /**
     * Test that a token issued to another Facebook app is refused before the profile is read
     *
     * @return void
     */
    public function testTokenForAnotherAppIsRefused(): void
    {
        $this->assertTokenRefused(['app_id' => '9999999999']);
    }

    /**
     * Test that a token Facebook reports invalid is refused
     *
     * @return void
     */
    public function testInvalidTokenIsRefused(): void
    {
        $this->assertTokenRefused(['is_valid' => false]);
    }

    /**
     * Test that an expired token is refused
     *
     * @return void
     */
    public function testExpiredTokenIsRefused(): void
    {
        $this->assertTokenRefused(['expires_at' => (new DateTime())->getTimestamp() - 1]);
    }

    /**
     * Test that a token with no expiry (0) is refused rather than trusted forever
     *
     * @return void
     */
    public function testNeverExpiringTokenIsRefused(): void
    {
        $this->assertTokenRefused(['expires_at' => 0]);
    }

    /**
     * Test that an app or page token is refused: only a user token proves a person
     *
     * @return void
     */
    public function testNonUserTokenIsRefused(): void
    {
        $this->assertTokenRefused(['type' => 'PAGE']);
    }

    /**
     * Test that a profile whose id is not the inspected token's user is refused
     *
     * @return void
     */
    public function testProfileForAnotherUserIsRefused(): void
    {
        $pending = $this->begin();
        $this->serveGraph(profile: ['id' => '10158000000000002', 'email' => 'other@example.com']);

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        $this->complete($pending);
    }

    /**
     * Test that a debug_token error answer fails as a provider error
     *
     * @return void
     */
    public function testGraphErrorFails(): void
    {
        $pending = $this->begin();
        $this->serveGraph();
        $this->fixture->addRoute(self::GRAPH . '/debug_token', 400, '{"error":{"message":"Invalid OAuth access token.","type":"OAuthException","code":190}}');

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        $this->complete($pending);
    }

    /**
     * Test that a refused code exchange fails as a provider error
     *
     * @return void
     */
    public function testRefusedExchangeFails(): void
    {
        $pending = $this->begin();
        $this->fixture->addRoute(self::GRAPH . '/oauth/access_token', 400, '{"error":{"message":"This authorization code has been used.","type":"OAuthException","code":100}}');

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        $this->complete($pending);
    }

    /**
     * Test that a user who cancels at Facebook gets a denial, and nothing is sent
     *
     * @return void
     */
    public function testCancelledSignInIsDenied(): void
    {
        $pending = $this->begin();

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_AUTHORIZATION_DENIED->value);

        try {
            $this->service()->completeAuthorization(new CallbackParameters(state: $pending->state, error: 'access_denied'), $pending);
        } finally {
            $this->assertSame([], $this->fixture->getSentRequests());
        }
    }

    /**
     * Test that a forged `state` on a Facebook callback is refused by the flow, before Facebook is called
     *
     * @return void
     */
    public function testBadStateIsRefusedBeforeAnyGraphCall(): void
    {
        $flows  = new AuthorizationFlowService(new NullLogger(), new Psr16Cache(new ArrayAdapter()));
        $handle = $flows->store($this->begin());

        try {
            $flows->takeForCallback($handle, new CallbackParameters(code: 'auth-code', state: 'forged'));
            $this->fail('A forged state was accepted');
        } catch (DtoException $e) {
            $this->assertSame(ExceptionCode::IDENTITY_FLOW_INVALID->value, $e->getCode());
        }

        $this->assertSame([], $this->fixture->getSentRequests());
    }

    /**
     * Test that a replayed Facebook callback finds no flow: the handle is single-use
     *
     * @return void
     */
    public function testReplayedFlowHandleIsRefused(): void
    {
        $flows      = new AuthorizationFlowService(new NullLogger(), new Psr16Cache(new ArrayAdapter()));
        $pending    = $this->begin();
        $handle     = $flows->store($pending);
        $parameters = new CallbackParameters(code: 'auth-code', state: $pending->state);
        $this->serveGraph();

        $this->assertSame(self::USER_ID, $this->service()->completeAuthorization($parameters, $flows->takeForCallback($handle, $parameters))->subject);

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_FLOW_INVALID->value);
        $flows->takeForCallback($handle, $parameters);
    }

    /**
     * Test that a flow begun with another provider is refused
     *
     * @return void
     */
    public function testFlowForAnotherProviderIsRefused(): void
    {
        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_FLOW_INVALID->value);
        $this->service()->completeAuthorization(new CallbackParameters(code: 'c', state: 's'), new PendingAuthorization(provider: IdentityProvider::GOOGLE, state: 's'));
    }

    /**
     * Test that with no credentials configured the provider fails closed
     *
     * @return void
     */
    public function testMissingCredentialsFailClosed(): void
    {
        putenv('FACEBOOK_APP_ID');

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        $this->service('', '')->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI));
    }

    /**
     * A fresh Graph stand-in per test
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->fixture = new IdentityFixture();
    }

    /**
     * Assert that a debug_token answer with these overrides is refused, and /me is never called
     *
     * @param array<string, mixed> $overrides Fields of debug_token's `data` to replace
     *
     * @return void
     */
    private function assertTokenRefused(array $overrides): void
    {
        $pending = $this->begin();
        $this->serveGraph(debug: $overrides);

        try {
            $this->complete($pending);
            $this->fail('A token that should be refused was accepted');
        } catch (DtoException $e) {
            $this->assertSame(ExceptionCode::IDENTITY_TOKEN_INVALID->value, $e->getCode());
        }

        foreach ($this->fixture->getSentRequests() as $sent) {
            $this->assertStringStartsNotWith(self::GRAPH . '/me', (string) $sent->getUri(), 'The profile was read before the token was trusted');
        }
    }

    /**
     * Build the service under test
     *
     * @param string $appId           Meta app id
     * @param string $appSecret       Meta app secret
     * @param string $graphApiVersion Graph API version
     *
     * @return FacebookIdentityProviderService The service
     */
    private function service(string $appId = self::APP_ID, string $appSecret = self::APP_SECRET, string $graphApiVersion = ''): FacebookIdentityProviderService
    {
        return new FacebookIdentityProviderService(
            new NullLogger(),
            $this->fixture,
            new Psr17Factory(),
            new JsonService(new NullLogger()),
            $appId,
            $appSecret,
            $graphApiVersion,
        );
    }

    /**
     * Begin a flow
     *
     * @return PendingAuthorization The flow
     */
    private function begin(): PendingAuthorization
    {
        return $this->service()->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI, '/back'));
    }

    /**
     * Complete a flow with a good code and its own state
     *
     * @param PendingAuthorization $pending The flow
     *
     * @return VerifiedIdentity The identity
     */
    private function complete(PendingAuthorization $pending): VerifiedIdentity
    {
        return $this->service()->completeAuthorization(new CallbackParameters(code: 'auth-code', state: $pending->state), $pending);
    }

    /**
     * Make the Graph API answer the code exchange, debug_token and /me
     *
     * @param array<string, mixed>      $debug   Fields of debug_token's `data` to replace
     * @param array<string, mixed>|null $profile The /me answer, or null for the default
     *
     * @return void
     */
    private function serveGraph(array $debug = [], ?array $profile = null): void
    {
        $json = $this->fixture->getJsonService();

        $this->fixture->addRoute(self::GRAPH . '/oauth/access_token', 200, $json->encode([
            'access_token' => self::USER_TOKEN,
            'expires_in'   => 5183944,
            'token_type'   => 'bearer',
        ]));
        $this->fixture->addRoute(self::GRAPH . '/debug_token', 200, $json->encode(['data' => array_merge([
            'application' => 'Test App',
            'app_id'      => self::APP_ID,
            'expires_at'  => (new DateTime())->getTimestamp() + 5183944,
            'is_valid'    => true,
            'scopes'      => ['email', 'public_profile'],
            'type'        => 'USER',
            'user_id'     => self::USER_ID,
        ], $debug),
        ]));
        $this->fixture->addRoute(self::GRAPH . '/me', 200, $json->encode($profile ?? [
            'email'      => 'person@example.com',
            'first_name' => 'Grace',
            'id'         => self::USER_ID,
            'last_name'  => 'Hopper',
        ]));
    }

    /**
     * Parse a request's query string
     *
     * @param Request $request The request
     *
     * @return array<int|string, mixed> The query parameters
     */
    private function query(Request $request): array
    {
        parse_str($request->getUri()->getQuery(), $query);

        return $query;
    }
}
