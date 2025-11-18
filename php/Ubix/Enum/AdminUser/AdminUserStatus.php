<?php

declare(strict_types=1);

namespace Ubix\Enum\AdminUser;

/**
 * Enumeration of admin user statuses
 *
 * @see \Ubix\Tests\Enum\AdminUser\AdminUserStatusTest PHPUnit test case
 */
enum AdminUserStatus: int
{
    case ACTIVE   = 1;
    case INACTIVE = 0;
}
