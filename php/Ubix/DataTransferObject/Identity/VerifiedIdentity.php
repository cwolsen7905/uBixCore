<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Identity;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\Identity\IdentityProvider;

/**
 * Data transfer object for an identity a provider has vouched for, after verification
 *
 * Returned by {@see \Ubix\Service\Identity\IdentityProviderServiceInterface::completeAuthorization()}
 * once the flow, the code exchange and (for OpenID Connect providers) the ID
 * token signature and claims have all been checked. Like the payment seam's
 * `VerifiedWebhookEvent`, it is a separate type so that a host handler taking
 * it cannot be handed claims straight off a request.
 *
 * **Key identities on `provider` + `subject`, never on `email`.** The subject is
 * the provider's permanent id for this person in this app. The email can
 * change, can be an Apple relay address, and can belong to a domain that lapsed
 * and was re-registered by someone else. Deciding which account an identity
 * belongs to is host policy; this DTO only reports facts.
 *
 * @see \Ubix\Tests\DataTransferObject\Identity\VerifiedIdentityTest PHPUnit test case
 */
final readonly class VerifiedIdentity implements Dto
{
    /**
     * Constructor
     *
     * @param IdentityProvider $provider            The provider that vouched for this identity
     * @param string           $subject             The provider's permanent user id (`sub`; Facebook's user id)
     * @param string           $email               The email the provider gave, or empty when it gave none
     * @param bool             $emailVerified       Whether the provider asserts the email is verified; always false for a provider that makes no such assertion
     * @param bool             $emailIsPrivateRelay Whether the email is a provider relay address (Apple "Hide My Email")
     * @param string           $givenName           Given name as the provider reports it, or empty
     * @param string           $familyName          Family name as the provider reports it, or empty
     * @param string           $hostedDomain        Google Workspace domain (`hd`), or empty; lets a host apply Google's guidance on when the email claim is authoritative
     */
    public function __construct(
        public readonly IdentityProvider $provider = IdentityProvider::GOOGLE,
        public readonly string $subject = '',
        public readonly string $email = '',
        public readonly bool $emailVerified = false,
        public readonly bool $emailIsPrivateRelay = false,
        public readonly string $givenName = '',
        public readonly string $familyName = '',
        public readonly string $hostedDomain = '',
    ) {
    }
}
