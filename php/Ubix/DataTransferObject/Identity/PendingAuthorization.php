<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Identity;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\Identity\IdentityProvider;

/**
 * Data transfer object for a sign-in that has been sent to a provider and not yet returned
 *
 * Returned by {@see \Ubix\Service\Identity\IdentityProviderServiceInterface::beginAuthorization()}.
 * The host redirects the browser to `authorizationUrl` and keeps everything
 * else server-side with {@see \Ubix\Service\Identity\AuthorizationFlowService}.
 *
 * `state`, `nonce` and `codeVerifier` are secrets for the life of the flow: the
 * provider sees only `state`, the nonce and the PKCE challenge derived from the
 * verifier, and a callback is accepted only when what comes back matches what
 * is held here.
 *
 * @see \Ubix\Tests\DataTransferObject\Identity\PendingAuthorizationTest PHPUnit test case
 */
final readonly class PendingAuthorization implements Dto
{
    /**
     * Constructor
     *
     * @param IdentityProvider $provider               The provider this flow was sent to
     * @param string           $authorizationUrl       The provider URL to redirect the browser to
     * @param string           $state                  Random value the provider echoes back; binds the callback to this flow
     * @param string           $nonce                  Random value the provider embeds in its ID token; binds the token to this flow
     * @param string           $codeVerifier           PKCE verifier; only its S256 challenge was sent to the provider
     * @param string           $redirectUri            The callback URL used; the code exchange must repeat it exactly
     * @param string           $returnTo               Where the host sends the browser afterwards (already validated)
     * @param string           $hostContext            Opaque host state, returned verbatim
     * @param int              $createdAtUnixTimestamp When the flow started
     */
    public function __construct(
        public readonly IdentityProvider $provider = IdentityProvider::GOOGLE,
        public readonly string $authorizationUrl = '',
        public readonly string $state = '',
        public readonly string $nonce = '',
        public readonly string $codeVerifier = '',
        public readonly string $redirectUri = '',
        public readonly string $returnTo = '',
        public readonly string $hostContext = '',
        public readonly int $createdAtUnixTimestamp = 0,
    ) {
    }
}
