<?php

declare(strict_types=1);

namespace Ubix\Enum\User;

/**
 * Enum for user status values
 *
 * @see \Ubix\Tests\Enum\User\UserStatusTest PHPUnit test case
 */
enum UserStatus: string
{
    case ACTIVE    = 'active';
    case INACTIVE  = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING   = 'pending';
}
