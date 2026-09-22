<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Identity;

use DateTime;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Log\NullLogger;
use Ubix\DataTransferObject\Identity\BeginAuthorizationRequest;
use Ubix\DataTransferObject\Identity\CallbackParameters;
use Ubix\DataTransferObject\Identity\PendingAuthorization;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Identity\IdentityProvider;
use Ubix\Exception\DtoException;
use Ubix\Service\Identity\GoogleIdentityProviderService;
use Ubix\Service\JsonService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Identity\GoogleIdentityProviderService
 *
 * Google is played by {@see IdentityFixture}: its token endpoint returns an ID
 * token signed with a local key, and its JWKS publishes that key.
 *
 * @coversDefaultClass \Ubix\Service\Identity\GoogleIdentityProviderService
 */
final class GoogleIdentityProviderServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const CLIENT_ID    = 'client-1.apps.googleusercontent.com';
    private const REDIRECT_URI = 'https://api.example.com/auth/federated/google/callback';

    private IdentityFixture $fixture;

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(GoogleIdentityProviderService::class);
    }

    /**
     * Test the authorization URL: PKCE S256, fresh state and nonce, our redirect URI
     *
     * @return void
     */
    public function testBeginAuthorizationBuildsAPkceRequest(): void
    {
        $pending = $this->service()->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI, '/back', 'ctx', 'hint@example.com'));

        $this->assertStringStartsWith('https://accounts.google.com/o/oauth2/v2/auth?', $pending->authorizationUrl);
        parse_str((string) parse_url($pending->authorizationUrl, PHP_URL_QUERY), $query);

        $this->assertSame(self::CLIENT_ID, $query['client_id']);
        $this->assertSame(self::REDIRECT_URI, $query['redirect_uri']);
        $this->assertSame('code', $query['response_type']);
        $this->assertSame('openid email profile', $query['scope']);
        $this->assertSame('S256', $query['code_challenge_method']);
        $this->assertSame(rtrim(strtr(base64_encode(hash('sha256', $pending->codeVerifier, true)), '+/', '-_'), '='), $query['code_challenge']);
        $this->assertSame($pending->state, $query['state']);
        $this->assertSame($pending->nonce, $query['nonce']);
        $this->assertSame('hint@example.com', $query['login_hint']);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $pending->state);
        $this->assertSame('ctx', $pending->hostContext);
        $this->assertSame('/back', $pending->returnTo);

        $second = $this->service()->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI));
        $this->assertNotSame($pending->state, $second->state);
        $this->assertNotSame($pending->nonce, $second->nonce);
        $this->assertNotSame($pending->codeVerifier, $second->codeVerifier);
    }

    /**
     * Test the whole return trip: the code exchange carries the verifier, and the verified identity comes back
     *
     * @return void
     */
    public function testCompleteAuthorizationReturnsTheVerifiedIdentity(): void
    {
        $pending = $this->begin();
        $this->serveToken(['nonce' => $pending->nonce]);

        $identity = $this->service()->completeAuthorization(new CallbackParameters(code: 'auth-code', state: $pending->state), $pending);

        $this->assertSame(IdentityProvider::GOOGLE, $identity->provider);
        $this->assertSame('google-subject-1', $identity->subject);
        $this->assertSame('person@gmail.com', $identity->email);
        $this->assertTrue($identity->emailVerified);
        $this->assertFalse($identity->emailIsPrivateRelay);
        $this->assertSame('Grace', $identity->givenName);
        $this->assertSame('Hopper', $identity->familyName);

        $exchange = $this->fixture->getSentRequests()[0];
        $this->assertSame('POST', $exchange->getMethod());
        parse_str((string) $exchange->getBody(), $form);
        $this->assertSame('auth-code', $form['code']);
        $this->assertSame($pending->codeVerifier, $form['code_verifier']);
        $this->assertSame(self::REDIRECT_URI, $form['redirect_uri']);
        $this->assertSame('secret-1', $form['client_secret']);
        $this->assertSame('authorization_code', $form['grant_type']);
    }

    /**
     * Test that Google's facts are reported as given: unverified stays unverified, `hd` passes through
     *
     * @return void
     */
    public function testUnverifiedEmailAndHostedDomainAreReportedAsGiven(): void
    {
        $pending = $this->begin();
        $this->serveToken(['nonce' => $pending->nonce, 'email' => 'pastor@church.example', 'email_verified' => false, 'hd' => 'church.example']);

        $identity = $this->service()->completeAuthorization(new CallbackParameters(code: 'c', state: $pending->state), $pending);

        $this->assertFalse($identity->emailVerified);
        $this->assertSame('church.example', $identity->hostedDomain);
    }

    /**
     * Test that a user who cancels at Google gets a denial, and nothing is exchanged
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
     * Test that a refused code exchange fails as a provider error
     *
     * @return void
     */
    public function testRefusedExchangeFails(): void
    {
        $pending = $this->begin();
        $this->fixture->addRoute('https://oauth2.googleapis.com/token', 400, '{"error":"invalid_grant"}');

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        $this->service()->completeAuthorization(new CallbackParameters(code: 'c', state: $pending->state), $pending);
    }

    /**
     * Test that an ID token for another Google client is refused even though Google signed it
     *
     * @return void
     */
    public function testTokenForAnotherClientIsRefused(): void
    {
        $pending = $this->begin();
        $this->serveToken(['nonce' => $pending->nonce, 'aud' => 'another-app.apps.googleusercontent.com']);

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        $this->service()->completeAuthorization(new CallbackParameters(code: 'c', state: $pending->state), $pending);
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
        $this->service()->completeAuthorization(new CallbackParameters(code: 'c', state: 's'), new PendingAuthorization(provider: IdentityProvider::APPLE, state: 's'));
    }

    /**
     * Test that with no credentials configured the provider fails closed
     *
     * @return void
     */
    public function testMissingCredentialsFailClosed(): void
    {
        putenv('GOOGLE_OAUTH_CLIENT_ID');

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        $this->service('', '')->beginAuthorization(new BeginAuthorizationRequest(self::REDIRECT_URI));
    }

    /**
     * Serve Google's JWKS from the fixture
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->fixture = new IdentityFixture();
        $this->fixture->addRoute('https://www.googleapis.com/oauth2/v3/certs', 200, $this->fixture->getJwks());
    }

    /**
     * Build the service under test
     *
     * @param string $clientId     OAuth client id
     * @param string $clientSecret OAuth client secret
     *
     * @return GoogleIdentityProviderService The service
     */
    private function service(string $clientId = self::CLIENT_ID, string $clientSecret = 'secret-1'): GoogleIdentityProviderService
    {
        $factory = new Psr17Factory();

        return new GoogleIdentityProviderService(
            new NullLogger(),
            $this->fixture,
            $factory,
            $factory,
            $this->fixture->getVerifier(),
            new JsonService(new NullLogger()),
            $clientId,
            $clientSecret,
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
     * Make the token endpoint answer with a signed ID token
     *
     * @param array<string, mixed> $overrides Claims to replace
     *
     * @return void
     */
    private function serveToken(array $overrides): void
    {
        $token = $this->fixture->sign(array_merge([
            'aud'            => self::CLIENT_ID,
            'email'          => 'person@gmail.com',
            'email_verified' => true,
            'exp'            => (new DateTime())->getTimestamp() + 300,
            'family_name'    => 'Hopper',
            'given_name'     => 'Grace',
            'iat'            => (new DateTime())->getTimestamp(),
            'iss'            => 'https://accounts.google.com',
            'sub'            => 'google-subject-1',
        ], $overrides));

        $this->fixture->addRoute('https://oauth2.googleapis.com/token', 200, $this->fixture->getJsonService()->encode(['id_token' => $token]));
    }
}
