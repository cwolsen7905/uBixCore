<?php

declare(strict_types=1);

namespace Ubix\Service\Identity;

use Ubix\DataTransferObject\Identity\BeginAuthorizationRequest;
use Ubix\DataTransferObject\Identity\CallbackParameters;
use Ubix\DataTransferObject\Identity\PendingAuthorization;
use Ubix\DataTransferObject\Identity\VerifiedIdentity;
use Ubix\Enum\Identity\IdentityProvider;
use Ubix\Exception\DtoException;

/**
 * Interface for a vendor-agnostic external identity provider ("Continue with ...")
 *
 * A host binds one implementation per provider it enables and depends on this
 * interface only. The implementation is the only code that knows the provider's
 * endpoints, token format or quirks; the host never parses a JWT or calls a
 * provider API itself.
 *
 * The seam is deliberately mechanism-only. It answers "this browser just proved
 * it holds provider account X, and the provider says these things about it". It
 * does **not** answer "which of our accounts is that" -- signing in, signing up
 * and linking are host policy, and a framework that guessed would guess wrong
 * for somebody.
 *
 * Flow:
 * 1. `beginAuthorization()` -> store the result with {@see AuthorizationFlowService}
 *    and redirect the browser to its `authorizationUrl`;
 * 2. on the callback, {@see AuthorizationFlowService::takeForCallback()} returns
 *    the stored flow only if the browser's flow cookie and the returned `state`
 *    both match;
 * 3. `completeAuthorization()` exchanges the code and verifies what comes back.
 *
 * @see \Ubix\Tests\Service\Identity\GoogleIdentityProviderServiceTest PHPUnit test case of the first implementation
 */
interface IdentityProviderServiceInterface
{
    /**
     * Get the provider this implementation talks to
     *
     * @return IdentityProvider The provider
     */
    public function getProvider(): IdentityProvider;

    /**
     * Begin a sign-in: generate `state`, `nonce` and a PKCE verifier and build the provider URL
     *
     * @param BeginAuthorizationRequest $request Where to call back, where to return, and opaque host context
     *
     * @return PendingAuthorization The flow to store server-side, carrying the URL to redirect to
     */
    public function beginAuthorization(BeginAuthorizationRequest $request): PendingAuthorization;

    /**
     * Complete a sign-in: exchange the code and verify the provider's answer
     *
     * Call only with a flow obtained from {@see AuthorizationFlowService::takeForCallback()},
     * which is what ties the callback to the browser that started it.
     *
     * @param CallbackParameters   $parameters What the provider sent back
     * @param PendingAuthorization $pending    The stored flow this callback belongs to
     *
     * @return VerifiedIdentity The verified identity
     *
     * @throws DtoException When the user declined, or the exchange or verification failed; the message is safe to show
     */
    public function completeAuthorization(CallbackParameters $parameters, PendingAuthorization $pending): VerifiedIdentity;
}
