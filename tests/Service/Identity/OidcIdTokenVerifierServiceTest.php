<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Identity;

use DateTime;
use Firebase\JWT\JWT;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Identity\OidcIdTokenVerifierService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Identity\OidcIdTokenVerifierService
 *
 * One passing token, then one refusal per check -- each negative case changes
 * exactly one thing about an otherwise valid token, so a check that silently
 * stops working fails exactly one test.
 *
 * @coversDefaultClass \Ubix\Service\Identity\OidcIdTokenVerifierService
 */
final class OidcIdTokenVerifierServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const AUDIENCE = 'client-1';
    private const ISSUER   = 'https://idp.test';
    private const JWKS_URI = 'https://idp.test/jwks';
    private const NONCE    = 'nonce-1';

    private IdentityFixture $fixture;

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(OidcIdTokenVerifierService::class);
    }

    /**
     * Test that a valid token's claims come back
     *
     * @return void
     */
    public function testValidTokenReturnsClaims(): void
    {
        $claims = $this->verify($this->fixture->sign($this->claims()));

        $this->assertSame('subject-1', $claims['sub']);
        $this->assertSame('person@example.com', $claims['email']);
    }

    /**
     * Test that a token minted for another app is refused
     *
     * @return void
     */
    public function testWrongAudienceIsRefused(): void
    {
        $this->expectRefusal();
        $this->verify($this->fixture->sign($this->claims(['aud' => 'someone-elses-client'])));
    }

    /**
     * Test that a token from another issuer is refused
     *
     * @return void
     */
    public function testWrongIssuerIsRefused(): void
    {
        $this->expectRefusal();
        $this->verify($this->fixture->sign($this->claims(['iss' => 'https://evil.test'])));
    }

    /**
     * Test that an expired token is refused (beyond the 60 s leeway)
     *
     * @return void
     */
    public function testExpiredTokenIsRefused(): void
    {
        $this->expectRefusal();
        $this->verify($this->fixture->sign($this->claims(['exp' => $this->now() - 600, 'iat' => $this->now() - 1200])));
    }

    /**
     * Test that a token without `exp` is refused
     *
     * @return void
     */
    public function testTokenWithoutExpiryIsRefused(): void
    {
        $claims = $this->claims();
        unset($claims['exp']);

        $this->expectRefusal();
        $this->verify($this->fixture->sign($claims));
    }

    /**
     * Test that a token issued for another sign-in (different nonce) is refused
     *
     * @return void
     */
    public function testNonceMismatchIsRefused(): void
    {
        $this->expectRefusal();
        $this->verify($this->fixture->sign($this->claims(['nonce' => 'nonce-of-another-flow'])));
    }

    /**
     * Test that an unsigned (`alg: none`) token is refused
     *
     * @return void
     */
    public function testUnsignedTokenIsRefused(): void
    {
        $header  = rtrim(strtr(base64_encode('{"alg":"none","typ":"JWT"}'), '+/', '-_'), '=');
        $payload = rtrim(strtr(base64_encode($this->fixture->getJsonService()->encode($this->claims())), '+/', '-_'), '=');

        $this->expectRefusal();
        $this->verify($header . '.' . $payload . '.');
    }

    /**
     * Test that an HMAC-signed token is refused before any key is looked up
     *
     * @return void
     */
    public function testHmacTokenIsRefused(): void
    {
        $this->expectRefusal();
        $this->verify(JWT::encode($this->claims(), str_repeat('k', 64), 'HS256', IdentityFixture::KEY_ID));
    }

    /**
     * Test that a tampered payload fails the signature check
     *
     * @return void
     */
    public function testTamperedTokenIsRefused(): void
    {
        [$header, , $signature] = explode('.', $this->fixture->sign($this->claims()));
        $forged                 = rtrim(strtr(base64_encode($this->fixture->getJsonService()->encode($this->claims(['sub' => 'someone-else']))), '+/', '-_'), '=');

        $this->expectRefusal();
        $this->verify($header . '.' . $forged . '.' . $signature);
    }

    /**
     * Test that a token signed by a key the provider does not publish is refused
     *
     * @return void
     */
    public function testUnknownKeyIsRefused(): void
    {
        $this->expectRefusal();
        $this->verify((new IdentityFixture())->sign($this->claims()));
    }

    /**
     * Test that a multi-audience token must name this app as its authorised party
     *
     * @return void
     */
    public function testAudienceListRequiresAuthorisedParty(): void
    {
        $this->assertSame('subject-1', $this->verify($this->fixture->sign($this->claims(['aud' => [self::AUDIENCE, 'other'], 'azp' => self::AUDIENCE])))['sub']);

        $this->expectRefusal();
        $this->verify($this->fixture->sign($this->claims(['aud' => [self::AUDIENCE, 'other'], 'azp' => 'other'])));
    }

    /**
     * Serve the fixture's JWKS
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->fixture = new IdentityFixture();
        $this->fixture->addRoute(self::JWKS_URI, 200, $this->fixture->getJwks());
    }

    /**
     * Get the current Unix time
     *
     * @return int Seconds since the epoch
     */
    private function now(): int
    {
        return (new DateTime())->getTimestamp();
    }

    /**
     * Build valid claims, with overrides
     *
     * @param array<string, mixed> $overrides Claims to replace
     *
     * @return array<string, mixed> The claims
     */
    private function claims(array $overrides = []): array
    {
        return array_merge([
            'aud'   => self::AUDIENCE,
            'email' => 'person@example.com',
            'exp'   => $this->now() + 300,
            'iat'   => $this->now(),
            'iss'   => self::ISSUER,
            'nonce' => self::NONCE,
            'sub'   => 'subject-1',
        ], $overrides);
    }

    /**
     * Verify a token against the fixture's issuer, audience and nonce
     *
     * @param string $token The token
     *
     * @return array<string, mixed> The claims
     */
    private function verify(string $token): array
    {
        return $this->fixture->getVerifier()->verifyIdToken($token, self::JWKS_URI, [self::ISSUER], self::AUDIENCE, self::NONCE);
    }

    /**
     * Expect the one refusal every failed check produces
     *
     * @return void
     */
    private function expectRefusal(): void
    {
        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_TOKEN_INVALID->value);
    }
}
