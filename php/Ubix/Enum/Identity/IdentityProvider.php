<?php

declare(strict_types=1);

namespace Ubix\Enum\Identity;

/**
 * Enumeration of the external identity providers a host can accept sign-in from
 *
 * Provider names are protocol vocabulary, not product vocabulary: any product
 * offering "Continue with Google" means the same Google. The backing value is
 * what a host stores next to the provider's subject id, so it never changes.
 *
 * @see \Ubix\Tests\Enum\Identity\IdentityProviderTest PHPUnit test case
 */
enum IdentityProvider: string
{
    case APPLE    = 'apple';
    case FACEBOOK = 'facebook';
    case GOOGLE   = 'google';
}
