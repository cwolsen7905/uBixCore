<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Identity;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for starting a sign-in with an external identity provider
 *
 * Passed to {@see \Ubix\Service\Identity\IdentityProviderServiceInterface::beginAuthorization()}.
 *
 * `hostContext` exists because the host's own session is not reliably visible
 * when the provider sends the browser back: Sign in with Apple returns by a
 * cross-site POST, which a `SameSite=Lax` session cookie does not ride. Anything
 * the host needs on return -- "this flow connects a provider to signed-in user
 * 42" -- is put here, stored server-side with the flow, and handed back
 * verbatim. The framework never parses it and never sends it to the provider,
 * which is why a host can trust it: it never left the server.
 *
 * @see \Ubix\Tests\DataTransferObject\Identity\BeginAuthorizationRequestTest PHPUnit test case
 */
final readonly class BeginAuthorizationRequest implements Dto
{
    /**
     * Constructor
     *
     * @param string $redirectUri The callback URL registered with the provider, taken from configuration and never derived from the request host
     * @param string $returnTo    Where the host sends the browser after a completed sign-in; validate it first with {@see \Ubix\Service\Identity\ReturnToValidatorService}
     * @param string $hostContext Opaque host state stored with the flow and returned verbatim on the callback (at most 1 KiB)
     * @param string $loginHint   An email address to pre-select at the provider, or empty
     */
    public function __construct(
        public readonly string $redirectUri = '',
        public readonly string $returnTo = '',
        public readonly string $hostContext = '',
        public readonly string $loginHint = '',
    ) {
    }
}
