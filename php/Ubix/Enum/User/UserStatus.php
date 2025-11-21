<?php

declare(strict_types=1);

namespace Ubix\Enum\User;

/**
 * Enum for user status values
 */
enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING = 'pending';
}
