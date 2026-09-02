<?php

declare(strict_types=1);

namespace Ubix\Enum\User;

/**
 * The roles a registrant may select at sign-up (registration TDS §3; admin is never payload-assignable)
 *
 * @see \Ubix\Tests\Enum\User\RegistrationRoleTest PHPUnit test case
 */
enum RegistrationRole: string
{
    case SUPPORTER = 'supporter';
    case CREATOR   = 'creator';
}
