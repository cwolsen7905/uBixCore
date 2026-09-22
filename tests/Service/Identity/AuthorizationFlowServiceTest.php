<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Identity;

use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Ubix\DataTransferObject\Identity\CallbackParameters;
use Ubix\DataTransferObject\Identity\PendingAuthorization;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Identity\IdentityProvider;
use Ubix\Exception\DtoException;
use Ubix\Service\Identity\AuthorizationFlowService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Identity\AuthorizationFlowService
 *
 * @coversDefaultClass \Ubix\Service\Identity\AuthorizationFlowService
 */
final class AuthorizationFlowServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private AuthorizationFlowService $service;

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AuthorizationFlowService::class);
    }

    /**
     * Test that a stored flow comes back intact, including the host's opaque context
     *
     * @return void
     */
    public function testStoredFlowRoundTrips(): void
    {
        $pending = $this->pending();
        $taken   = $this->service->takeForCallback($this->service->store($pending), new CallbackParameters(code: 'c', state: $pending->state));

        $this->assertSame($pending->provider, $taken->provider);
        $this->assertSame($pending->authorizationUrl, $taken->authorizationUrl);
        $this->assertSame($pending->state, $taken->state);
        $this->assertSame($pending->nonce, $taken->nonce);
        $this->assertSame($pending->codeVerifier, $taken->codeVerifier);
        $this->assertSame($pending->redirectUri, $taken->redirectUri);
        $this->assertSame($pending->returnTo, $taken->returnTo);
        $this->assertSame($pending->hostContext, $taken->hostContext);
        $this->assertSame($pending->createdAtUnixTimestamp, $taken->createdAtUnixTimestamp);
    }

    /**
     * Test that a flow can be taken only once, so a replayed callback finds nothing
     *
     * @return void
     */
    public function testFlowIsSingleUse(): void
    {
        $pending    = $this->pending();
        $handle     = $this->service->store($pending);
        $parameters = new CallbackParameters(code: 'c', state: $pending->state);
        $this->service->takeForCallback($handle, $parameters);

        $this->expectFlowInvalid();
        $this->service->takeForCallback($handle, $parameters);
    }

    /**
     * Test that a callback whose state does not match is refused -- and the flow is burned
     *
     * @return void
     */
    public function testStateMismatchIsRefusedAndBurnsTheFlow(): void
    {
        $pending = $this->pending();
        $handle  = $this->service->store($pending);

        try {
            $this->service->takeForCallback($handle, new CallbackParameters(code: 'c', state: 'forged'));
            $this->fail('A forged state was accepted');
        } catch (DtoException $e) {
            $this->assertSame(ExceptionCode::IDENTITY_FLOW_INVALID->value, $e->getCode());
        }

        $this->expectFlowInvalid();
        $this->service->takeForCallback($handle, new CallbackParameters(code: 'c', state: $pending->state));
    }

    /**
     * Test that a callback without the starting browser's cookie is refused
     *
     * @return void
     */
    public function testMissingCookieIsRefused(): void
    {
        $this->expectFlowInvalid();
        $this->service->takeForCallback('', new CallbackParameters(code: 'c', state: 's'));
    }

    /**
     * Test that a malformed cookie never reaches the cache as a key
     *
     * @return void
     */
    public function testMalformedCookieIsRefused(): void
    {
        $this->expectFlowInvalid();
        $this->service->takeForCallback('../../UBIX_SOMETHING_ELSE', new CallbackParameters(code: 'c', state: 's'));
    }

    /**
     * Test that oversized host context is refused rather than stored
     *
     * @return void
     */
    public function testOversizedHostContextIsRefused(): void
    {
        $this->expectFlowInvalid();
        $this->service->store($this->pending(str_repeat('x', 1025)));
    }

    /**
     * Test the cookie's attributes: survives Apple's cross-site POST, unreadable by script, host-only
     *
     * @return void
     */
    public function testCookieHeaderAttributes(): void
    {
        $header = $this->service->getCookieHeader(str_repeat('a', 64));

        $this->assertStringStartsWith('__Host-ubix_authflow=' . str_repeat('a', 64) . ';', $header);
        foreach (['Path=/', 'Max-Age=600', 'Secure', 'HttpOnly', 'SameSite=None'] as $attribute) {
            $this->assertStringContainsString('; ' . $attribute, $header);
        }

        $this->assertStringNotContainsStringIgnoringCase('Domain=', $header);
        $this->assertStringContainsString('Max-Age=0', $this->service->getExpiredCookieHeader());
    }

    /**
     * Fresh in-memory cache per test
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->service = new AuthorizationFlowService(new NullLogger(), new Psr16Cache(new ArrayAdapter()));
    }

    /**
     * Build a pending flow
     *
     * @param string $hostContext Opaque host context
     *
     * @return PendingAuthorization The flow
     */
    private function pending(string $hostContext = '{"intent":"link","userId":42}'): PendingAuthorization
    {
        return new PendingAuthorization(
            provider:               IdentityProvider::APPLE,
            authorizationUrl:       'https://idp.test/auth?x=1',
            state:                  bin2hex(random_bytes(32)),
            nonce:                  bin2hex(random_bytes(32)),
            codeVerifier:           'verifier',
            redirectUri:            'https://api.example.com/auth/federated/apple/callback',
            returnTo:               '/welcome',
            hostContext:            $hostContext,
            createdAtUnixTimestamp: 1700000000,
        );
    }

    /**
     * Expect the flow-invalid refusal
     *
     * @return void
     */
    private function expectFlowInvalid(): void
    {
        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_FLOW_INVALID->value);
    }
}
